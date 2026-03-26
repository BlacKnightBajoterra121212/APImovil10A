<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BranchInventory;
use App\Models\InventoryMovement;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class inventarioController extends Controller
{
    public function index()
    {
        $inventories = BranchInventory::with(['branch', 'product' => function($q) {
            $q->where('status', 'active');
        }])->orderBy('stock', 'asc')->get();
        
        $branches = Branch::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();
        
        return view('inventario.inventario', compact('inventories', 'branches', 'products'));
    }

    public function ajustarStock(Request $request)
    {
        $request->validate([
            'id_branch' => 'required|exists:branches,id',
            'id_product' => 'required|exists:products,id',
            'movement_type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:150'
        ]);

        try {
            DB::beginTransaction();

            // Verificar que el producto esté activo
            $product = Product::findOrFail($request->id_product);
            if ($product->status !== 'active') {
                throw new \Exception('El producto no está activo');
            }

            // Buscar o crear el inventario de sucursal
            $inventory = BranchInventory::firstOrNew([
                'id_branch' => $request->id_branch,
                'id_product' => $request->id_product
            ]);

            // Actualizar stock
            if ($request->movement_type === 'in') {
                $inventory->stock = ($inventory->stock ?? 0) + $request->quantity;
            } else {
                if (($inventory->stock ?? 0) < $request->quantity) {
                    throw new \Exception('Stock insuficiente para realizar la salida');
                }
                $inventory->stock = ($inventory->stock ?? 0) - $request->quantity;
            }
            $inventory->save();

            // Registrar el movimiento en inventory_movements
            $movement = InventoryMovement::create([
                'id_branch' => $request->id_branch,
                'id_product' => $request->id_product,
                'movement_type' => $request->movement_type,
                'quantity' => $request->quantity,
                'reason' => $request->reason,
                'movement_date' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock ajustado exitosamente',
                'data' => [
                    'inventory' => $inventory->load(['branch', 'product']),
                    'movement' => $movement->load(['branch', 'product'])
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al ajustar stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMovements($branchId = null, $productId = null)
    {
        $movements = InventoryMovement::with(['branch', 'product']);
        
        if ($branchId) {
            $movements->where('id_branch', $branchId);
        }
        if ($productId) {
            $movements->where('id_product', $productId);
        }
        
        $movements = $movements->orderBy('movement_date', 'desc')
            ->limit(50)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $movements
        ]);
    }

    public function filterByBranch(Request $request)
    {
        $branchId = $request->branch_id;
        
        $inventories = BranchInventory::with(['branch', 'product' => function($q) {
            $q->where('status', 'active');
        }])
            ->when($branchId, function($query) use ($branchId) {
                return $query->where('id_branch', $branchId);
            })
            ->orderBy('stock', 'asc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $inventories
        ]);
    }
}
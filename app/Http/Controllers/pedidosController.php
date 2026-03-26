<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class pedidosController extends Controller
{
    public function index()
    {
        $orders = Order::with(['branch', 'client', 'user', 'details.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $branches = Branch::where('status', 'active')->get();
        $clients = Client::all();
        $products = Product::where('status', 'active')->get();

        return view('pedidos.pedidos', compact('orders', 'branches', 'clients', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_branch' => 'required|exists:branches,id',
            'id_client' => 'required|exists:clients,id',
            'products' => 'required|array|min:1',
            'products.*.id_product' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            $details = [];

            foreach ($request->products as $item) {
                $product = Product::find($item['id_product']);
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $details[] = [
                    'id_product' => $item['id_product'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal
                ];
            }

            $order = Order::create([
                'id_company' => auth()->user()->id_company,
                'id_branch' => $request->id_branch,
                'id_client' => $request->id_client,
                'id_user' => auth()->id(),
                'total' => $total,
                'status' => 'pending', // Estado inicial correcto
                'order_date' => now()
            ]);

            foreach ($details as $detail) {
                OrderDetail::create([
                    'id_order' => $order->id,
                    'id_product' => $detail['id_product'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'subtotal' => $detail['subtotal']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'data' => $order->load(['branch', 'client', 'details.product'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // CORREGIDO: Usar los estados correctos de tu tabla
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        try {
            $order = Order::findOrFail($id);
            $order->status = $request->status;
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Estado del pedido actualizado a ' . $request->status,
                'data' => $order->load(['branch', 'client', 'details.product'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with(['branch', 'client', 'user', 'details.product'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function filterByStatus($status = null)
    {
        $orders = Order::with(['branch', 'client', 'details.product'])
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
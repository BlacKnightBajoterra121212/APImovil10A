<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BranchInventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventarioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_branch' => 'nullable|integer|exists:branches,id',
            'id_product' => 'nullable|integer|exists:products,id',
            'id_category' => 'nullable|integer|exists:categories,id',
            'product_status' => 'nullable|in:active,inactive',
            'low_stock' => 'nullable|boolean',
            'stock_max' => 'nullable|integer|min:0',
            'per_page' => 'nullable|integer|min:1|max:100',
        ], [
            'id_branch.exists' => 'La sucursal seleccionada no existe.',
            'id_product.exists' => 'El producto seleccionado no existe.',
            'id_category.exists' => 'La categoría seleccionada no existe.',
            'product_status.in' => 'El estado de producto debe ser active o inactive.',
            'low_stock.boolean' => 'El valor de low_stock debe ser verdadero o falso.',
            'stock_max.integer' => 'El valor de stock_max debe ser un número entero.',
            'stock_max.min' => 'El valor de stock_max no puede ser negativo.',
            'per_page.integer' => 'El valor de per_page debe ser un número entero.',
            'per_page.min' => 'El valor de per_page debe ser al menos 1.',
            'per_page.max' => 'El valor de per_page no puede ser mayor a 100.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $query = BranchInventory::query()
            ->with([
                'branch:id,name',
                'product:id,id_category,name,status',
                'product.category:id,name',
            ])
            ->orderBy('stock', 'asc');

        if ($request->filled('id_branch')) {
            $query->where('id_branch', (int) $request->input('id_branch'));
        }

        if ($request->filled('id_product')) {
            $query->where('id_product', (int) $request->input('id_product'));
        }

        if ($request->filled('stock_max')) {
            $query->where('stock', '<=', (int) $request->input('stock_max'));
        } elseif ($request->boolean('low_stock')) {
            $query->where('stock', '<=', 10);
        }

        if ($request->filled('id_category') || $request->filled('product_status')) {
            $query->whereHas('product', function ($productQuery) use ($request) {
                if ($request->filled('id_category')) {
                    $productQuery->where('id_category', (int) $request->input('id_category'));
                }

                if ($request->filled('product_status')) {
                    $productQuery->where('status', (string) $request->input('product_status'));
                }
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $inventario = $query->paginate($perPage)->appends($request->query());

        return $this->successResponse('Inventario obtenido correctamente.', [
            'items' => collect($inventario->items())->map(fn (BranchInventory $item) => $this->inventoryPayload($item))->values(),
            'pagination' => [
                'current_page' => $inventario->currentPage(),
                'per_page' => $inventario->perPage(),
                'total' => $inventario->total(),
                'last_page' => $inventario->lastPage(),
            ],
            'filters' => [
                'id_branch' => $request->input('id_branch'),
                'id_product' => $request->input('id_product'),
                'id_category' => $request->input('id_category'),
                'product_status' => $request->input('product_status'),
                'low_stock' => $request->input('low_stock'),
                'stock_max' => $request->input('stock_max'),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $inventory = BranchInventory::with([
            'branch:id,name',
            'product:id,id_category,name,status',
            'product.category:id,name',
        ])->find($id);

        if (!$inventory) {
            return $this->errorResponse('Registro de inventario no encontrado.', ['id' => ['El inventario solicitado no existe.']], 404);
        }

        return $this->successResponse('Inventario obtenido correctamente.', [
            'inventario' => $this->inventoryPayload($inventory),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_branch' => 'required|integer|exists:branches,id',
            'id_product' => 'required|integer|exists:products,id',
            'stock' => 'required|integer|min:0',
        ], [
            'id_branch.required' => 'La sucursal es obligatoria.',
            'id_branch.exists' => 'La sucursal seleccionada no existe.',
            'id_product.required' => 'El producto es obligatorio.',
            'id_product.exists' => 'El producto seleccionado no existe.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $this->validateDuplicateInventory($validator, $request);
        });

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $inventory = DB::transaction(function () use ($request) {
            return BranchInventory::create([
                'id_branch' => (int) $request->input('id_branch'),
                'id_product' => (int) $request->input('id_product'),
                'stock' => (int) $request->input('stock'),
            ]);
        });

        $inventory->load([
            'branch:id,name',
            'product:id,id_category,name,status',
            'product.category:id,name',
        ]);

        return $this->successResponse('Registro de inventario creado correctamente.', [
            'inventario' => $this->inventoryPayload($inventory),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $inventory = BranchInventory::find($id);

        if (!$inventory) {
            return $this->errorResponse('Registro de inventario no encontrado.', ['id' => ['El inventario solicitado no existe.']], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_branch' => 'required|integer|exists:branches,id',
            'id_product' => 'required|integer|exists:products,id',
            'stock' => 'required|integer|min:0',
        ], [
            'id_branch.required' => 'La sucursal es obligatoria.',
            'id_branch.exists' => 'La sucursal seleccionada no existe.',
            'id_product.required' => 'El producto es obligatorio.',
            'id_product.exists' => 'El producto seleccionado no existe.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
        ]);

        $validator->after(function ($validator) use ($request, $id) {
            $this->validateDuplicateInventory($validator, $request, $id);
        });

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        DB::transaction(function () use ($request, $inventory) {
            $inventory->id_branch = (int) $request->input('id_branch');
            $inventory->id_product = (int) $request->input('id_product');
            $inventory->stock = (int) $request->input('stock');
            $inventory->save();
        });

        $inventory->load([
            'branch:id,name',
            'product:id,id_category,name,status',
            'product.category:id,name',
        ]);

        return $this->successResponse('Registro de inventario actualizado correctamente.', [
            'inventario' => $this->inventoryPayload($inventory),
        ]);
    }

    public function updateStock(Request $request, int $id): JsonResponse
    {
        $inventory = BranchInventory::find($id);

        if (!$inventory) {
            return $this->errorResponse('Registro de inventario no encontrado.', ['id' => ['El inventario solicitado no existe.']], 404);
        }

        $validator = Validator::make($request->all(), [
            'stock' => 'required|integer|min:0',
        ], [
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $inventory->stock = (int) $request->input('stock');
        $inventory->save();

        $inventory->load([
            'branch:id,name',
            'product:id,id_category,name,status',
            'product.category:id,name',
        ]);

        return $this->successResponse('Stock actualizado correctamente.', [
            'inventario' => $this->inventoryPayload($inventory),
        ]);
    }

    public function storeMovement(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_branch' => 'required|integer|exists:branches,id',
            'id_product' => 'required|integer|exists:products,id',
            'movement_type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:150',
            'movement_date' => 'nullable|date',
        ], [
            'id_branch.required' => 'La sucursal es obligatoria.',
            'id_branch.exists' => 'La sucursal seleccionada no existe.',
            'id_product.required' => 'El producto es obligatorio.',
            'id_product.exists' => 'El producto seleccionado no existe.',
            'movement_type.required' => 'El tipo de movimiento es obligatorio.',
            'movement_type.in' => 'El tipo de movimiento debe ser in o out.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad mínima es 1.',
            'reason.max' => 'La razón no puede tener más de 150 caracteres.',
            'movement_date.date' => 'La fecha de movimiento no es válida.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $result = DB::transaction(function () use ($request) {
            $inventory = BranchInventory::firstOrNew([
                'id_branch' => (int) $request->input('id_branch'),
                'id_product' => (int) $request->input('id_product'),
            ]);

            $currentStock = (int) ($inventory->stock ?? 0);
            $quantity = (int) $request->input('quantity');
            $movementType = (string) $request->input('movement_type');

            if ($movementType === 'out' && $currentStock < $quantity) {
                return [
                    'error' => $this->errorResponse('Stock insuficiente para realizar la salida.', [
                        'quantity' => ['La cantidad de salida supera el stock disponible.'],
                    ], 422),
                ];
            }

            $inventory->stock = $movementType === 'in'
                ? $currentStock + $quantity
                : $currentStock - $quantity;
            $inventory->save();

            $movement = InventoryMovement::create([
                'id_branch' => (int) $request->input('id_branch'),
                'id_product' => (int) $request->input('id_product'),
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'reason' => $request->filled('reason') ? (string) $request->input('reason') : null,
                'movement_date' => $request->filled('movement_date') ? (string) $request->input('movement_date') : now(),
            ]);

            $inventory->load([
                'branch:id,name',
                'product:id,id_category,name,status',
                'product.category:id,name',
            ]);
            $movement->load(['branch:id,name', 'product:id,name']);

            return [
                'inventory' => $inventory,
                'movement' => $movement,
            ];
        });

        if (isset($result['error']) && $result['error'] instanceof JsonResponse) {
            return $result['error'];
        }

        return $this->successResponse('Movimiento registrado correctamente.', [
            'inventario' => $this->inventoryPayload($result['inventory']),
            'movimiento' => $this->movementPayload($result['movement']),
        ], 201);
    }

    public function movements(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_branch' => 'nullable|integer|exists:branches,id',
            'id_product' => 'nullable|integer|exists:products,id',
            'movement_type' => 'nullable|in:in,out',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'per_page' => 'nullable|integer|min:1|max:100',
        ], [
            'id_branch.exists' => 'La sucursal seleccionada no existe.',
            'id_product.exists' => 'El producto seleccionado no existe.',
            'movement_type.in' => 'El tipo de movimiento debe ser in o out.',
            'date_from.date' => 'La fecha inicial no es válida.',
            'date_to.date' => 'La fecha final no es válida.',
            'date_to.after_or_equal' => 'La fecha final debe ser mayor o igual a la fecha inicial.',
            'per_page.integer' => 'El valor de per_page debe ser un número entero.',
            'per_page.min' => 'El valor de per_page debe ser al menos 1.',
            'per_page.max' => 'El valor de per_page no puede ser mayor a 100.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $query = InventoryMovement::query()
            ->with(['branch:id,name', 'product:id,name'])
            ->orderBy('movement_date', 'desc');

        if ($request->filled('id_branch')) {
            $query->where('id_branch', (int) $request->input('id_branch'));
        }

        if ($request->filled('id_product')) {
            $query->where('id_product', (int) $request->input('id_product'));
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', (string) $request->input('movement_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', (string) $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', (string) $request->input('date_to'));
        }

        $perPage = (int) $request->input('per_page', 15);
        $movimientos = $query->paginate($perPage)->appends($request->query());

        return $this->successResponse('Movimientos de inventario obtenidos correctamente.', [
            'items' => collect($movimientos->items())->map(fn (InventoryMovement $movement) => $this->movementPayload($movement))->values(),
            'pagination' => [
                'current_page' => $movimientos->currentPage(),
                'per_page' => $movimientos->perPage(),
                'total' => $movimientos->total(),
                'last_page' => $movimientos->lastPage(),
            ],
            'filters' => [
                'id_branch' => $request->input('id_branch'),
                'id_product' => $request->input('id_product'),
                'movement_type' => $request->input('movement_type'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ],
        ]);
    }

    private function validateDuplicateInventory($validator, Request $request, ?int $ignoreId = null): void
    {
        if (!$request->filled('id_branch') || !$request->filled('id_product')) {
            return;
        }

        $query = BranchInventory::where('id_branch', (int) $request->input('id_branch'))
            ->where('id_product', (int) $request->input('id_product'));

        if (!is_null($ignoreId)) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            $validator->errors()->add('id_product', 'Ya existe un registro de inventario para esta sucursal y producto.');
        }
    }

    private function inventoryPayload(BranchInventory $inventory): array
    {
        return [
            'id' => $inventory->id,
            'id_branch' => $inventory->id_branch,
            'branch_name' => $inventory->branch?->name,
            'id_product' => $inventory->id_product,
            'product_name' => $inventory->product?->name,
            'product_status' => $inventory->product?->status,
            'category_name' => $inventory->product?->category?->name,
            'stock' => (int) $inventory->stock,
        ];
    }

    private function movementPayload(InventoryMovement $movement): array
    {
        return [
            'id' => $movement->id,
            'id_branch' => $movement->id_branch,
            'branch_name' => $movement->branch?->name,
            'id_product' => $movement->id_product,
            'product_name' => $movement->product?->name,
            'movement_type' => $movement->movement_type,
            'quantity' => (int) $movement->quantity,
            'reason' => $movement->reason,
            'movement_date' => optional($movement->movement_date)->format('Y-m-d H:i:s') ?? null,
        ];
    }

    private function successResponse(string $message, array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => (object) [],
        ], $status);
    }

    private function errorResponse(string $message, array $errors = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => (object) [],
            'errors' => $errors,
        ], $status);
    }
}

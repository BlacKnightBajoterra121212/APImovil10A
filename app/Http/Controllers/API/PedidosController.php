<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PedidosController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:pending,completed,cancelled',
            'id_branch' => 'nullable|integer|exists:branches,id',
            'id_client' => 'nullable|integer|exists:clients,id',
            'id_user' => 'nullable|integer|exists:users,id',
            'id_company' => 'nullable|integer|exists:companies,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'search' => 'nullable|string|max:50',
            'per_page' => 'nullable|integer|min:1|max:100',
        ], [
            'status.in' => 'El estado debe ser pending, completed o cancelled.',
            'id_branch.exists' => 'La sucursal seleccionada no existe.',
            'id_client.exists' => 'El cliente seleccionado no existe.',
            'id_user.exists' => 'El usuario seleccionado no existe.',
            'id_company.exists' => 'La empresa seleccionada no existe.',
            'date_from.date' => 'La fecha inicial no es válida.',
            'date_to.date' => 'La fecha final no es válida.',
            'date_to.after_or_equal' => 'La fecha final debe ser mayor o igual a la fecha inicial.',
            'search.max' => 'La búsqueda no puede tener más de 50 caracteres.',
            'per_page.integer' => 'El valor de per_page debe ser un número entero.',
            'per_page.min' => 'El valor de per_page debe ser al menos 1.',
            'per_page.max' => 'El valor de per_page no puede ser mayor a 100.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $query = Order::query()
            ->with([
                'branch:id,name',
                'client:id,name',
                'user:id,name',
                'details:id,id_order,id_product,quantity,unit_price,subtotal',
                'details.product:id,name',
            ])
            ->orderBy('created_at', 'desc');

        foreach (['status', 'id_branch', 'id_client', 'id_user', 'id_company'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', (string) $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', (string) $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where('id', 'LIKE', '%' . $search . '%');
        }

        $perPage = (int) $request->input('per_page', 15);
        $pedidos = $query->paginate($perPage)->appends($request->query());

        return $this->successResponse('Pedidos obtenidos correctamente.', [
            'items' => collect($pedidos->items())->map(fn (Order $order) => $this->orderPayload($order, true))->values(),
            'pagination' => [
                'current_page' => $pedidos->currentPage(),
                'per_page' => $pedidos->perPage(),
                'total' => $pedidos->total(),
                'last_page' => $pedidos->lastPage(),
            ],
            'filters' => [
                'status' => $request->input('status'),
                'id_branch' => $request->input('id_branch'),
                'id_client' => $request->input('id_client'),
                'id_user' => $request->input('id_user'),
                'id_company' => $request->input('id_company'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
                'search' => $request->input('search'),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->findOrderWithRelations($id);

        if (!$order) {
            return $this->errorResponse('Pedido no encontrado.', ['id' => ['El pedido solicitado no existe.']], 404);
        }

        return $this->successResponse('Pedido obtenido correctamente.', [
            'pedido' => $this->orderPayload($order, true),
        ]);
    }

    public function showDetail(int $id): JsonResponse
    {
        $order = $this->findOrderWithRelations($id);

        if (!$order) {
            return $this->errorResponse('Pedido no encontrado.', ['id' => ['El pedido solicitado no existe.']], 404);
        }

        return $this->successResponse('Detalle de pedido obtenido correctamente.', [
            'id' => $order->id,
            'details' => $this->detailPayload($order),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_company' => 'required|integer|exists:companies,id',
            'id_branch' => 'required|integer|exists:branches,id',
            'id_client' => 'required|integer|exists:clients,id',
            'id_user' => 'required|integer|exists:users,id',
            'status' => 'required|in:pending,completed,cancelled',
            'order_date' => 'nullable|date',
            'details' => 'required|array|min:1',
            'details.*.id_product' => 'required|integer|exists:products,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.unit_price' => 'nullable|numeric|min:0',
        ], [
            'id_company.required' => 'La empresa es obligatoria.',
            'id_company.exists' => 'La empresa seleccionada no existe.',
            'id_branch.required' => 'La sucursal es obligatoria.',
            'id_branch.exists' => 'La sucursal seleccionada no existe.',
            'id_client.required' => 'El cliente es obligatorio.',
            'id_client.exists' => 'El cliente seleccionado no existe.',
            'id_user.required' => 'El usuario es obligatorio.',
            'id_user.exists' => 'El usuario seleccionado no existe.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser pending, completed o cancelled.',
            'order_date.date' => 'La fecha del pedido no es válida.',
            'details.required' => 'El detalle del pedido es obligatorio.',
            'details.array' => 'El detalle del pedido debe ser un arreglo.',
            'details.min' => 'El pedido debe tener al menos un producto.',
            'details.*.id_product.required' => 'El producto en cada detalle es obligatorio.',
            'details.*.id_product.exists' => 'Uno de los productos seleccionados no existe.',
            'details.*.quantity.required' => 'La cantidad en cada detalle es obligatoria.',
            'details.*.quantity.integer' => 'La cantidad debe ser un número entero.',
            'details.*.quantity.min' => 'La cantidad mínima por producto es 1.',
            'details.*.unit_price.numeric' => 'El precio unitario debe ser numérico.',
            'details.*.unit_price.min' => 'El precio unitario no puede ser negativo.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $this->validateOrderCompatibility($validator, $request);
        });

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $order = DB::transaction(function () use ($request) {
            $total = 0;
            $detailsToSave = [];

            foreach ($request->input('details', []) as $detail) {
                $product = Product::find((int) $detail['id_product']);

                $unitPrice = array_key_exists('unit_price', $detail) && $detail['unit_price'] !== null
                    ? (float) $detail['unit_price']
                    : (float) $product->price;

                $quantity = (int) $detail['quantity'];
                $subtotal = $unitPrice * $quantity;
                $total += $subtotal;

                $detailsToSave[] = [
                    'id_product' => (int) $detail['id_product'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
            }

            $order = Order::create([
                'id_company' => (int) $request->input('id_company'),
                'id_branch' => (int) $request->input('id_branch'),
                'id_client' => (int) $request->input('id_client'),
                'id_user' => (int) $request->input('id_user'),
                'total' => $total,
                'status' => (string) $request->input('status'),
                'order_date' => $request->filled('order_date') ? (string) $request->input('order_date') : now(),
            ]);

            foreach ($detailsToSave as $detailRow) {
                OrderDetail::create([
                    'id_order' => $order->id,
                    'id_product' => $detailRow['id_product'],
                    'quantity' => $detailRow['quantity'],
                    'unit_price' => $detailRow['unit_price'],
                    'subtotal' => $detailRow['subtotal'],
                ]);
            }

            return $order;
        });

        $order->load([
            'branch:id,name',
            'client:id,name',
            'user:id,name',
            'details:id,id_order,id_product,quantity,unit_price,subtotal',
            'details.product:id,name',
        ]);

        return $this->successResponse('Pedido creado correctamente.', [
            'pedido' => $this->orderPayload($order, true),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $order = Order::with('details')->find($id);

        if (!$order) {
            return $this->errorResponse('Pedido no encontrado.', ['id' => ['El pedido solicitado no existe.']], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_company' => 'required|integer|exists:companies,id',
            'id_branch' => 'required|integer|exists:branches,id',
            'id_client' => 'required|integer|exists:clients,id',
            'id_user' => 'required|integer|exists:users,id',
            'status' => 'required|in:pending,completed,cancelled',
            'order_date' => 'nullable|date',
            'details' => 'nullable|array|min:1',
            'details.*.id_product' => 'required_with:details|integer|exists:products,id',
            'details.*.quantity' => 'required_with:details|integer|min:1',
            'details.*.unit_price' => 'nullable|numeric|min:0',
        ], [
            'id_company.required' => 'La empresa es obligatoria.',
            'id_company.exists' => 'La empresa seleccionada no existe.',
            'id_branch.required' => 'La sucursal es obligatoria.',
            'id_branch.exists' => 'La sucursal seleccionada no existe.',
            'id_client.required' => 'El cliente es obligatorio.',
            'id_client.exists' => 'El cliente seleccionado no existe.',
            'id_user.required' => 'El usuario es obligatorio.',
            'id_user.exists' => 'El usuario seleccionado no existe.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser pending, completed o cancelled.',
            'order_date.date' => 'La fecha del pedido no es válida.',
            'details.array' => 'El detalle del pedido debe ser un arreglo.',
            'details.min' => 'El pedido debe tener al menos un producto.',
            'details.*.id_product.required_with' => 'El producto en cada detalle es obligatorio.',
            'details.*.id_product.exists' => 'Uno de los productos seleccionados no existe.',
            'details.*.quantity.required_with' => 'La cantidad en cada detalle es obligatoria.',
            'details.*.quantity.integer' => 'La cantidad debe ser un número entero.',
            'details.*.quantity.min' => 'La cantidad mínima por producto es 1.',
            'details.*.unit_price.numeric' => 'El precio unitario debe ser numérico.',
            'details.*.unit_price.min' => 'El precio unitario no puede ser negativo.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $this->validateOrderCompatibility($validator, $request);
        });

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        DB::transaction(function () use ($request, $order) {
            $order->id_company = (int) $request->input('id_company');
            $order->id_branch = (int) $request->input('id_branch');
            $order->id_client = (int) $request->input('id_client');
            $order->id_user = (int) $request->input('id_user');
            $order->status = (string) $request->input('status');
            $order->order_date = $request->filled('order_date') ? (string) $request->input('order_date') : $order->order_date;

            if ($request->filled('details')) {
                $total = 0;
                $detailsToSave = [];

                foreach ($request->input('details', []) as $detail) {
                    $product = Product::find((int) $detail['id_product']);
                    $unitPrice = array_key_exists('unit_price', $detail) && $detail['unit_price'] !== null
                        ? (float) $detail['unit_price']
                        : (float) $product->price;
                    $quantity = (int) $detail['quantity'];
                    $subtotal = $unitPrice * $quantity;
                    $total += $subtotal;

                    $detailsToSave[] = [
                        'id_product' => (int) $detail['id_product'],
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ];
                }

                $order->total = $total;
                $order->save();
                $order->details()->delete();

                foreach ($detailsToSave as $detailRow) {
                    OrderDetail::create([
                        'id_order' => $order->id,
                        'id_product' => $detailRow['id_product'],
                        'quantity' => $detailRow['quantity'],
                        'unit_price' => $detailRow['unit_price'],
                        'subtotal' => $detailRow['subtotal'],
                    ]);
                }

                return;
            }

            $order->save();
        });

        $order->load([
            'branch:id,name',
            'client:id,name',
            'user:id,name',
            'details:id,id_order,id_product,quantity,unit_price,subtotal',
            'details.product:id,name',
        ]);

        return $this->successResponse('Pedido actualizado correctamente.', [
            'pedido' => $this->orderPayload($order, true),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->errorResponse('Pedido no encontrado.', ['id' => ['El pedido solicitado no existe.']], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,cancelled',
        ], [
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser pending, completed o cancelled.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $order->status = (string) $request->input('status');
        $order->save();

        $order->load([
            'branch:id,name',
            'client:id,name',
            'user:id,name',
            'details:id,id_order,id_product,quantity,unit_price,subtotal',
            'details.product:id,name',
        ]);

        return $this->successResponse('Estado de pedido actualizado correctamente.', [
            'pedido' => $this->orderPayload($order, true),
        ]);
    }

    private function findOrderWithRelations(int $id): ?Order
    {
        return Order::with([
            'branch:id,name',
            'client:id,name',
            'user:id,name',
            'details:id,id_order,id_product,quantity,unit_price,subtotal',
            'details.product:id,name',
        ])->find($id);
    }

    private function validateOrderCompatibility($validator, Request $request): void
    {
        $companyId = (int) $request->input('id_company');

        $branchId = $request->input('id_branch');
        if ($branchId) {
            $branch = Branch::find((int) $branchId);
            if (!$branch || (int) $branch->id_company !== $companyId) {
                $validator->errors()->add('id_branch', 'La sucursal no pertenece a la empresa seleccionada.');
            }
        }

        $clientId = $request->input('id_client');
        if ($clientId) {
            $client = Client::find((int) $clientId);
            if (!$client || (int) $client->id_company !== $companyId) {
                $validator->errors()->add('id_client', 'El cliente no pertenece a la empresa seleccionada.');
            }
        }

        $userId = $request->input('id_user');
        if ($userId) {
            $user = User::find((int) $userId);
            if (!$user || (int) $user->id_company !== $companyId) {
                $validator->errors()->add('id_user', 'El usuario no pertenece a la empresa seleccionada.');
            }
        }

        foreach ($request->input('details', []) as $index => $detail) {
            if (!isset($detail['id_product'])) {
                continue;
            }

            $product = Product::find((int) $detail['id_product']);

            if (!$product || (int) $product->id_company !== $companyId) {
                $validator->errors()->add("details.$index.id_product", 'El producto no pertenece a la empresa seleccionada.');
            }
        }
    }

    private function orderPayload(Order $order, bool $includeDetails = false): array
    {
        $payload = [
            'id' => $order->id,
            'id_company' => $order->id_company,
            'id_branch' => $order->id_branch,
            'branch_name' => $order->branch?->name,
            'id_client' => $order->id_client,
            'client_name' => $order->client?->name,
            'id_user' => $order->id_user,
            'user_name' => $order->user?->name,
            'total' => (float) $order->total,
            'status' => $order->status,
            'order_date' => optional($order->order_date)->format('Y-m-d H:i:s') ?? null,
        ];

        if ($includeDetails) {
            $payload['details'] = $this->detailPayload($order);
        }

        return $payload;
    }

    private function detailPayload(Order $order): array
    {
        return $order->details->map(function (OrderDetail $detail) {
            return [
                'id_product' => $detail->id_product,
                'product_name' => $detail->product?->name,
                'quantity' => $detail->quantity,
                'unit_price' => (float) $detail->unit_price,
                'subtotal' => (float) $detail->subtotal,
            ];
        })->values()->all();
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

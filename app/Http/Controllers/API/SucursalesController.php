<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SucursalesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:active,inactive',
            'id_company' => 'nullable|integer|exists:companies,id',
            'search' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:100',
        ], [
            'status.in' => 'El estado debe ser active o inactive.',
            'id_company.exists' => 'La empresa seleccionada no existe.',
            'search.max' => 'La búsqueda no puede tener más de 100 caracteres.',
            'per_page.integer' => 'El valor de per_page debe ser un número entero.',
            'per_page.min' => 'El valor de per_page debe ser al menos 1.',
            'per_page.max' => 'El valor de per_page no puede ser mayor a 100.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $query = Branch::query()
            ->with([
                'company:id,name',
                'encargado:id,id_branch,name',
            ])
            ->withCount([
                'activeUsers as empleados_count',
                'inventories as inventario_count',
                'orders as pedidos_hoy_count' => function ($ordersQuery) {
                    $ordersQuery->whereDate('order_date', today());
                },
            ])
            ->orderBy('name', 'asc');

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('id_company')) {
            $query->where('id_company', (int) $request->input('id_company'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $sucursales = $query->paginate($perPage)->appends($request->query());

        $summary = [
            'registradas' => (int) $sucursales->total(),
            'activas' => (int) Branch::where('status', 'active')->when(
                $request->filled('id_company'),
                fn ($subQuery) => $subQuery->where('id_company', (int) $request->input('id_company'))
            )->count(),
            'empleados' => (int) collect($sucursales->items())->sum('empleados_count'),
            'pedidos_hoy' => (int) collect($sucursales->items())->sum('pedidos_hoy_count'),
        ];

        return $this->successResponse('Sucursales obtenidas correctamente.', [
            'items' => collect($sucursales->items())->map(fn (Branch $branch) => $this->branchPayload($branch))->values(),
            'pagination' => [
                'current_page' => $sucursales->currentPage(),
                'per_page' => $sucursales->perPage(),
                'total' => $sucursales->total(),
                'last_page' => $sucursales->lastPage(),
            ],
            'summary' => $summary,
            'filters' => [
                'status' => $request->input('status'),
                'id_company' => $request->input('id_company'),
                'search' => $request->input('search'),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $branch = $this->findBranch($id);

        if (!$branch) {
            return $this->errorResponse('Sucursal no encontrada.', ['id' => ['La sucursal solicitada no existe.']], 404);
        }

        return $this->successResponse('Sucursal obtenida correctamente.', [
            'sucursal' => $this->branchPayload($branch),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_company' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ], [
            'id_company.required' => 'La empresa es obligatoria.',
            'id_company.exists' => 'La empresa seleccionada no existe.',
            'name.required' => 'El nombre de la sucursal es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'address.max' => 'La dirección no puede tener más de 200 caracteres.',
            'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser active o inactive.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $branch = DB::transaction(function () use ($request) {
            return Branch::create([
                'id_company' => (int) $request->input('id_company'),
                'name' => (string) $request->input('name'),
                'address' => $request->filled('address') ? (string) $request->input('address') : null,
                'phone' => $request->filled('phone') ? (string) $request->input('phone') : null,
                'status' => (string) $request->input('status'),
            ]);
        });

        $branch = $this->findBranch($branch->id);

        return $this->successResponse('Sucursal creada correctamente.', [
            'sucursal' => $this->branchPayload($branch),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return $this->errorResponse('Sucursal no encontrada.', ['id' => ['La sucursal solicitada no existe.']], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_company' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ], [
            'id_company.required' => 'La empresa es obligatoria.',
            'id_company.exists' => 'La empresa seleccionada no existe.',
            'name.required' => 'El nombre de la sucursal es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'address.max' => 'La dirección no puede tener más de 200 caracteres.',
            'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser active o inactive.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        DB::transaction(function () use ($request, $branch) {
            $branch->id_company = (int) $request->input('id_company');
            $branch->name = (string) $request->input('name');
            $branch->address = $request->filled('address') ? (string) $request->input('address') : null;
            $branch->phone = $request->filled('phone') ? (string) $request->input('phone') : null;
            $branch->status = (string) $request->input('status');
            $branch->save();
        });

        $branch = $this->findBranch($branch->id);

        return $this->successResponse('Sucursal actualizada correctamente.', [
            'sucursal' => $this->branchPayload($branch),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return $this->errorResponse('Sucursal no encontrada.', ['id' => ['La sucursal solicitada no existe.']], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive',
        ], [
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser active o inactive.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $branch->status = (string) $request->input('status');
        $branch->save();

        $branch = $this->findBranch($branch->id);

        return $this->successResponse('Estado de sucursal actualizado correctamente.', [
            'sucursal' => $this->branchPayload($branch),
        ]);
    }

    public function operation(int $id): JsonResponse
    {
        $branch = $this->findBranch($id);

        if (!$branch) {
            return $this->errorResponse('Sucursal no encontrada.', ['id' => ['La sucursal solicitada no existe.']], 404);
        }

        return $this->successResponse('Resumen operativo obtenido correctamente.', [
            'operacion' => [
                'id' => $branch->id,
                'id_company' => $branch->id_company,
                'company_name' => $branch->company?->name,
                'status' => $branch->status,
                'empleados_count' => (int) $branch->empleados_count,
                'inventario_count' => (int) $branch->inventario_count,
                'pedidos_hoy_count' => (int) $branch->pedidos_hoy_count,
            ],
        ]);
    }

    public function catalog(): JsonResponse
    {
        $companies = Company::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return $this->successResponse('Catálogo de empresas obtenido correctamente.', [
            'companies' => $companies->map(fn (Company $company) => [
                'id' => $company->id,
                'name' => $company->name,
            ])->values(),
        ]);
    }

    private function findBranch(int $id): ?Branch
    {
        return Branch::query()
            ->with([
                'company:id,name',
                'encargado:id,id_branch,name',
            ])
            ->withCount([
                'activeUsers as empleados_count',
                'inventories as inventario_count',
                'orders as pedidos_hoy_count' => function ($ordersQuery) {
                    $ordersQuery->whereDate('order_date', today());
                },
            ])
            ->find($id);
    }

    private function branchPayload(Branch $branch): array
    {
        return [
            'id' => $branch->id,
            'id_company' => $branch->id_company,
            'company_name' => $branch->company?->name,
            'name' => $branch->name,
            'address' => $branch->address,
            'phone' => $branch->phone,
            'status' => $branch->status,
            'encargado_name' => $branch->encargado?->name,
            'empleados_count' => (int) ($branch->empleados_count ?? 0),
            'pedidos_hoy_count' => (int) ($branch->pedidos_hoy_count ?? 0),
            'inventario_count' => (int) ($branch->inventario_count ?? 0),
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

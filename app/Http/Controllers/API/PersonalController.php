<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PersonalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:active,inactive',
            'id_branch' => 'nullable|integer|exists:branches,id',
            'id_role' => 'nullable|integer|exists:roles,id',
            'search' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:100',
        ], [
            'status.in' => 'El estado debe ser active o inactive.',
            'id_branch.exists' => 'La sucursal seleccionada no existe.',
            'id_role.exists' => 'El rol seleccionado no existe.',
            'search.max' => 'La búsqueda no puede tener más de 100 caracteres.',
            'per_page.integer' => 'El valor de per_page debe ser un número entero.',
            'per_page.min' => 'El valor de per_page debe ser al menos 1.',
            'per_page.max' => 'El valor de per_page no puede ser mayor a 100.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $query = User::query()
            ->with(['role:id,name', 'branch:id,name'])
            ->select(['id', 'id_company', 'id_role', 'id_branch', 'name', 'email', 'phone', 'status', 'created_at', 'updated_at'])
            ->orderBy('name', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('id_branch')) {
            $query->where('id_branch', (int) $request->input('id_branch'));
        }

        if ($request->filled('id_role')) {
            $query->where('id_role', (int) $request->input('id_role'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->trim();
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $personal = $query->paginate($perPage)->appends($request->query());

        return $this->successResponse('Personal obtenido correctamente.', [
            'items' => collect($personal->items())->map(fn (User $user) => $this->userPayload($user))->values(),
            'pagination' => [
                'current_page' => $personal->currentPage(),
                'per_page' => $personal->perPage(),
                'total' => $personal->total(),
                'last_page' => $personal->lastPage(),
            ],
            'filters' => [
                'status' => $request->input('status'),
                'id_branch' => $request->input('id_branch'),
                'id_role' => $request->input('id_role'),
                'search' => $request->input('search'),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::with(['role:id,name', 'branch:id,name'])->find($id);

        if (!$user) {
            return $this->errorResponse('Usuario no encontrado.', ['id' => ['El usuario solicitado no existe.']], 404);
        }

        return $this->successResponse('Usuario obtenido correctamente.', [
            'user' => $this->userPayload($user),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'id_company' => 'required|integer|exists:companies,id',
            'id_role' => 'required|integer|exists:roles,id',
            'id_branch' => 'nullable|integer|exists:branches,id',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.max' => 'El correo no puede tener más de 100 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'id_company.required' => 'La empresa es obligatoria.',
            'id_company.exists' => 'La empresa seleccionada no existe.',
            'id_role.required' => 'El rol es obligatorio.',
            'id_role.exists' => 'El rol seleccionado no existe.',
            'id_branch.exists' => 'La sucursal seleccionada no existe.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser active o inactive.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $this->validateBranchCompanyCompatibility(
                $validator,
                $request->input('id_branch'),
                $request->input('id_company')
            );
        });

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $user = DB::transaction(function () use ($request) {
            return User::create([
                'name' => (string) $request->input('name'),
                'email' => (string) $request->input('email'),
                'phone' => $request->filled('phone') ? (string) $request->input('phone') : null,
                'id_company' => (int) $request->input('id_company'),
                'id_role' => (int) $request->input('id_role'),
                'id_branch' => $request->filled('id_branch') ? (int) $request->input('id_branch') : null,
                'status' => (string) $request->input('status'),
                'password' => Hash::make((string) $request->input('password')),
            ]);
        });

        $user->load(['role:id,name', 'branch:id,name']);

        return $this->successResponse('Usuario creado correctamente.', [
            'user' => $this->userPayload($user),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('Usuario no encontrado.', ['id' => ['El usuario solicitado no existe.']], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'id_company' => 'required|integer|exists:companies,id',
            'id_role' => 'required|integer|exists:roles,id',
            'id_branch' => 'nullable|integer|exists:branches,id',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.max' => 'El correo no puede tener más de 100 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'id_company.required' => 'La empresa es obligatoria.',
            'id_company.exists' => 'La empresa seleccionada no existe.',
            'id_role.required' => 'El rol es obligatorio.',
            'id_role.exists' => 'El rol seleccionado no existe.',
            'id_branch.exists' => 'La sucursal seleccionada no existe.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser active o inactive.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $this->validateBranchCompanyCompatibility(
                $validator,
                $request->input('id_branch'),
                $request->input('id_company')
            );
        });

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        DB::transaction(function () use ($request, $user) {
            $user->name = (string) $request->input('name');
            $user->email = (string) $request->input('email');
            $user->phone = $request->filled('phone') ? (string) $request->input('phone') : null;
            $user->id_company = (int) $request->input('id_company');
            $user->id_role = (int) $request->input('id_role');
            $user->id_branch = $request->filled('id_branch') ? (int) $request->input('id_branch') : null;
            $user->status = (string) $request->input('status');

            if ($request->filled('password')) {
                $user->password = Hash::make((string) $request->input('password'));
            }

            $user->save();
        });

        $user->load(['role:id,name', 'branch:id,name']);

        return $this->successResponse('Usuario actualizado correctamente.', [
            'user' => $this->userPayload($user),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $user = User::with(['role:id,name', 'branch:id,name'])->find($id);

        if (!$user) {
            return $this->errorResponse('Usuario no encontrado.', ['id' => ['El usuario solicitado no existe.']], 404);
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

        $user->status = (string) $request->input('status');
        $user->save();

        return $this->successResponse('Estado de usuario actualizado correctamente.', [
            'user' => $this->userPayload($user),
        ]);
    }

    private function validateBranchCompanyCompatibility($validator, $branchId, $companyId): void
    {
        if (empty($branchId) || empty($companyId)) {
            return;
        }

        $branch = Branch::find($branchId);

        if (!$branch || (int) $branch->id_company !== (int) $companyId) {
            $validator->errors()->add('id_branch', 'La sucursal no pertenece a la empresa seleccionada.');
        }
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => $user->status,
            'id_role' => $user->id_role,
            'role_name' => $user->role?->name,
            'id_branch' => $user->id_branch,
            'branch_name' => $user->branch?->name,
            'id_company' => $user->id_company,
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

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive',
            'id_company' => 'nullable|integer|exists:companies,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ], [
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'email.max' => 'El correo no puede tener más de 100 caracteres.',
            'status.in' => 'El estado debe ser active o inactive.',
            'id_company.exists' => 'La empresa seleccionada no existe.',
            'per_page.integer' => 'El valor de per_page debe ser un número entero.',
            'per_page.min' => 'El valor de per_page debe ser al menos 1.',
            'per_page.max' => 'El valor de per_page no puede ser mayor a 100.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $query = Client::query()
            ->select(['id', 'id_company', 'name', 'phone', 'email', 'address', 'status', 'created_at', 'updated_at'])
            ->orderBy('name', 'asc');

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . trim((string) $request->input('name')) . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'LIKE', '%' . trim((string) $request->input('email')) . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('id_company')) {
            $query->where('id_company', (int) $request->input('id_company'));
        }

        $perPage = (int) $request->input('per_page', 15);
        $clientes = $query->paginate($perPage)->appends($request->query());

        return $this->successResponse('Clientes obtenidos correctamente.', [
            'items' => collect($clientes->items())->map(fn (Client $client) => $this->clientPayload($client))->values(),
            'pagination' => [
                'current_page' => $clientes->currentPage(),
                'per_page' => $clientes->perPage(),
                'total' => $clientes->total(),
                'last_page' => $clientes->lastPage(),
            ],
            'filters' => [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'status' => $request->input('status'),
                'id_company' => $request->input('id_company'),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->errorResponse('Cliente no encontrado.', ['id' => ['El cliente solicitado no existe.']], 404);
        }

        return $this->successResponse('Cliente obtenido correctamente.', [
            'cliente' => $this->clientPayload($client),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_company' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:200',
            'status' => 'required|in:active,inactive',
        ], [
            'id_company.required' => 'La empresa es obligatoria.',
            'id_company.exists' => 'La empresa seleccionada no existe.',
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.max' => 'El correo no puede tener más de 100 caracteres.',
            'address.max' => 'La dirección no puede tener más de 200 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser active o inactive.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $client = DB::transaction(function () use ($request) {
            return Client::create([
                'id_company' => (int) $request->input('id_company'),
                'name' => (string) $request->input('name'),
                'phone' => $request->filled('phone') ? (string) $request->input('phone') : null,
                'email' => $request->filled('email') ? (string) $request->input('email') : null,
                'address' => $request->filled('address') ? (string) $request->input('address') : null,
                'status' => (string) $request->input('status'),
            ]);
        });

        return $this->successResponse('Cliente creado correctamente.', [
            'cliente' => $this->clientPayload($client),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->errorResponse('Cliente no encontrado.', ['id' => ['El cliente solicitado no existe.']], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_company' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:200',
            'status' => 'required|in:active,inactive',
        ], [
            'id_company.required' => 'La empresa es obligatoria.',
            'id_company.exists' => 'La empresa seleccionada no existe.',
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.max' => 'El correo no puede tener más de 100 caracteres.',
            'address.max' => 'La dirección no puede tener más de 200 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser active o inactive.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        DB::transaction(function () use ($request, $client) {
            $client->id_company = (int) $request->input('id_company');
            $client->name = (string) $request->input('name');
            $client->phone = $request->filled('phone') ? (string) $request->input('phone') : null;
            $client->email = $request->filled('email') ? (string) $request->input('email') : null;
            $client->address = $request->filled('address') ? (string) $request->input('address') : null;
            $client->status = (string) $request->input('status');
            $client->save();
        });

        return $this->successResponse('Cliente actualizado correctamente.', [
            'cliente' => $this->clientPayload($client),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->errorResponse('Cliente no encontrado.', ['id' => ['El cliente solicitado no existe.']], 404);
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

        $client->status = (string) $request->input('status');
        $client->save();

        return $this->successResponse('Estado de cliente actualizado correctamente.', [
            'cliente' => $this->clientPayload($client),
        ]);
    }

    private function clientPayload(Client $client): array
    {
        return [
            'id' => $client->id,
            'id_company' => $client->id_company,
            'name' => $client->name,
            'phone' => $client->phone,
            'email' => $client->email,
            'address' => $client->address,
            'status' => $client->status,
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

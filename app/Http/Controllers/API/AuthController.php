<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Error de validación.', $validator->errors()->toArray(), 422);
        }

        $credentials = [
            'email' => (string) $request->input('email'),
            'password' => (string) $request->input('password'),
            'status' => 'active',
        ];

        if (!Auth::attempt($credentials, (bool) $request->boolean('remember'))) {
            return $this->errorResponse(
                'Las credenciales no coinciden con nuestros registros o el usuario está inactivo.',
                ['credentials' => ['Credenciales inválidas.']],
                401
            );
        }

        $request->session()->regenerate();

        $user = $request->user();

        return $this->successResponse('Login exitoso.', [
            'user' => $this->userPayload($user),
            'session' => [
                'guard' => 'web',
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->successResponse('Logout exitoso.', []);
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse('Usuario autenticado.', [
            'user' => $this->userPayload($request->user()),
        ]);
    }

    private function userPayload($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'id_role' => $user->id_role,
            'id_company' => $user->id_company,
            'id_branch' => $user->id_branch,
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

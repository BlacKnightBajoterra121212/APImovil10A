<?php

namespace API\Controllers;

use API\Responses\ApiResponse;
use API\Services\ApiTokenService;
use API\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly ApiTokenService $tokenService,
    ) {
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Error de validación.', $validator->errors()->toArray(), 422);
        }

        // Conecta con el sistema de autenticación existente usando Auth::validate
        // y respetando la regla de negocio de usuarios activos.
        $user = $this->authService->validateActiveUserCredentials(
            (string) $request->input('email'),
            (string) $request->input('password')
        );

        if (!$user) {
            return ApiResponse::error(
                'Las credenciales no son válidas o el usuario está inactivo.',
                ['credentials' => ['Credenciales inválidas.']],
                401
            );
        }

        $token = $this->tokenService->issueTokenForUser($user);

        return ApiResponse::success('Login exitoso.', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'role' => $user->role?->name ?? null,
                'id_role' => $user->id_role,
            ],
            'auth' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if ($token) {
            $this->tokenService->revokeToken($token);
        }

        return ApiResponse::success('Logout exitoso.');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return ApiResponse::success('Usuario autenticado.', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'role' => $user->role?->name ?? null,
                'id_role' => $user->id_role,
                'id_company' => $user->id_company,
                'id_branch' => $user->id_branch,
            ],
        ]);
    }
}

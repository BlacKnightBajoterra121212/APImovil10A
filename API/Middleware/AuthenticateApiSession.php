<?php

namespace API\Middleware;

use API\Responses\ApiResponse;
use API\Services\ApiTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiSession
{
    public function __construct(private readonly ApiTokenService $tokenService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return ApiResponse::error('No autorizado. Token no proporcionado.', [
                'token' => ['Debes enviar un token Bearer válido.'],
            ], 401);
        }

        $user = $this->tokenService->getUserFromToken($token);

        if (!$user) {
            return ApiResponse::error('No autorizado. Token inválido o expirado.', [
                'token' => ['El token no es válido o ya expiró.'],
            ], 401);
        }

        // Inyecta el usuario autenticado en el request para los endpoints protegidos.
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}

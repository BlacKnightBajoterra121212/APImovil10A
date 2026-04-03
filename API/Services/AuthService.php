<?php

namespace API\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function validateActiveUserCredentials(string $email, string $password): ?User
    {
        // Reutiliza el mismo criterio de negocio del login web:
        // credenciales válidas + usuario con status = active.
        $credentials = [
            'email' => $email,
            'password' => $password,
            'status' => 'active',
        ];

        if (!Auth::validate($credentials)) {
            return null;
        }

        return User::with('role')
            ->where('email', $email)
            ->where('status', 'active')
            ->first();
    }
}

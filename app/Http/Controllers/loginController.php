<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class loginController extends Controller
{
    /**
     * Muestra el formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesa el login del usuario
     */
    public function login(Request $request)
    {
        // Validar los campos
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Debe ingresar un correo electrónico válido',
            'email.max' => 'El correo no puede tener más de 100 caracteres',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
        ]);

        // Si la validación falla
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Preparar credenciales
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'active' // Solo usuarios activos pueden ingresar
        ];

        // Intentar autenticar
        if (Auth::attempt($credentials)) {
            // Regenerar sesión para seguridad
            $request->session()->regenerate();
            
            // Redirigir según el rol o a dashboard
            return redirect()->intended('/dashboard')
                ->with('success', '¡Bienvenido ' . Auth::user()->name . '!');
        }

        // Si falla la autenticación
        return redirect()->back()
            ->withErrors([
                'email' => 'Las credenciales no coinciden con nuestros registros o el usuario está inactivo',
            ])
            ->withInput($request->except('password'));
    }

    /**
     * Cierra la sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'Sesión cerrada exitosamente');
    }
}

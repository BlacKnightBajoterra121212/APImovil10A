<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class userController extends Controller
{
    /**
     * Muestra la vista principal del personal
     */
    public function showPersonal(Request $request)
    {
        // Obtener todos los roles para los selects
        $roles = Role::all();
        
        // Obtener los usuarios con su rol
        $personal = User::with('role')
            ->orderBy('name', 'asc')
            ->paginate(10);
        
        // Verificar si hay búsqueda en sesión
        $busqueda = session('busqueda');
        
        return view("personal.dirPersonal", compact('personal', 'roles', 'busqueda'));
    }
    
    /**
     * Buscar personal por nombre
     */
    public function buscarPersonal(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|min:2'
        ]);
        
        $nombre = $request->input('nombre');
        
        // Buscar usuarios que coincidan con el nombre
        $personal = User::with('role')
            ->where('name', 'LIKE', "%{$nombre}%")
            ->orWhere('email', 'LIKE', "%{$nombre}%")
            ->orderBy('name', 'asc')
            ->paginate(10);
        
        $roles = Role::all();
        
        // Guardar término de búsqueda en sesión para mostrarlo en la vista
        session()->flash('busqueda', $nombre);
        
        return view("personal.dirPersonal", compact('personal', 'roles'));
    }
    
    /**
     * Guardar un nuevo usuario/personal
     */
    public function guardarPersonal(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'id_role' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Obtener ID de la compañía del usuario logueado
            $id_company = auth()->user()->id_company ?? 1;
            
            // Crear usuario en la tabla users
            $user = User::create([
                'id_company' => $id_company,
                'id_role' => $request->id_role,
                'id_branch' => null,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? '12345'),
                'phone' => $request->phone,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            DB::commit();
            
            return redirect()->route('personal')
                ->with('success', 'Usuario creado exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Actualizar un usuario/personal existente
     */
    public function actualizarPersonal(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'id_role' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Buscar el usuario
            $user = User::findOrFail($id);
            
            // Actualizar datos del usuario
            $user->name = $request->name;
            $user->email = $request->email;
            $user->id_role = $request->id_role;
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->updated_at = now();
            $user->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'data' => $user
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Eliminar (desactivar) un usuario
     */
    public function eliminarPersonal($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Desactivar usuario
            $user->status = 'inactive';
            $user->updated_at = now();
            $user->save();
            
            return redirect()->route('personal')
                ->with('success', 'Usuario desactivado exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al desactivar el usuario: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtener datos de un usuario específico para editar (API)
     */
    public function obtenerPersonal($id)
    {
        try {
            $user = User::with('role')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'id_role' => $user->id_role,
                    'role_name' => $user->role->name ?? '',
                    'phone' => $user->phone,
                    'status' => $user->status
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }
    }
    
    /**
     * Reactivar un usuario desactivado
     */
    public function reactivarPersonal($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Reactivar usuario
            $user->status = 'active';
            $user->updated_at = now();
            $user->save();
            
            return redirect()->route('personal')
                ->with('success', 'Usuario reactivado exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al reactivar el usuario: ' . $e->getMessage());
        }
    }
}
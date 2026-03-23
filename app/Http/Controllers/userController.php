<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Personal;
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
        
        // Obtener el personal con paginación y relación con usuarios
        $personal = User::with('user')
            ->orderBy('nomreg', 'asc')
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
        
        // Buscar personal que coincida con el nombre
        $personal = Personal::with('user')
            ->where('nomreg', 'LIKE', "%{$nombre}%")
            ->orWhere('nompat', 'LIKE', "%{$nombre}%")
            ->orWhere('nommat', 'LIKE', "%{$nombre}%")
            ->orderBy('nomreg', 'asc')
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
            'nomreg' => 'required|string|max:100',
            'nompat' => 'required|string|max:100',
            'nommat' => 'nullable|string|max:100',
            'numcel' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'id_role' => 'required|exists:roles,id',
            'pais' => 'nullable|string|max:100'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Obtener ID de la compañía del usuario logueado (asumiendo que usas autenticación)
            $id_company = auth()->user()->id_company ?? 1; // Cambia según tu lógica
            
            // Crear usuario en la tabla users
            $user = User::create([
                'id_company' => $id_company,
                'id_role' => $request->id_role,
                'id_branch' => null,
                'name' => $request->nomreg . ' ' . $request->nompat,
                'email' => $request->email,
                'password' => Hash::make($request->nompas ?? '12345'),
                'phone' => $request->numcel,
                'status' => 'active'
            ]);
            
            // Crear registro en la tabla personal
            $personal = new Personal();
            $personal->id_user = $user->id;
            $personal->nomreg = $request->nomreg;
            $personal->nompat = $request->nompat;
            $personal->nommat = $request->nommat;
            $personal->numcel = $request->numcel;
            $personal->numtel = $request->numtel ?? null;
            $personal->nomema = $request->email;
            $personal->fecreg = now();
            $personal->save();
            
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
            'nomreg' => 'required|string|max:100',
            'nompat' => 'required|string|max:100',
            'nommat' => 'nullable|string|max:100',
            'numcel' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $request->input('user_id'),
            'id_role' => 'required|exists:roles,id',
            'fnac' => 'nullable|date',
            'falta' => 'nullable|date',
            'estatus' => 'required|in:active,inactive',
            // Datos de pagos
            'comisiona' => 'nullable|boolean',
            'banco' => 'nullable|string|max:100',
            'titular' => 'nullable|string|max:255',
            'cuenta' => 'nullable|string|max:50',
            'clabe' => 'nullable|string|max:50',
            // Datos fiscales
            'rfc' => 'nullable|string|max:20',
            'regimen' => 'nullable|string|max:10',
            'razon' => 'nullable|string|max:255',
            'domicilio' => 'nullable|string|max:500',
            'cp' => 'nullable|string|max:10',
            'estado' => 'nullable|string|max:100',
            'email_fiscal' => 'nullable|email'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Buscar el personal
            $personal = Personal::findOrFail($id);
            
            // Actualizar datos del personal
            $personal->nomreg = $request->nomreg;
            $personal->nompat = $request->nompat;
            $personal->nommat = $request->nommat;
            $personal->numcel = $request->numcel;
            $personal->numtel = $request->numtel ?? $personal->numtel;
            $personal->nomema = $request->email;
            $personal->fecnac = $request->fnac;
            $personal->fecreg = $request->falta ?? $personal->fecreg;
            
            // Actualizar estatus (activo/inactivo)
            if ($request->estatus == 'inactive') {
                $personal->feccan = now(); // Fecha de cancelación
            } else {
                $personal->feccan = null; // Activo
            }
            
            // Datos de pagos
            $personal->comisiona = $request->comisiona ?? false;
            $personal->banco = $request->banco;
            $personal->titular = $request->titular;
            $personal->cuenta = $request->cuenta;
            $personal->clabe = $request->clabe;
            
            // Datos fiscales
            $personal->df_rfc = $request->rfc;
            $personal->df_reg = $request->regimen;
            $personal->df_razon = $request->razon;
            $personal->df_dom = $request->domicilio;
            $personal->df_pos = $request->cp;
            $personal->df_edo = $request->estado;
            $personal->df_email = $request->email_fiscal;
            
            $personal->save();
            
            // Actualizar el usuario relacionado
            $user = User::find($personal->id_user);
            if ($user) {
                $user->name = $request->nomreg . ' ' . $request->nompat;
                $user->email = $request->email;
                $user->id_role = $request->id_role;
                $user->phone = $request->numcel;
                $user->status = $request->estatus;
                $user->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'data' => $personal
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
            $personal = Personal::findOrFail($id);
            
            // En lugar de eliminar, desactivamos
            $personal->feccan = now();
            $personal->save();
            
            // Desactivar también el usuario
            if ($personal->id_user) {
                $user = User::find($personal->id_user);
                if ($user) {
                    $user->status = 'inactive';
                    $user->save();
                }
            }
            
            return redirect()->route('personal')
                ->with('success', 'Usuario desactivado exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al desactivar el usuario: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtener datos de un personal específico para editar (API)
     */
    public function obtenerPersonal($id)
    {
        try {
            $personal = Personal::with('user')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $personal->id,
                    'user_id' => $personal->user->id,
                    'nomreg' => $personal->nomreg,
                    'nompat' => $personal->nompat,
                    'nommat' => $personal->nommat,
                    'numcel' => $personal->numcel,
                    'numtel' => $personal->numtel,
                    'email' => $personal->user->email,
                    'id_role' => $personal->user->id_role,
                    'fecnac' => $personal->fecnac,
                    'fecreg' => $personal->fecreg,
                    'feccan' => $personal->feccan,
                    'status' => $personal->user->status,
                    'comisiona' => $personal->comisiona,
                    'banco' => $personal->banco,
                    'titular' => $personal->titular,
                    'cuenta' => $personal->cuenta,
                    'clabe' => $personal->clabe,
                    'df_rfc' => $personal->df_rfc,
                    'df_reg' => $personal->df_reg,
                    'df_razon' => $personal->df_razon,
                    'df_dom' => $personal->df_dom,
                    'df_pos' => $personal->df_pos,
                    'df_edo' => $personal->df_edo,
                    'df_email' => $personal->df_email
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
            $personal = Personal::findOrFail($id);
            
            // Reactivar personal
            $personal->feccan = null;
            $personal->save();
            
            // Reactivar usuario
            if ($personal->id_user) {
                $user = User::find($personal->id_user);
                if ($user) {
                    $user->status = 'active';
                    $user->save();
                }
            }
            
            return redirect()->route('personal')
                ->with('success', 'Usuario reactivado exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al reactivar el usuario: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with('company')->orderBy('name', 'asc')->get();
        $companies = Company::all();
        
        return view('sucursales.sucursales', compact('branches', 'companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_company' => 'required|exists:companies,id',
            'name' => 'required|string|max:100|unique:branches,name',
            'address' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            DB::beginTransaction();

            $branch = Branch::create([
                'id_company' => $request->id_company,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'status' => $request->status
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sucursal creada exitosamente',
                'data' => $branch->load('company')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la sucursal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_company' => 'required|exists:companies,id',
            'name' => 'required|string|max:100|unique:branches,name,' . $id,
            'address' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            DB::beginTransaction();

            $branch = Branch::findOrFail($id);
            $branch->update([
                'id_company' => $request->id_company,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'status' => $request->status
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sucursal actualizada exitosamente',
                'data' => $branch->load('company')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la sucursal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $branch = Branch::findOrFail($id);
            $branch->status = 'inactive';
            $branch->save();

            return response()->json([
                'success' => true,
                'message' => 'Sucursal desactivada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar la sucursal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reactivate($id)
    {
        try {
            $branch = Branch::findOrFail($id);
            $branch->status = 'active';
            $branch->save();

            return response()->json([
                'success' => true,
                'message' => 'Sucursal reactivada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reactivar la sucursal: ' . $e->getMessage()
            ], 500);
        }
    }
}
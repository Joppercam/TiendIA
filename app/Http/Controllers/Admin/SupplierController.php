<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Constructor del controlador.
     */
    public function __construct()
    {
        $this->middleware('permission:view-suppliers')->only(['index', 'show']);
        $this->middleware('permission:create-suppliers')->only(['create', 'store']);
        $this->middleware('permission:edit-suppliers')->only(['edit', 'update']);
        $this->middleware('permission:delete-suppliers')->only(['destroy']);
    }

    /**
     * Mostrar listado de proveedores.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();
        
        // Filtro por nombre o email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filtro por estado (activo/inactivo)
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }
        
        $suppliers = $query->orderBy('name')->paginate(15);
        
        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return view('admin.suppliers.create');
    }

    /**
     * Almacenar un nuevo proveedor.
     */
    public function store(SupplierRequest $request)
    {
        $supplier = Supplier::create($request->validated());
        
        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('success', 'Proveedor creado correctamente.');
    }

    /**
     * Mostrar información de un proveedor.
     */
    public function show(Supplier $supplier)
    {
        // Cargar los movimientos de inventario relacionados
        $movements = $supplier->inventoryMovements()
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.suppliers.show', compact('supplier', 'movements'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Actualizar un proveedor.
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());
        
        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Eliminar un proveedor.
     */
    public function destroy(Supplier $supplier)
    {
        // Verificar si tiene movimientos de inventario asociados
        $hasMovements = $supplier->inventoryMovements()->exists();
        
        if ($hasMovements) {
            return back()->with('error', 'No se puede eliminar este proveedor porque tiene movimientos de inventario asociados.');
        }
        
        $supplier->delete();
        
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
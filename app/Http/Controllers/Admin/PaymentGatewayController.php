<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentGatewayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,super-admin');
    }
    
    /**
     * Muestra el listado de pasarelas de pago
     */
    public function index()
    {
        $gateways = PaymentGateway::orderBy('position', 'asc')->get();
        
        return view('admin.payment-gateways.index', [
            'gateways' => $gateways
        ]);
    }
    
    /**
     * Muestra el formulario para crear una nueva pasarela
     */
    public function create()
    {
        return view('admin.payment-gateways.create');
    }
    
    /**
     * Almacena una nueva pasarela de pago
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_gateways',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'logo' => 'nullable|image|max:1024',
            'position' => 'nullable|integer|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'instructions' => 'nullable|string',
            'config' => 'nullable|array',
            'credentials' => 'nullable|array'
        ]);
        
        // Procesar logo si se proporciona
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('payment-gateways', 'public');
            $validated['logo'] = $path;
        }
        
        // Establecer posición si no se proporciona
        if (!isset($validated['position'])) {
            $maxPosition = PaymentGateway::max('position') ?? 0;
            $validated['position'] = $maxPosition + 1;
        }
        
        // Si es pasarela por defecto, desactivar las demás
        if ($validated['is_default'] ?? false) {
            PaymentGateway::where('is_default', true)->update(['is_default' => false]);
        }
        
        $gateway = PaymentGateway::create($validated);
        
        return redirect()->route('admin.payment-gateways.index')
            ->with('success', 'Pasarela de pago creada correctamente');
    }
    
    /**
     * Muestra el formulario para editar una pasarela
     */
    public function edit($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        
        return view('admin.payment-gateways.edit', [
            'gateway' => $gateway
        ]);
    }
    
    /**
     * Actualiza una pasarela de pago
     */
    public function update(Request $request, $id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_gateways,code,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'logo' => 'nullable|image|max:1024',
            'position' => 'nullable|integer|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'instructions' => 'nullable|string',
            'config' => 'nullable|array',
            'credentials' => 'nullable|array'
        ]);
        
        // Procesar logo si se proporciona
        if ($request->hasFile('logo')) {
            // Eliminar el logo anterior si existe
            if ($gateway->logo) {
                Storage::disk('public')->delete($gateway->logo);
            }
            
            $path = $request->file('logo')->store('payment-gateways', 'public');
            $validated['logo'] = $path;
        }
        
        // Si es pasarela por defecto, desactivar las demás
        if ($validated['is_default'] ?? false) {
            PaymentGateway::where('id', '!=', $id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
        
        $gateway->update($validated);
        
        return redirect()->route('admin.payment-gateways.index')
            ->with('success', 'Pasarela de pago actualizada correctamente');
    }
    
    /**
     * Elimina una pasarela de pago
     */
    public function destroy($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        
        // Verificar si hay pagos asociados
        if ($gateway->payments()->exists()) {
            return back()->with('error', 'No se puede eliminar esta pasarela porque tiene pagos asociados');
        }
        
        // Eliminar el logo si existe
        if ($gateway->logo) {
            Storage::disk('public')->delete($gateway->logo);
        }
        
        $gateway->delete();
        
        return redirect()->route('admin.payment-gateways.index')
            ->with('success', 'Pasarela de pago eliminada correctamente');
    }
    
    /**
     * Cambia la posición de una pasarela (arriba/abajo)
     */
    public function changePosition(Request $request, $id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $direction = $request->input('direction', 'up');
        
        if ($direction === 'up' && $gateway->position > 0) {
            // Buscar pasarela en posición anterior
            $prevGateway = PaymentGateway::where('position', $gateway->position - 1)->first();
            
            if ($prevGateway) {
                $prevGateway->position = $gateway->position;
                $prevGateway->save();
                
                $gateway->position = $gateway->position - 1;
                $gateway->save();
            }
        } elseif ($direction === 'down') {
            // Buscar pasarela en posición siguiente
            $nextGateway = PaymentGateway::where('position', $gateway->position + 1)->first();
            
            if ($nextGateway) {
                $nextGateway->position = $gateway->position;
                $nextGateway->save();
                
                $gateway->position = $gateway->position + 1;
                $gateway->save();
            }
        }
        
        return back()->with('success', 'Posición actualizada correctamente');
    }
    
    /**
     * Cambia el estado activo/inactivo de una pasarela
     */
    public function toggleActive($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $gateway->is_active = !$gateway->is_active;
        $gateway->save();
        
        return back()->with('success', 'Estado actualizado correctamente');
    }
}
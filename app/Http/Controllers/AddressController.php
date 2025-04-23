<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $addresses = Auth::user()->addresses;
        
        return view('addresses.index', [
            'addresses' => $addresses,
        ]);
    }

    public function create()
    {
        return view('addresses.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'is_default' => 'sometimes|boolean',
            'is_billing' => 'sometimes|boolean',
            'is_shipping' => 'sometimes|boolean',
        ]);
        
        $user = Auth::user();
        
        if (isset($validatedData['is_default']) && $validatedData['is_default']) {
            // Reset default status on other addresses
            $user->addresses()->update(['is_default' => false]);
        }
        
        $address = $user->addresses()->create($validatedData);
        
        return redirect()->route('addresses.index')
            ->with('success', 'Dirección creada correctamente.');
    }

    public function edit(Address $address)
    {
        $this->authorize('update', $address);
        
        return view('addresses.edit', [
            'address' => $address,
        ]);
    }

    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'is_default' => 'sometimes|boolean',
            'is_billing' => 'sometimes|boolean',
            'is_shipping' => 'sometimes|boolean',
        ]);
        
        if (isset($validatedData['is_default']) && $validatedData['is_default']) {
            // Reset default status on other addresses
            Auth::user()->addresses()->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }
        
        $address->update($validatedData);
        
        return redirect()->route('addresses.index')
            ->with('success', 'Dirección actualizada correctamente.');
    }

    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);
        
        // Check if address is used in orders
        if ($address->shippingOrders()->count() > 0 || $address->billingOrders()->count() > 0) {
            return redirect()->route('addresses.index')
                ->with('error', 'No se puede eliminar esta dirección porque está asociada a pedidos.');
        }
        
        $address->delete();
        
        return redirect()->route('addresses.index')
            ->with('success', 'Dirección eliminada correctamente.');
    }
}
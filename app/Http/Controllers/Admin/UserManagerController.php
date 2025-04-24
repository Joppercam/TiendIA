<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagerController extends AdminController
{
    public function index(Request $request)
    {
        $query = User::query();
        
        // Aplicar filtros
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('role') && !empty($request->role)) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        $users = $query->withCount('orders')
                      ->paginate(15)
                      ->appends($request->all());
        
        $roles = Role::pluck('name', 'id');
        
        return view('admin.users.index', compact('users', 'roles'));
    }
    
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array'
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);
        
        $user->roles()->sync($request->roles);
        
        // Crear perfil asociado
        $user->profile()->create();
        
        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuario creado correctamente');
    }
    
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }
    
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array'
        ]);
        
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email']
        ]);
        
        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }
        
        $user->roles()->sync($request->roles);
        
        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuario actualizado correctamente');
    }
    
    public function destroy(User $user)
    {
        // Evitar eliminar al usuario actual
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuario eliminado correctamente');
    }
}
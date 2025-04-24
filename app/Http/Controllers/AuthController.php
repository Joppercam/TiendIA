<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Métodos adicionales para autenticación personalizada si los necesitamos
    // (complementarios a los que proporciona Breeze)
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
    
    // Aquí podríamos añadir métodos para autenticación de dos factores,
    // bloqueo de cuentas, etc. si se requiere extender Breeze
}

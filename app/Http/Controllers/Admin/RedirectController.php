<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use App\Services\SeoService;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
        $this->middleware('admin');
    }

    /**
     * Muestra listado de redirecciones
     */
    public function index()
    {
        $redirects = Redirect::orderBy('source_url')->paginate(20);
        return view('admin.seo.redirects.index', compact('redirects'));
    }

    /**
     * Muestra formulario para crear redirección
     */
    public function create()
    {
        return view('admin.seo.redirects.create');
    }

    /**
     * Almacena una nueva redirección
     */
    public function store(Request $request)
    {
        $request->validate([
            'source_url' => 'required|string|max:255',
            'target_url' => 'required|string|max:255|different:source_url',
            'status_code' => 'required|in:301,302',
        ]);
        
        try {
            $this->seoService->createOrUpdateRedirect(
                $request->source_url,
                $request->target_url,
                (int) $request->status_code
            );
            
            return redirect()->route('admin.seo.redirects.index')
                ->with('success', 'Redirección creada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear la redirección: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Muestra formulario para editar redirección
     */
    public function edit(Redirect $redirect)
    {
        return view('admin.seo.redirects.edit', compact('redirect'));
    }

    /**
     * Actualiza una redirección existente
     */
    public function update(Request $request, Redirect $redirect)
    {
        $request->validate([
            'source_url' => 'required|string|max:255',
            'target_url' => 'required|string|max:255|different:source_url',
            'status_code' => 'required|in:301,302',
            'is_active' => 'boolean',
        ]);
        
        try {
            $redirect->update([
                'source_url' => $request->source_url,
                'target_url' => $request->target_url,
                'status_code' => $request->status_code,
                'is_active' => $request->has('is_active'),
            ]);
            
            return redirect()->route('admin.seo.redirects.index')
                ->with('success', 'Redirección actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la redirección: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Elimina una redirección
     */
    public function destroy(Redirect $redirect)
    {
        try {
            $redirect->delete();
            return redirect()->route('admin.seo.redirects.index')
                ->with('success', 'Redirección eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la redirección: ' . $e->getMessage());
        }
    }
}
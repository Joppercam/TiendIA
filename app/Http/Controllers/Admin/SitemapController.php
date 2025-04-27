<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sitemap;
use App\Services\SeoService;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
        $this->middleware('admin');
    }

    /**
     * Muestra listado de configuraciones de sitemap
     */
    public function index()
    {
        $sitemaps = Sitemap::all();
        return view('admin.seo.sitemaps.index', compact('sitemaps'));
    }

    /**
     * Muestra formulario para crear configuración de sitemap
     */
    public function create()
    {
        return view('admin.seo.sitemaps.create');
    }

    /**
     * Almacena una nueva configuración de sitemap
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:products,categories,brands,pages',
            'frequency' => 'required|string|in:always,hourly,daily,weekly,monthly,yearly,never',
            'priority' => 'required|numeric|min:0|max:1|regex:/^0?\.\d+$/',
            'is_active' => 'boolean',
        ]);
        
        try {
            Sitemap::create([
                'name' => $request->name,
                'type' => $request->type,
                'frequency' => $request->frequency,
                'priority' => $request->priority,
                'is_active' => $request->has('is_active'),
            ]);
            
            return redirect()->route('admin.seo.sitemaps.index')
                ->with('success', 'Configuración de sitemap creada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear la configuración: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Muestra formulario para editar configuración de sitemap
     */
    public function edit(Sitemap $sitemap)
    {
        return view('admin.seo.sitemaps.edit', compact('sitemap'));
    }

    /**
     * Actualiza una configuración de sitemap existente
     */
    public function update(Request $request, Sitemap $sitemap)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:products,categories,brands,pages',
            'frequency' => 'required|string|in:always,hourly,daily,weekly,monthly,yearly,never',
            'priority' => 'required|numeric|min:0|max:1|regex:/^0?\.\d+$/',
            'is_active' => 'boolean',
        ]);
        
        try {
            $sitemap->update([
                'name' => $request->name,
                'type' => $request->type,
                'frequency' => $request->frequency,
                'priority' => $request->priority,
                'is_active' => $request->has('is_active'),
            ]);
            
            return redirect()->route('admin.seo.sitemaps.index')
                ->with('success', 'Configuración de sitemap actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la configuración: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Elimina una configuración de sitemap
     */
    public function destroy(Sitemap $sitemap)
    {
        try {
            $sitemap->delete();
            return redirect()->route('admin.seo.sitemaps.index')
                ->with('success', 'Configuración de sitemap eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la configuración: ' . $e->getMessage());
        }
    }

    /**
     * Genera los sitemaps
     */
    public function generate(Request $request)
    {
        try {
            $type = $request->type ?? null;
            $this->seoService->generateSitemaps($type);
            
            return redirect()->route('admin.seo.sitemaps.index')
                ->with('success', 'Sitemaps generados correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar sitemaps: ' . $e->getMessage());
        }
    }
}
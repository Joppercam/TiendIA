<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoMetadata;
use App\Services\SeoService;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
        $this->middleware('admin');
    }

    /**
     * Muestra el panel de control SEO
     */
    public function dashboard()
    {
        return view('admin.seo.dashboard');
    }
    
    /**
     * Edita los metadatos SEO de un modelo específico
     */
    public function editMetadata(Request $request, $type, $id)
    {
        // Mapeamos los tipos a las clases de modelo
        $modelMap = [
            'products' => \App\Models\Product::class,
            'categories' => \App\Models\Category::class,
            'brands' => \App\Models\Brand::class,
            'pages' => \App\Models\Page::class,
        ];
        
        if (!isset($modelMap[$type])) {
            return redirect()->route('admin.seo.dashboard')
                ->with('error', 'Tipo de modelo no válido.');
        }
        
        $modelClass = $modelMap[$type];
        $model = $modelClass::findOrFail($id);
        
        return view('admin.seo.edit_metadata', compact('model', 'type'));
    }
    
    /**
     * Actualiza los metadatos SEO de un modelo
     */
    public function updateMetadata(Request $request, $type, $id)
    {
        $request->validate([
            'title' => 'nullable|max:70',
            'meta_description' => 'nullable|max:160',
            'meta_keywords' => 'nullable|max:255',
            'og_title' => 'nullable|max:70',
            'og_description' => 'nullable|max:200',
            'og_image' => 'nullable|max:255',
            'twitter_title' => 'nullable|max:70',
            'twitter_description' => 'nullable|max:200',
            'twitter_image' => 'nullable|max:255',
            'canonical_url' => 'nullable|url',
            'no_index' => 'boolean',
            'no_follow' => 'boolean',
        ]);
        
        // Mapeamos los tipos a las clases de modelo
        $modelMap = [
            'products' => \App\Models\Product::class,
            'categories' => \App\Models\Category::class,
            'brands' => \App\Models\Brand::class,
            'pages' => \App\Models\Page::class,
        ];
        
        if (!isset($modelMap[$type])) {
            return redirect()->route('admin.seo.dashboard')
                ->with('error', 'Tipo de modelo no válido.');
        }
        
        $modelClass = $modelMap[$type];
        $model = $modelClass::findOrFail($id);
        
        // Preparamos los datos
        $data = $request->only([
            'title', 'meta_description', 'meta_keywords', 
            'og_title', 'og_description', 'og_image',
            'twitter_title', 'twitter_description', 'twitter_image',
            'canonical_url'
        ]);
        
        // Agregamos los campos boolean
        $data['no_index'] = $request->has('no_index');
        $data['no_follow'] = $request->has('no_follow');
        
        // Actualizamos los metadatos
        $this->seoService->updateOrCreateMetadata($model, $data);
        
        return redirect()->back()
            ->with('success', 'Metadatos SEO actualizados correctamente.');
    }
}
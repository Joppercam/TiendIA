<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            
            // Guardar imagen original
            $image->storeAs('public/brands', $filename);
            
            // Crear miniatura
            $thumbnail = Image::make($image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            
            Storage::put('public/brands/thumbnails/' . $filename, $thumbnail->stream());
            
            $data['logo'] = $filename;
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marca creada correctamente');
    }

    public function show(Brand $brand)
    {
        return view('admin.brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($brand->logo) {
                Storage::delete([
                    'public/brands/' . $brand->logo,
                    'public/brands/thumbnails/' . $brand->logo
                ]);
            }

            $image = $request->file('logo');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            
            // Guardar imagen original
            $image->storeAs('public/brands', $filename);
            
            // Crear miniatura
            $thumbnail = Image::make($image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            
            Storage::put('public/brands/thumbnails/' . $filename, $thumbnail->stream());
            
            $data['logo'] = $filename;
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marca actualizada correctamente');
    }

    public function destroy(Brand $brand)
    {
        // Verificar si tiene productos asociados
        if ($brand->products()->count() > 0) {
            return back()->withErrors(['general' => 'No se puede eliminar la marca porque tiene productos asociados']);
        }

        // Eliminar logo
        if ($brand->logo) {
            Storage::delete([
                'public/brands/' . $brand->logo,
                'public/brands/thumbnails/' . $brand->logo
            ]);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marca eliminada correctamente');
    }
}

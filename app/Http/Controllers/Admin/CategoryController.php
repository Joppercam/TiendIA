<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'order' => 'integer'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            
            // Guardar imagen original
            $image->storeAs('public/categories', $filename);
            
            // Crear miniatura
            $thumbnail = Image::make($image)
                ->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            
            Storage::put('public/categories/thumbnails/' . $filename, $thumbnail->stream());
            
            $data['image'] = $filename;
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría creada correctamente');
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)
            ->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'order' => 'integer'
        ]);

        // Verificar que parent_id no sea el mismo ID o un hijo
        if ($request->parent_id && ($request->parent_id == $category->id || $this->isChildOf($category->id, $request->parent_id))) {
            return back()->withErrors(['parent_id' => 'No se puede seleccionar esta categoría como padre']);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($category->image) {
                Storage::delete([
                    'public/categories/' . $category->image,
                    'public/categories/thumbnails/' . $category->image
                ]);
            }

            $image = $request->file('image');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            
            // Guardar imagen original
            $image->storeAs('public/categories', $filename);
            
            // Crear miniatura
            $thumbnail = Image::make($image)
                ->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            
            Storage::put('public/categories/thumbnails/' . $filename, $thumbnail->stream());
            
            $data['image'] = $filename;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría actualizada correctamente');
    }

    public function destroy(Category $category)
    {
        // Verificar si tiene productos asociados
        if ($category->products()->count() > 0) {
            return back()->withErrors(['general' => 'No se puede eliminar la categoría porque tiene productos asociados']);
        }

        // Verificar si tiene categorías hijas
        if ($category->children()->count() > 0) {
            return back()->withErrors(['general' => 'No se puede eliminar la categoría porque tiene subcategorías']);
        }

        // Eliminar imágenes
        if ($category->image) {
            Storage::delete([
                'public/categories/' . $category->image,
                'public/categories/thumbnails/' . $category->image
            ]);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría eliminada correctamente');
    }

    private function isChildOf($categoryId, $possibleParentId)
    {
        $children = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
        
        if (in_array($possibleParentId, $children)) {
            return true;
        }
        
        foreach ($children as $childId) {
            if ($this->isChildOf($childId, $possibleParentId)) {
                return true;
            }
        }
        
        return false;
    }
}

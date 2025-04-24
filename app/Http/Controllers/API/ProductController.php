<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     summary="Obtener lista de productos",
     *     description="Retorna una lista paginada de productos con posibilidad de filtrado",
     *     operationId="getProducts",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filtrar por ID de categoría",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="brand",
     *         in="query",
     *         description="Filtrar por ID de marca",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Búsqueda por texto",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="price_min",
     *         in="query",
     *         description="Precio mínimo",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="price_max",
     *         in="query",
     *         description="Precio máximo",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Ordenamiento (price_asc, price_desc, newest, popular)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Elementos por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud inválida"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $products = $this->productService->getProducts(
            $request->get('category'),
            $request->get('brand'),
            $request->get('search'),
            $request->get('price_min'),
            $request->get('price_max'),
            $request->get('sort'),
            $request->get('per_page', 15)
        );

        return ProductResource::collection($products);
    }

    /**
     * Detalles de un producto específico
     */
    public function show($id)
    {
        $product = Product::with(['category', 'brand', 'images'])->findOrFail($id);
        return new ProductResource($product);
    }

    /**
     * Crear un nuevo producto (requiere autenticación)
     */
    public function store(Request $request)
    {
        $this->authorize('create', Product::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'sku' => 'required|string|unique:products',
            'quantity' => 'required|integer|min:0',
            // Otras validaciones según sea necesario
        ]);

        $product = $this->productService->createProduct($validated);
        
        return new ProductResource($product);
    }

    /**
     * Actualizar un producto existente (requiere autenticación)
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'brand_id' => 'sometimes|exists:brands,id',
            'sku' => 'sometimes|string|unique:products,sku,' . $id,
            'quantity' => 'sometimes|integer|min:0',
            // Otras validaciones según sea necesario
        ]);

        $product = $this->productService->updateProduct($product, $validated);
        
        return new ProductResource($product);
    }

    /**
     * Eliminar un producto (requiere autenticación)
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('delete', $product);
        
        $this->productService->deleteProduct($product);
        
        return response()->json(['message' => 'Producto eliminado correctamente']);
    }
}
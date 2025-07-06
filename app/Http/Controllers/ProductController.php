<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        return response()->json($product, Response::HTTP_CREATED);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        return response()->json($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        try {
            $product->delete();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            // If the product is referenced by other records, prevent deletion
            return response()->json(
                ['message' => 'Cannot delete this product as it is being used in orders or stock movements.'],
                Response::HTTP_CONFLICT
            );
        }
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return response()->json([
            'message' => 'Product correctly restored.',
            'product' => $product
        ]);
    }

    /**
     * Get paginated products with optional filters
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getFilteredProducts(Request $request): JsonResponse
    {
        $query = Product::query();
        
        // Filtro por categoría
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', 'like', '%' . $request->category . '%');
        }
        
        // Filtro por stock bajo (menor a alerta mínima)
        if ($request->has('low_stock') && $request->boolean('low_stock')) {
            $query->whereRaw('current_stock < min_stock_alert');
        }
        
        // Filtro por codigo/nombre/descripcion del producto (búsqueda)
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filtro por rango de precios
        if ($request->has('min_price') && !empty($request->min_price)) {
            $query->where('suggested_sale_price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price') && !empty($request->max_price)) {
            $query->where('suggested_sale_price', '<=', $request->max_price);
        }
        
        // Filtro por stock mínimo
        if ($request->has('min_stock') && !empty($request->min_stock)) {
            $query->where('current_stock', '>=', $request->min_stock);
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validar campos de ordenamiento permitidos
        $allowedSortFields = [
            'name', 'sku', 'category', 'current_stock', 'min_stock_alert',
            'suggested_sale_price', 'avg_purchase_price', 'created_at', 'updated_at'
        ];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        // Paginación
        $perPage = $request->get('per_page', 15);
        $perPage = min(max($perPage, 1), 100); // Limitar entre 1 y 100
        
        $products = $query->paginate($perPage);
        
        // Agregar información adicional a cada producto
        $products->getCollection()->transform(function ($product) {
            $product->is_low_stock = $product->current_stock < $product->min_stock_alert;
            $product->stock_percentage = $product->min_stock_alert > 0 
                ? round(($product->current_stock / $product->min_stock_alert) * 100, 2) 
                : 0;
            return $product;
        });
        
        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more_pages' => $products->hasMorePages(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl()
            ],
            'filters_applied' => [
                'category' => $request->get('category'),
                'low_stock' => $request->boolean('low_stock'),
                'search' => $request->get('search'),
                'min_price' => $request->get('min_price'),
                'max_price' => $request->get('max_price'),
                'min_stock' => $request->get('min_stock'),
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ]
        ]);
    }
    
    /**
     * Get available categories for filtering
     * 
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        $categories = Product::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->orderBy('category')
            ->pluck('category');
            
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
    
    /**
     * Get products statistics
     * 
     * @return JsonResponse
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_products' => Product::count(),
            'low_stock_products' => Product::whereRaw('current_stock < min_stock_alert')->count(),
            'out_of_stock_products' => Product::where('current_stock', 0)->count(),
            'total_categories' => Product::distinct('category')->count(),
            'avg_stock_value' => Product::selectRaw('AVG(current_stock * suggested_sale_price) as avg_value')
                ->first()
                ->avg_value ?? 0
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
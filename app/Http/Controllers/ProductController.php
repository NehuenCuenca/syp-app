<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterProductsRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $products = Product::all();            
            return response()->json($products);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al recuperar los productos.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = Product::create($request->validated());
            return response()->json($product, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            // Manejar errores específicos de BD
            if ($e->getCode() === '23000') { // Constraint violation
                return response()->json([
                    'message' => 'El producto con este SKU ya existe.'
                ], Response::HTTP_CONFLICT);
            }
            
            return response()->json([
                'message' => 'Error al crear el producto.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        try {
            $product->update($request->validated());
            return response()->json($product);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'El producto con este SKU ya existe.'
                ], Response::HTTP_CONFLICT);
            }
            
            return response()->json([
                'message' => 'Error al actualizar el producto.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Product $product): JsonResponse
    {
        try {
            $product->delete();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede eliminar este producto porque se está utilizando en pedidos o movimientos de stock.'
            ], Response::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el producto.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            // Validar que el ID sea numérico
            if (!is_numeric($id)) {
                return response()->json([
                    'message' => 'ID de producto no válido.'
                ], Response::HTTP_BAD_REQUEST);
            }
            
            $product = Product::onlyTrashed()->findOrFail($id);
            $product->restore();

            return response()->json([
                'message' => 'Producto correctamente restaurado.',
                'product' => $product
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Producto eliminado no encontrado.'
            ], Response::HTTP_NOT_FOUND);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al restaurar el producto.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get paginated products with optional filters
     * 
     * @param FilterProductsRequest $request
     * @return JsonResponse
     */
    public function getFilteredProducts(FilterProductsRequest $request): JsonResponse
    {
        try {
            $filters = $request->getFilters();
            $query = Product::query();
            
            // Aplicar filtros
            if (!empty($filters['category'])) {
                $query->where('category', 'like', '%' . $filters['category'] . '%');
            }
            
            if ($filters['low_stock']) {
                $query->whereRaw('current_stock < min_stock_alert');
            }
            
            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('sku', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
                });
            }
            
            if (!empty($filters['min_price'])) {
                $query->where('suggested_sale_price', '>=', $filters['min_price']);
            }
            
            if (!empty($filters['max_price'])) {
                $query->where('suggested_sale_price', '<=', $filters['max_price']);
            }
            
            if (!empty($filters['min_stock'])) {
                $query->where('current_stock', '>=', $filters['min_stock']);
            }
            
            // Ordenamiento
            $query->orderBy($filters['sort_by'], $filters['sort_order']);
            
            // Paginación
            $products = $query->paginate($filters['per_page']);
            
            // Agregar información adicional
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
                'filters_applied' => $filters
            ]);
            
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al recuperar los productos.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Se produjo un error inesperado.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function getCategories(): JsonResponse
    {
        try {
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
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al recuperar las categorías.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function getStats(): JsonResponse
    {
        try {
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
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al recuperar las estadisticas.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
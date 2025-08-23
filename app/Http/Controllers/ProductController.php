<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterProductsRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
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
            $products = Product::with(['category'])->get();            
            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Todos los productos recuperados exitosamente.',
                'total' => $products->count()
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al recuperar los productos.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            // dd($request->input('id_category'));
            $product = Product::create($request->only([
                'sku',
                'name',
                'description',
                'buy_price',
                'profit_percentage',
                'sale_price',
                'current_stock',
                'min_stock_alert',
                'id_category',
            ]));
            
            $product->category;
            return response()->json($product, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            // Manejar errores específicos de BD
            if ($e->getCode() === '23000') { // Constraint violation
                return response()->json([
                    'message' => 'El producto con este SKU ya existe.'
                ], Response::HTTP_CONFLICT);
            }
            
            return response()->json([
                'message' => 'Error al crear el producto.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Product $product): JsonResponse
    {
        $product->category;
        return response()->json($product);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        try {
            //actualizar categoria si es nueva
            if($request->input('id_category')){
                $category = Category::firstOrCreate(['name' => $request->input('category', 'Sin categoría')]);
                $request->merge(['id_category' => $category->id]);
            }
            
            $product->update($request->all());
            $product->category;

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
            $product->category;

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

    public const ALLOWED_SORT_FIELDS  = [
        'id_category', 'low_stock', 'search', 'min_sale_price', 'max_sale_price', 'min_stock',
    ];
    public const ALLOWED_SORT_DIRECTIONS = ['asc', 'desc'];

    /**
     * Get filters to be used in the index view
     */
    public function getFilters()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'categories' => Category::all(),
                'sort_by' => self::ALLOWED_SORT_FIELDS,
                'sort_direction' => self::ALLOWED_SORT_DIRECTIONS
            ],
            'message' => 'Datos para filtrar productos obtenidos exitosamente'
        ]);
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
            if (!empty($filters['id_category'])) {
                $query->where('id_category', $filters['id_category']);
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
            
            if (!empty($filters['min_sale_price'])) {
                $query->where('sale_price', '>=', $filters['min_sale_price']);
            }
            
            if (!empty($filters['max_sale_price'])) {
                $query->where('sale_price', '<=', $filters['max_sale_price']);
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
                $product->category;
                $product->is_low_stock = $product->current_stock < $product->min_stock_alert;
                $product->stock_percentage = $product->min_stock_alert > 0 
                    ? round(($product->current_stock / $product->min_stock_alert) * 100, 2) 
                    : 0;
                return $product;
            });
            
            return response()->json([
                'success' => true,
                'filtered_products' => $products,
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
    
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_products' => Product::count(),
                'low_stock_products' => Product::whereRaw('current_stock < min_stock_alert')->count(),
                'out_of_stock_products' => Product::where('current_stock', 0)->count(),
                'total_categories' => Category::count(),
                'average_profit_percentage' => floatval(Product::avg('profit_percentage')),
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
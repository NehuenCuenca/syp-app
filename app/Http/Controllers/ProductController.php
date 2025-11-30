<?php

namespace App\Http\Controllers;

use App\Exports\CatalogExport;
use App\Http\Requests\FilterProductsRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Category;
use App\Models\MovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        try {
            $products = Product::select('id', 'code', 'name', 'current_stock', 'min_stock_alert')->get();
            
            $meta = [
                'total' => $products->count()
            ];
            
            return $this->successResponse(
                $products, 
                'Todos los productos recuperados exitosamente.',
                $meta
            );
            
        } catch (QueryException $e) {
            Log::error('Error de base de datos al recuperar productos', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            
            return $this->errorResponse(
                'Error al recuperar los productos desde la base de datos.',
                [],
                [],
                500
            );
            
        } catch (Exception $e) {
            Log::error('Error inesperado al recuperar productos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al recuperar los productos.',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $product = Product::create($request->only([
                'name',
                'buy_price',
                'profit_percentage',
                'sale_price',
                'current_stock',
                'min_stock_alert',
                'id_category',
            ]));
            
            $product->load('category');

            StockMovement::create([
                'id_product' => $product->id,
                'id_order' => null,
                'id_order_detail' => null,
                'id_movement_type' => MovementType::where('name', 'Ajuste Positivo')->first()->id,
                'quantity_moved' => $product->current_stock,
                'notes' => "Stock inicial del producto {$product->name}",
            ]);
            
            DB::commit();
            
            return $this->createdResponse(
                $product,
                'Producto creado exitosamente.'
            );
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Error de base de datos al crear producto', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'data' => $request->all()
            ]);
            
            // Manejar errores específicos de BD
            if ($e->getCode() === '23000') { // Constraint violation
                return $this->errorResponse(
                    'El producto con este nombre ya existe.',
                    ['name' => ['El nombre debe ser único.']],
                    [],
                    409
                );
            }
            
            return $this->errorResponse(
                'Error al crear el producto en la base de datos.',
                [],
                [],
                500
            );
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error inesperado al crear producto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al crear el producto.',
                [],
                [],
                500
            );
        }
    }

    public function show(Product $product): JsonResponse
    {
        try {
            $product->load('category');
            
            return $this->successResponse(
                $product,
                'Producto recuperado exitosamente.'
            );
            
        } catch (Exception $e) {
            Log::error('Error inesperado al mostrar producto', [
                'product_id' => $product->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al recuperar el producto.',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            // Actualizar categoria si es nueva
            if ($request->input('id_category')) {
                $category = Category::firstOrCreate([
                    'name' => $request->input('category', 'Varios')
                ]);
                $request->merge(['id_category' => $category->id]);
            }

            $oldStock = $product->current_stock;
            $newStock = $request->input('current_stock');
            if($oldStock !== $newStock) {
                $stockDifference = $newStock - $oldStock;
                $movementType = $stockDifference > 0 ? 'Ajuste Positivo' : 'Ajuste Negativo';

                StockMovement::create([
                    'id_product' => $product->id,
                    'id_order' => null,
                    'id_order_detail' => null,
                    'id_movement_type' => MovementType::where('name', $movementType)->first()->id,
                    'quantity_moved' => $stockDifference,
                    'notes' => "Actualización de stock del producto {$product->name}",
                ]);
            }

            $product->update($request->all());
            $product->load('category');

            DB::commit();
            
            return $this->successResponse(
                $product,
                'Producto actualizado exitosamente.'
            );
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Error de base de datos al actualizar producto', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'data' => $request->all()
            ]);
            
            if ($e->getCode() === '23000') {
                return $this->errorResponse(
                    'El producto con este nombre ya existe.',
                    ['name' => ['El nombre debe ser único.']],
                    [],
                    409
                );
            }
            
            return $this->errorResponse(
                'Error al actualizar el producto en la base de datos.',
                [],
                [],
                500
            );
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error inesperado al actualizar producto', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al actualizar el producto.',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    public function destroy(Product $product): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $productId = $product->id;
            $product->delete();
            
            DB::commit();
            
            return $this->deletedResponse(
                $productId,
                'Producto eliminado exitosamente.'
            );
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Error de base de datos al eliminar producto', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            
            return $this->errorResponse(
                'No se puede eliminar este producto porque se está utilizando en pedidos o movimientos de stock.',
                [],
                [],
                409
            );
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error inesperado al eliminar producto', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al eliminar el producto.',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    public function restore($id): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            // Validar que el ID sea numérico
            if (!is_numeric($id)) {
                return $this->errorResponse(
                    'ID de producto no válido.',
                    ['id' => ['El ID debe ser un número válido.']],
                    [],
                    400
                );
            }
            
            $product = Product::onlyTrashed()->findOrFail($id);
            $product->restore();
            $product->load('category');

            DB::commit();
            
            return $this->restoredResponse(
                $product,
                'Producto restaurado exitosamente.'
            );
            
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            
            Log::warning('Intento de restaurar producto no encontrado', [
                'product_id' => $id
            ]);
            
            return $this->notFoundResponse(
                'Producto eliminado no encontrado.'
            );
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Error de base de datos al restaurar producto', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            
            return $this->errorResponse(
                'Error al restaurar el producto en la base de datos.',
                [],
                [],
                500
            );
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error inesperado al restaurar producto', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al restaurar el producto.',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    public const ALLOWED_SORT_FIELDS = [
        'code' => 'COD',   
        'id_category' => 'Categoria',   
        'sale_price' => 'Precio de venta',   
        'current_stock' => 'Stock actual',   
        'created_at' => 'Fecha de creacion',   
        'name' => 'Nombre'
    ];

    public const ALLOWED_SORT_DIRECTIONS = [
        'asc' => 'Ascendente',
        'desc' => 'Descendente'
    ];

    /**
     * Get filters to be used in the index view
     */
    public function getFilters(): JsonResponse
    {
        try {
            $products = Product::select('id', 'code', 'name')->get();

            $categories = Category::select('id', 'name')->orderBy('id', 'asc')->get();

            $data = [
                'categories' => $categories,
                'products' => $products,
                'sort_by' => self::ALLOWED_SORT_FIELDS,
                'sort_direction' => self::ALLOWED_SORT_DIRECTIONS
            ];
            
            return $this->successResponse(
                $data,
                'Datos para filtrar productos obtenidos exitosamente.'
            );
            
        } catch (QueryException $e) {
            Log::error('Error de base de datos al obtener filtros', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            
            return $this->errorResponse(
                'Error al obtener los datos de filtros desde la base de datos.',
                [],
                [],
                500
            );
            
        } catch (Exception $e) {
            Log::error('Error inesperado al obtener filtros', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al obtener los filtros.',
                ['exception' => $e->getMessage()],
                [],
                500
            );
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
            $query = Product::query()->with(['category']);
            
            // Aplicar filtros
            if (!empty($filters['id_category'])) {
                $query->where('id_category', $filters['id_category']);
            }
            
            if ($filters['low_stock']) {
                $query->whereRaw('current_stock < min_stock_alert');
            }
            
            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('code', 'like', "{$filters['search']}%")
                      ->orWhere('name', 'like', "%{$filters['search']}%");
                });
            }
            
            if (!empty($filters['min_sale_price'])) {
                $query->where('sale_price', '>=', $filters['min_sale_price']);
            }
            
            if (!empty($filters['max_sale_price'])) {
                $query->where('sale_price', '<=', $filters['max_sale_price']);
            }
            
            // Ordenamiento
            if (in_array($filters['sort_by'], array_keys(self::ALLOWED_SORT_FIELDS))) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
            }
            
            $query->select('id', 'code', 'name', 'current_stock', 'min_stock_alert', 'id_category', DB::raw('(current_stock < min_stock_alert) as is_low_stock'));

            // Paginación
            $products = $query->paginate($filters['per_page']);
            
            $additionalMeta = [
                'filters_applied' => $filters
            ];
            
            return $this->paginatedResponse(
                $products,
                'Productos filtrados recuperados exitosamente.',
                $additionalMeta
            );
            
        } catch (QueryException $e) {
            Log::error('Error de base de datos al filtrar productos', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'filters' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error al filtrar los productos desde la base de datos.',
                [],
                [],
                500
            );
            
        } catch (Exception $e) {
            Log::error('Error inesperado al filtrar productos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'filters' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al filtrar los productos.',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    /**
     * Exportar catálogo de productos a Excel
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCatalog()
    {
        // Generar el nombre del archivo con la fecha actual
        $fileName = 'catalogo_productos_' . date('Ymd') . '.xlsx';
        
        // Descargar el archivo Excel
        return Excel::download(new CatalogExport(), $fileName, null, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'X-Filename' => $fileName
        ]);
    }
}
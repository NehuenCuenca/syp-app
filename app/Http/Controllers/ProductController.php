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
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function index(FilterProductsRequest $request): JsonResponse
    {
        try {
            $filters = $request->getFilters();
            $query = Product::query()->withTrashed()->with(['category']);
            
            // Aplicar filtros
            if (!empty($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }
            
            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('code', 'like', "{$filters['search']}%")
                      ->orWhere('name', 'like', "%{$filters['search']}%");
                });
            }
            
            // Ordenamiento
            if (in_array($filters['sort_by'], array_keys(self::ALLOWED_SORT_FIELDS))) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
            }
            
            $query->select(
                'id', 'code', 'name',
                'current_stock', 'min_stock_alert', 'category_id',
                DB::raw('(current_stock < min_stock_alert) as is_low_stock'), 
                'deleted_at'
            );

            // Paginación
            $products = $query->paginate($filters['per_page']);
            
            $additionalMeta = [
                'filters_applied' => $filters
            ];

            Log::info('Retrieved filtered products', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);
            
            return $this->paginatedResponse(
                $products,
                'Productos filtrados recuperados exitosamente.',
                $additionalMeta
            );
            
        } catch (QueryException $e) {
            Log::error('Error from database trying to filter products', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error al filtrar los productos desde la base de datos.',
                [],
                [],
                500,
                config('app.debug') ? $e : null
            );
            
        } catch (Exception $e) {
            Log::error('Unexpected error trying to get filtered products', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al filtrar los productos.',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            //create category if dont exist
            $category = Category::firstOrCreate(['name' => $request->input('category', 'Varios')]);
            $request->merge(['category_id' => $category->id]);
            
            $product = Product::create($request->only([
                'name',
                'buy_price',
                'profit_percentage',
                'sale_price',
                'current_stock',
                'min_stock_alert',
                'category_id',
            ]));
            
            $product->load('category');

            StockMovement::create([
                'id_product' => $product->id,
                'id_order' => null,
                'id_order_detail' => null,
                'id_movement_type' => MovementType::where('name', 'Ajuste Positivo')->first()->id,
                'quantity_moved' => $product->current_stock,
                'notes' => "Inicio de stock",
            ]);
            
            DB::commit();

            Log::info('Product has been created', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'id_product' => $product->id,
            ]);
            
            return $this->createdResponse(
                $product,
                'Producto creado exitosamente.'
            );
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Error trying to create a product', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            // Manejar errores específicos de BD
            if ($e->getCode() === '23000') { // Constraint violation
                return $this->errorResponse(
                    'El producto con este nombre ya existe.',
                    ['name' => ['El nombre debe ser único.']],
                    [],
                    409,
                    config('app.debug') ? $e : null
                );
            }
            
            return $this->errorResponse(
                'Error al crear el producto en la base de datos.',
                [],
                [],
                500,
                config('app.debug') ? $e : null
            );
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Unexpected error trying to create a product', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al crear el producto.',
                [],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        try {
            $product->load('category');
            
            return $this->successResponse(
                $product,
                'Producto recuperado exitosamente.'
            );
        } catch (Exception $e) {
            Log::error('Unexpected error trying to show a product', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'product_id' => $product->id ?? null,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al recuperar el producto.',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            // Actualizar categoria si es nueva
            if ($request->input('category_id')) {
                $category = Category::firstOrCreate([
                    'name' => $request->input('category', 'Varios')
                ]);
                $request->merge(['category_id' => $category->id]);
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
                    'notes' => "Actualización de stock",
                ]);
            }

            $product->update($request->all());
            $product->load('category');

            DB::commit();

            Log::info('Product has been updated', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'product_id' => $product->id ?? null,
            ]);

            return $this->successResponse(
                $product,
                'Producto actualizado exitosamente.'
            );
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Error from the database while updating a product', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            if ($e->getCode() === '23000') {
                return $this->errorResponse(
                    'El producto con este nombre ya existe.',
                    ['name' => ['El nombre debe ser único.']],
                    [],
                    409,
                config('app.debug') ? $e : null
                );
            }
            
            return $this->errorResponse(
                'Error al actualizar el producto en la base de datos.',
                [],
                [],
                500,
                config('app.debug') ? $e : null
            );
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Unexpected error while updating a product', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'product_id' => $product->id ?? null,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al actualizar el producto.',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $productId = $product->id;
            $product->delete();
            
            DB::commit();

            Log::info('Product has been soft deleted', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'product_id' => $product->id ?? null,
            ]);
            
            return $this->deletedResponse(
                $productId,
                'Producto eliminado exitosamente.'
            );
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Error from database while trying delete a product', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'product_id' => $product->id ?? null,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'No se puede eliminar este producto porque se está utilizando en pedidos o movimientos de stock.',
                [],
                [],
                409,
                config('app.debug') ? $e : null
            );
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Unexpected error while trying to delete a product', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'product_id' => $product->id ?? null,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al eliminar el producto.',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    public function restore(Request $request,$id): JsonResponse
    {
        DB::beginTransaction();
        
        try {            
            $product = Product::onlyTrashed()->findOrFail($id);
            $product->restore();
            $product->load('category');

            DB::commit();

            Log::info('Product has been restored', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'product_id' => $product->id ?? null,
            ]);

            return $this->restoredResponse(
                $product,
                'Producto restaurado exitosamente.'
            );  
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            
            Log::error('Error trying to restore a product', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'product_id' => $product->id ?? null,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->notFoundResponse(
                'Producto eliminado no encontrado.'
            );
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Error from database trying to restore a product', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'product_id' => $id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
            ]);
            
            return $this->errorResponse(
                'Error al restaurar el producto en la base de datos.',
                [],
                [],
                500,
                config('app.debug') ? $e : null
            );
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Unexpected error trying to restore a product', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'product_id' => $product->id ?? null,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al restaurar el producto.',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    public const ALLOWED_SORT_FIELDS = [
        'code' => 'COD',   
        'id_category' => 'Categoria',   
        'sale_price' => 'Precio de venta',   
        'current_stock' => 'Stock actual',   
        'created_at' => 'Fecha de creacion',   
        'name' => 'Nombre',
        'deleted_at' => 'Fecha de eliminacion'
    ];

    public const ALLOWED_SORT_DIRECTIONS = [
        'asc' => 'Ascendente',
        'desc' => 'Descendente'
    ];

    /**
     * Get filters to be used in the index view
     */
    public function getFilters(Request $request): JsonResponse
    {
        try {
            $products = Product::select('id', 'code', 'name', 'deleted_at')->get();

            $categories = Category::select('id', 'name')->orderBy('id', 'asc')->get();

            $data = [
                'categories' => $categories,
                'products' => $products,
                'sort_by' => self::ALLOWED_SORT_FIELDS,
                'sort_direction' => self::ALLOWED_SORT_DIRECTIONS
            ];

            Log::info('Retrieved filters for products', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);
            
            return $this->successResponse(
                $data,
                'Datos para filtrar productos obtenidos exitosamente.'
            );
            
        } catch (QueryException $e) {
            Log::error('Error trying to get filters por product', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'product_id' => $product->id ?? null,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error al obtener los datos de filtros desde la base de datos.',
                [],
                [],
                500,
                config('app.debug') ? $e : null
            );
            
        } catch (Exception $e) {
            Log::error('Unexpected error trying to get the filter for products', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Se produjo un error inesperado al obtener los filtros.',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Exportar catálogo de productos a Excel
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCatalog(Request $request)
    {
        try {
            // Obtener el ID de la categoría a excluir (opcional)
            $excludeCategoryId = $request->query('exclude_category');
            
            // Validar que sea un número si se proporciona
            if ($excludeCategoryId && !is_numeric($excludeCategoryId)) {
                Log::error('Error of validation trying to export catalog excluding a category', [
                    'user_email' => $request->user()->email,
                    'ip' => $request->ip(),
                    'error' => 'Invalid parameter at exclude_category (must be a number)',
                    'data' => $request->all()
                ]);

                return $this->errorResponse('El parámetro exclude_category no es valido', ['exclude_category' => 'Debe ser un numero'], [], 400);
            }

            //Validar que el ID de la categoría a excluir exista en la base de datos
            if ($excludeCategoryId && !Category::find($excludeCategoryId)) {
                Log::error('Error of validation trying to export catalog excluding a category', [
                    'user_email' => $request->user()->email,
                    'ip' => $request->ip(),
                    'error' => 'Category not found',
                    'data' => $request->all()
                ]);

                return $this->errorResponse('El parámetro exclude_category no se encontró en la base de datos', [], [], 400);
            }

            // Generar el nombre del archivo con la fecha actual
            $fileName = 'catalogo_productos_' . date('Ymd') . '.xlsx';

            Log::info('Products catalog has been exported', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);
            
            // Descargar el archivo Excel
            return Excel::download(new CatalogExport($excludeCategoryId), $fileName, null, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Filename' => $fileName
            ]);
        } catch (\Exception $e) {
            Log::error('Error trying to export the list of contacts', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Error inesperado al exportar el listado de contactos.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
            );
        }
    }
}
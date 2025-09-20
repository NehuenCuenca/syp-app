<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApiResponseService
{
    /**
     * Respuesta de éxito estándar
     */
    public static function success(
        $data = null, 
        string $message = 'Operación exitosa', 
        array $meta = [], 
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'errors' => []
        ], $statusCode);
    }

    /**
     * Respuesta de error estándar
     */
    public static function error(
        string $message = 'Ha ocurrido un error',
        array $errors = [],
        array $meta = [],
        int $statusCode = 400
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => array_merge([
                'error_code' => 'API_ERROR',
                'timestamp' => now()->toISOString()
            ], $meta),
            'errors' => $errors
        ], $statusCode);
    }

    /**
     * Respuesta con paginación
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        string $message = 'Datos obtenidos exitosamente',
        array $additionalMeta = []
    ): JsonResponse {
        $meta = array_merge([
            'pagination' => [
                'total' => $paginator->total(),
                'count' => count($paginator->items()),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'total_pages' => $paginator->lastPage(),
                'has_more' => $paginator->hasMorePages()
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'next' => $paginator->nextPageUrl(),
                'prev' => $paginator->previousPageUrl()
            ]
        ], $additionalMeta);

        return self::success($paginator->items(), $message, $meta);
    }

    /**
     * Respuesta para crear recurso
     */
    public static function created(
        $data,
        string $message = 'Recurso creado exitosamente',
        ?string $resourceLocation = null
    ): JsonResponse {
        $meta = [];
        if ($resourceLocation) {
            $meta['resource_location'] = $resourceLocation;
        }

        return self::success($data, $message, $meta, 201);
    }

    /**
     * Respuesta para eliminación
     */
    public static function deleted(
        $deletedId = null,
        string $message = 'Recurso eliminado exitosamente',
        bool $softDelete = true
    ): JsonResponse {
        $data = [];
        $meta = ['soft_delete' => $softDelete];

        if ($deletedId) {
            $data['deleted_id'] = $deletedId;
            $data['deleted_at'] = now()->toISOString();

            if ($softDelete) {
                $meta['restore_until'] = now()->addDays(30)->toISOString();
            }
        }

        return self::success($data, $message, $meta);
    }

    /**
     * Respuesta para restauración
     */
    public static function restored(
        $data,
        string $message = 'Recurso restaurado exitosamente'
    ): JsonResponse {
        $meta = [
            'was_deleted_at' => $data['deleted_at'] ?? null
        ];

        if (isset($data['deleted_at'])) {
            unset($data['deleted_at']);
        }

        $data['restored_at'] = now()->toISOString();

        return self::success($data, $message, $meta);
    }

    /**
     * Respuesta de validación con errores
     */
    public static function validationError(
        array $errors,
        string $message = 'Error de validación'
    ): JsonResponse {
        return self::error($message, $errors, [
            'error_code' => 'VALIDATION_ERROR'
        ], 422);
    }

    /**
     * Respuesta 404
     */
    public static function notFound(
        string $message = 'Recurso no encontrado'
    ): JsonResponse {
        return self::error($message, [], [
            'error_code' => 'RESOURCE_NOT_FOUND'
        ], 404);
    }
}
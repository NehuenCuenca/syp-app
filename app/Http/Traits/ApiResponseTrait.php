<?php

namespace App\Http\Traits;

use App\Services\ApiResponseService;

/**
 * Trait ApiResponseTrait
 * 
 * Proporciona métodos helper para generar respuestas JSON consistentes
 * en todos los controladores de la API.
 * 
 * Uso:
 * class UserController extends Controller
 * {
 *     use ApiResponseTrait;
 * 
 *     public function index()
 *     {
 *         $users = User::all();
 *         return $this->successResponse($users, 'Usuarios obtenidos');
 *     }
 * }
 */
trait ApiResponseTrait
{
    /**
     * Respuesta de éxito
     */
    protected function successResponse($data = null, $message = 'Operación exitosa', $meta = [], $statusCode = 200)
    {
        return ApiResponseService::success($data, $message, $meta, $statusCode);
    }

    /**
     * Respuesta de error
     */
    protected function errorResponse($message = 'Ha ocurrido un error', $errors = [], $meta = [], $statusCode = 400)
    {
        // dd($message, $errors, $meta, $statusCode);
        return ApiResponseService::error($message, $errors, $meta, $statusCode);
    }

    /**
     * Respuesta con paginación
     */
    protected function paginatedResponse($paginator, $message = 'Datos obtenidos exitosamente', $additionalMeta = [])
    {
        return ApiResponseService::paginated($paginator, $message, $additionalMeta);
    }

    /**
     * Respuesta para recurso creado (HTTP 201)
     */
    protected function createdResponse($data, $message = 'Recurso creado exitosamente', $resourceLocation = null)
    {
        return ApiResponseService::created($data, $message, $resourceLocation);
    }

    /**
     * Respuesta para recurso eliminado
     */
    protected function deletedResponse($deletedId = null, $message = 'Recurso eliminado exitosamente', $softDelete = true)
    {
        return ApiResponseService::deleted($deletedId, $message, $softDelete);
    }

    /**
     * Respuesta para recurso restaurado
     */
    protected function restoredResponse($data, $message = 'Recurso restaurado exitosamente')
    {
        return ApiResponseService::restored($data, $message);
    }

    /**
     * Respuesta de error de validación (HTTP 422)
     */
    protected function validationErrorResponse($errors, $message = 'Error de validación')
    {
        return ApiResponseService::validationError($errors, $message);
    }

    /**
     * Respuesta para recurso no encontrado (HTTP 404)
     */
    protected function notFoundResponse($message = 'Recurso no encontrado')
    {
        return ApiResponseService::notFound($message);
    }
}
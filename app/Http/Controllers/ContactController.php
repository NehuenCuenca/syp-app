<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ContactController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of all contacts.
     */
    public function index(): JsonResponse
    {
        try {
            $contacts = Contact::select('id', 'company_name', 'contact_name', 'phone', 'contact_type')
                                ->orderBy('created_at', 'desc')->get();

            return $this->successResponse(
                $contacts,
                'Todos los contactos recuperados exitosamente.',
                ['total' => $contacts->count()]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al recuperar los contactos.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public const ALLOWED_SORT_FIELDS  = [
            'id' => 'ID', 
            'company_name' => 'Nombre de negocio', 
            'contact_name' => 'Nombre de contacto',
            'contact_type' => 'Tipo de contacto',
            'created_at' => 'Fecha de creación',
    ];

    public const ALLOWED_SORT_DIRECTIONS = [
        'asc' => 'Ascendente',
        'desc' => 'Descendente'
    ];

    public function getFilters(): JsonResponse
    {
        try {
            $contactTypes = Contact::select('contact_type')
                ->distinct()
                ->whereNotNull('contact_type')
                ->where('contact_type', '!=', '')
                ->orderBy('contact_type')
                ->pluck('contact_type');

            $contactIds = Contact::select('id')
                ->distinct()
                ->pluck('id');
                
            $filterData = [
                'contact_types' => $contactTypes,
                'contact_ids' => $contactIds,
                'sort_by' => self::ALLOWED_SORT_FIELDS,
                'sort_direction' => self::ALLOWED_SORT_DIRECTIONS
            ];

            return $this->successResponse($filterData, 'Filtros obtenidos exitosamente.');

        } catch (QueryException $e) {
            return $this->errorResponse(
                'Error al recuperar los tipos de contactos.',
                ['database_error' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error inesperado al obtener los filtros.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Display a filtered and paginated listing of contacts.
     */
    public function getFilteredContacts(Request $request): JsonResponse
    {
        try {
            $query = Contact::query();

            // Filtrar por tipo de contacto si se proporciona
            if ($request->has('contact_type')) {
                $query->where('contact_type', $request->contact_type);
            }

            // Búsqueda por nombre de empresa o contacto
            $search = $request->get('search', '');
            if ($request->has('search')) {
                // $query->where('company_name', 'like', "%{$search}%");
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                    //   ->orWhere('contact_name', 'like', "%{$search}%");
                });
            }

            // Ordenamiento
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            if (in_array($sortBy, array_keys(self::ALLOWED_SORT_FIELDS))) {
                $query->orderBy($sortBy, $sortDirection)
                    ->select('id', 'company_name', 'contact_name', 'phone', 'contact_type');
            }

            // Paginación
            $perPage = $request->get('per_page', 9);
            $contacts = $query->paginate($perPage);

            $filtersApplied = [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_direction' => $sortDirection,
                'per_page' => $perPage,
                'page' => $request->integer('page', 1)
            ];

            return $this->paginatedResponse(
                $contacts,
                'Contactos filtrados recuperados exitosamente.',
                ['filters_applied' => $filtersApplied]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al procesar la consulta de contactos.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        try {
            $contact = Contact::create($request->validated());
            return $this->createdResponse($contact, 'Contacto creado exitosamente.');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al crear el contacto.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $contact = Contact::withTrashed()->findOrFail($id);
            return $this->successResponse($contact, 'Contacto recuperado exitosamente.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Contacto no encontrado.');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al recuperar el contacto.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        try {
            $contact->update($request->validated());
            return $this->successResponse($contact->fresh(), 'Contacto actualizado exitosamente.');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al actualizar el contacto.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Contact $contact): JsonResponse
    {
        try {
            $contact->delete();
            return $this->deletedResponse($contact->id, 'Contacto eliminado exitosamente.');
        } catch (QueryException $e) {
            return $this->errorResponse(
                'No se puede eliminar este contacto porque se está utilizando en pedidos.',
                [],
                [],
                Response::HTTP_CONFLICT
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al eliminar el contacto.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Restore a soft deleted contact.
     */
    public function restore($id): JsonResponse
    {
        if (!is_numeric($id)) {
            return $this->validationErrorResponse(
                ['id' => 'ID debe ser un número válido.'],
                'Error de validación en parámetros.'
            );
        }
        
        $contact = Contact::onlyTrashed()->find($id);
        if (!$contact) {
            return $this->notFoundResponse('Contacto eliminado no encontrado.');
        }

        try {

            $contact->restore();
            return $this->restoredResponse($contact, 'Contacto restaurado exitosamente.');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Error al restaurar el contacto.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Exports\ContactsExport;
use App\Http\Requests\FilterContactsRequest;
use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of all contacts.
     */
    public function index(FilterContactsRequest $request): JsonResponse
    {
        try {
            $filters = $request->getFilters();
            $query = Contact::query()->withTrashed();

            // Filtrar por tipo de contacto si se proporciona
            if (!empty($filters['contact_type'])) {
                $query->where('contact_type', $request->contact_type);
            }

            // Búsqueda por nombre de empresa o contacto
            $search = $request->get('search', '');
            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
            }

            // Ordenamiento
            if (in_array($filters['sort_by'], array_keys(FilterContactsRequest::ALLOWED_SORT_FIELDS))) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction'])
                    ->select(
                        'id', 'code', 'name', 
                        'phone', 'contact_type', 'deleted_at'
                    );
            }

            // Paginación
            $contacts = $query->paginate($filters['per_page']);

            Log::info('Retrieved contacts filtered', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);

            return $this->paginatedResponse(
                $contacts,
                'Contactos filtrados recuperados exitosamente.',
                ['filters_applied' => $filters]
            );
        } catch (\Exception $e) {
            Log::error('Error trying to get filtered contacts ', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Error al procesar la consulta de contactos.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
            );
        }
    }

    public function getFilters(Request $request): JsonResponse
    {
        try {
            $contactTypes = Contact::select('contact_type')
                ->distinct()
                ->whereNotNull('contact_type')
                ->where('contact_type', '!=', '')
                ->orderBy('contact_type')
                ->pluck('contact_type');

            $contacts = Contact::select('id', 'code', 'name', 'deleted_at')
                ->distinct()->get()->makeHidden(['last_order', 'phone_number_info']);
                
            $filterData = [
                'contact_types' => $contactTypes,
                'contacts_alias' => $contacts,
                'sort_by' => FilterContactsRequest::ALLOWED_SORT_FIELDS,
                'sort_direction' => FilterContactsRequest::ALLOWED_SORT_DIRECTIONS
            ];

            Log::info('Retrieved filters for contacts', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse($filterData, 'Filtros obtenidos exitosamente.');
        } catch (QueryException $e) {
            Log::error('Error trying to get contact types', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Error al recuperar los tipos de contactos.',
                ['database_error' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
            );
        } catch (\Exception $e) {
            Log::error('Unexpected error trying to get the contact filters', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Error inesperado al obtener los filtros.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
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

            Log::info('Contact has been created', [
                'user_email' => $request->user()->email,
                'contact_id' => $contact->id,
                'ip' => $request->ip(),
            ]);

            return $this->createdResponse($contact, 'Contacto creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error trying to create a contact', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Error inesperado al crear el contacto.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Contact $contact): JsonResponse
    {
        try {
            // $contact = Contact::withTrashed()->findOrFail($id);

            Log::info('Contact has been shown', [
                'user_email' => $request->user()->email,
                'contact_id' => $contact->id,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse($contact, 'Contacto recuperado exitosamente.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Contact to show was not founded', [
                'user_email' => $request->user()->email,
                'contact_id' => $contact->id,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->notFoundResponse('Contacto no encontrado.');
        } catch (\Exception $e) {
            Log::error('Unexpected error trying to show a contact', [
                'user_email' => $request->user()->email,
                'contact_id' => $contact->id,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Error inesperado al recuperar el contacto.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
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

            Log::info('Contact has been updated', [
                'user_email' => $request->user()->email,
                'contact_id' => $contact->id,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse($contact->fresh(), 'Contacto actualizado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error trying updating a contact', [
                'user_email' => $request->user()->email,
                'contact_id' => $contact->id,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Error al actualizar el contacto.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Request $request, Contact $contact): JsonResponse
    {
        try {
            $contact->delete();

            Log::info('Contact has been soft deleted', [
                'user_email' => $request->user()->email,
                'contact_id' => $contact->id,
                'ip' => $request->ip(),
            ]);

            return $this->deletedResponse($contact->id, 'Contacto eliminado exitosamente.');
        } catch (QueryException $e) {
            Log::error('Error trying deleting a contact because it has orders', [
                'user_email' => $request->user()->email,
                'contact_id' => $contact->id,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'No se puede eliminar este contacto porque se está utilizando en pedidos.',
                [],
                [],
                Response::HTTP_CONFLICT,
                config('app.debug') ? $e : null
            );
        } catch (\Exception $e) {
            Log::error('Unexpeted error trying to delete a contact', [
                'user_email' => $request->user()->email,
                'contact_id' => $contact->id,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Error al eliminar el contacto.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Restore a soft deleted contact.
     */
    public function restore(Request $request,$id): JsonResponse
    {        
        $contact = Contact::onlyTrashed()->find($id);
        if (!$contact) {
            return $this->notFoundResponse('Contacto eliminado no encontrado.');
        }

        try {
            $contact->restore();

            Log::info('Contact has been restored', [
                'user_email' => $request->user()->email,
                'contact_id' => $contact->id,
                'ip' => $request->ip(),
            ]);

            return $this->restoredResponse($contact, 'Contacto restaurado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error trying to restore a contact', [
                'user_email' => $request->user()->email,
                'contact_id' => $contact->id,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Error al restaurar el contacto.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Exportar listado de contactos agrupados por tipo a Excel
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportContacts(Request $request)
    {
        try {
            $fileName = 'listado_contactos_' . date('Ymd') . '.xlsx';

            Log::info('List of contacts has been exported', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);
            
            return Excel::download(
                new ContactsExport(),
                $fileName,
                \Maatwebsite\Excel\Excel::XLSX,
                ['X-Filename' => $fileName]
            );
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
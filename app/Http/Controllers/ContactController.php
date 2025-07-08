<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ContactController extends Controller
{
    /**
     * Display a listing of all contacts.
     */
    public function index(): JsonResponse
    {
        $contacts = Contact::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $contacts,
            'message' => 'Todos los contactos recuperados exitosamente.',
            'total' => $contacts->count()
        ]);
    }

    /**
     * Display a filtered and paginated listing of contacts.
     */
    public function getFilteredContacts(Request $request): JsonResponse
    {
        $query = Contact::query();

        // Filtrar por tipo de contacto si se proporciona
        if ($request->has('contact_type')) {
            $query->where('contact_type', $request->contact_type);
        }

        // Búsqueda por nombre de empresa o contacto
        $search = $request->get('search', '');
        if ($request->has('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $contacts = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'filtered_contacts' => $contacts,
            'filters_applied' => [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'per_page' => $perPage,
                'page' => $request->integer('page', 1)
            ],
            'message' => 'Contactos filtrados recuperados exitosamente.'
        ]);
    }

    public function getContactsTypes(): JsonResponse
    {
        try {
            $contactTypes = Contact::select('contact_type')
                ->distinct()
                ->whereNotNull('contact_type')
                ->where('contact_type', '!=', '')
                ->orderBy('contact_type')
                ->pluck('contact_type');
                
            return response()->json([
                'success' => true,
                'data' => $contactTypes
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al recuperar los tipos de contactos.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        try {
            $contact = Contact::create($request->validated());
            return response()->json([
                'success' => true,
                'data' => $contact,
                'message' => 'Contacto creado exitosamente.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el contacto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $contact = Contact::withTrashed()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $contact,
            'message' => 'Contacto recuperado exitosamente.'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        $contact->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $contact->fresh(),
            'message' => 'Contacto actualizado exitosamente.'
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contacto eliminado exitosamente.'
        ]);
    }

    /**
     * Restore a soft deleted contact.
     */
    public function restore($id): JsonResponse
    {
        if (!is_numeric($id)) {
            return response()->json([
                'success' => false,
                'message' => 'ID debe ser un número válido.'
            ], 400);
        }
        
        $contact = Contact::onlyTrashed()->find($id);
        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contacto eliminado no encontrado.'
            ], 404);
        }

        $contact->restore();

        return response()->json([
            'success' => true,
            'data' => $contact,
            'message' => 'Contacto restaurado exitosamente.'
        ]);
    }
}
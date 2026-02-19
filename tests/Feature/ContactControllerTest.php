<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper para autenticar un usuario vía Sanctum.
     */
    protected function authenticate()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        return $user;
    }

    /** @test */
    public function contacts_routes_are_protected_by_sanctum()
    {
        // Index
        $this->getJson('/api/contacts')->assertStatus(401);

        // Store
        $this->postJson('/api/contacts', [])->assertStatus(401);

        // Show
        $this->getJson('/api/contacts/1')->assertStatus(401);

        // Update
        $this->putJson('/api/contacts/1', [])->assertStatus(401);

        // Destroy
        $this->deleteJson('/api/contacts/1')->assertStatus(401);

        // Filtros
        $this->getJson('/api/contacts/filters')->assertStatus(401);

        // Restore
        $this->patchJson('/api/contacts/1/restore')->assertStatus(401);

        // Export
        $this->get('/api/contacts/export', ['Accept' => 'application/json'])
                ->assertStatus(401);
    }

    /** @test */
    public function index_returns_paginated_contacts_with_valid_filters()
    {
        $this->authenticate();

        // Creamos contactos de ambos tipos
        Contact::factory()->count(3)->create(['contact_type' => 'cliente']);
        Contact::factory()->count(2)->create(['contact_type' => 'proveedor']);

        $response = $this->getJson('/api/contacts?contact_type=cliente&search=');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => [
                    'pagination' => [
                        'total',
                        'count',
                        'per_page',
                        'current_page',
                        'total_pages',
                        'has_more',
                    ],
                    'links' => [
                        'first',
                        'last',
                        'next',
                        'prev',
                    ],
                    'filters_applied',
                ],
                'errors',
            ])
            ->assertJsonPath('success', true);

        // Verificamos que todos los devueltos sean del tipo filtrado
        $this->assertTrue(
            collect($response->json('data'))
                ->every(fn ($contact) => $contact['contact_type'] === 'cliente')
        );
    }

    /** @test */
    public function index_returns_validation_error_with_invalid_filters()
    {
        $this->authenticate();

        // contact_type inválido + per_page inválido (según FilterContactsRequest)
        $response = $this->getJson('/api/contacts?contact_type=invalido&per_page=abc');

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => [
                    'error_code',
                ],
                'errors',
            ])
            ->assertJsonPath('success', false)
            ->assertJsonPath('meta.error_code', 'VALIDATION_ERROR');
    }

    /** @test */
    public function store_creates_a_contact_with_valid_data()
    {
        $this->authenticate();

        $payload = [
            'name'         => 'ACME S.A.',
            'email'        => 'acme@example.com',
            'phone'        => '123456789',
            'address'      => 'Calle Falsa 123',
            'contact_type' => 'cliente',
        ];

        $response = $this->postJson('/api/contacts', $payload);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'address',
                    'contact_type',
                ],
                'meta',
                'errors',
            ])
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'ACME S.A.');

        $this->assertDatabaseHas('contacts', [
            'name'         => 'ACME S.A.',
            'email'        => 'acme@example.com',
            'contact_type' => 'cliente',
        ]);
    }

    /** @test */
    public function store_returns_validation_error_with_invalid_data()
    {
        $this->authenticate();

        // Falta name (required) y contact_type no es válido
        $payload = [
            'name'         => '',
            'email'        => 'no-es-un-email',
            'phone'        => str_repeat('1', 51), // > max:50
            'address'      => str_repeat('a', 101), // > max:100
            'contact_type' => 'otro', // no está en Contact::getContactTypes()
        ];

        $response = $this->postJson('/api/contacts', $payload);

        $response
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('meta.error_code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['error_code'],
                'errors' => [
                    'name',
                    'email',
                    'phone',
                    'address',
                    'contact_type',
                ],
            ]);
    }

    /** @test */
    public function show_returns_error_when_contact_does_not_exist()
    {
        $this->authenticate();

        $response = $this->getJson('/api/contacts/999999');

        $response
            ->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('meta.error_code', 'RESOURCE_NOT_FOUND');
    }

    /** @test */
    public function show_returns_a_single_contact_successfully()
    {
        $this->authenticate();

        $contact = Contact::factory()->create();

        $response = $this->getJson("/api/contacts/{$contact->id}");

        $response
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $contact->id)
            ->assertJsonPath('data.name', $contact->name);
    }

    /** @test */
    public function update_returns_error_when_contact_does_not_exist()
    {
        $this->authenticate();

        $response = $this->putJson('/api/contacts/999999', [
            'name' => 'Nuevo nombre',
        ]);

        $response
            ->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('meta.error_code', 'RESOURCE_NOT_FOUND');
    }

    /** @test */
    public function update_returns_validation_error_with_invalid_data()
    {
        $this->authenticate();

        $contact = Contact::factory()->create();

        $response = $this->putJson("/api/contacts/{$contact->id}", [
            'name'         => str_repeat('a', 101), // > max:100
            'email'        => 'no-es-email',
            'phone'        => str_repeat('1', 51),
            'address'      => str_repeat('x', 101),
            'contact_type' => 'otro',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('meta.error_code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'errors' => [
                    'name',
                    'email',
                    'phone',
                    'address',
                    'contact_type',
                ],
            ]);
    }

    /** @test */
    public function update_updates_contact_successfully_with_valid_data()
    {
        $this->authenticate();

        $contact = Contact::factory()->create([
            'name'         => 'Viejo nombre',
            'contact_type' => 'cliente',
        ]);

        $payload = [
            'name'         => 'Nuevo nombre',
            'email'        => 'nuevo@example.com',
            'contact_type' => 'proveedor',
        ];

        $response = $this->putJson("/api/contacts/{$contact->id}", $payload);

        $response
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Nuevo nombre')
            ->assertJsonPath('data.email', 'nuevo@example.com')
            ->assertJsonPath('data.contact_type', 'proveedor');

        $this->assertDatabaseHas('contacts', [
            'id'           => $contact->id,
            'name'         => 'Nuevo nombre',
            'email'        => 'nuevo@example.com',
            'contact_type' => 'proveedor',
        ]);
    }

    /** @test */
    public function destroy_returns_error_when_contact_does_not_exist()
    {
        $this->authenticate();

        $response = $this->deleteJson('/api/contacts/999999');

        $response
            ->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('meta.error_code', 'RESOURCE_NOT_FOUND');
    }

    /** @test */
    public function destroy_soft_deletes_a_contact_successfully()
    {
        $this->authenticate();

        $contact = Contact::factory()->create();

        $response = $this->deleteJson("/api/contacts/{$contact->id}");

        $response
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'deleted_id',
                    'deleted_at',
                ],
                'meta' => [
                    'soft_delete',
                ],
            ]);

        $this->assertSoftDeleted('contacts', [
            'id' => $contact->id,
        ]);
    }

    /** @test */
    public function restore_returns_error_when_contact_was_not_soft_deleted_or_does_not_exist()
    {
        $this->authenticate();

        // ID inexistente o contacto no borrado
        $response = $this->patchJson('/api/contacts/999999/restore');

        $response
            ->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('meta.error_code', 'RESOURCE_NOT_FOUND');
    }

    /** @test */
    public function restore_restores_a_soft_deleted_contact_successfully()
    {
        $this->authenticate();

        $contact = Contact::factory()->create();
        $contact->delete(); // soft delete

        $response = $this->patchJson("/api/contacts/{$contact->id}/restore");

        $response
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'contact_type',
                    'restored_at',
                ],
                'meta' => [
                    'was_deleted_at',
                ],
            ]);

        // Verificamos que ya no esté borrado
        $this->assertDatabaseHas('contacts', [
            'id'         => $contact->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function get_filters_returns_expected_structure()
    {
        $this->authenticate();

        Contact::factory()->count(3)->create();

        $response = $this->getJson('/api/contacts/filters');

        $response
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'contact_types',
                    'contacts_alias',
                    'sort_by',
                    'sort_direction',
                ],
                'meta',
                'errors',
            ]);
    }

    /** @test */
    public function export_contacts_downloads_an_excel_file_successfully()
    {
        $this->authenticate();

        Contact::factory()->count(5)->create();

        $response = $this->get('/api/contacts/export');

        $response->assertStatus(200);

        $expectedFileName = 'listado_contactos_' . date('Ymd') . '.xlsx';

        // Content-Type esperado para XLSX (Maatwebsite\Excel::XLSX)
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('content-type')
        );

        // Content-Disposition debe contener el nombre del archivo
        $this->assertStringContainsString(
            $expectedFileName,
            $response->headers->get('content-disposition')
        );

        // Cabecera personalizada X-Filename enviada en el controlador
        $this->assertEquals(
            $expectedFileName,
            $response->headers->get('X-Filename')
        );
    }
}
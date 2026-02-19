<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\MovementType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper para autenticar un usuario vía Sanctum.
     */
    protected function authenticate(): User
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Sanctum::actingAs($user, ['server:read', 'server:create', 'server:update', 'server:delete', 'server:restore']);

        return $user;
    }

    /**
     * Helper para autenticar un usuario vía Sanctum SIN la habilidad server:create.
     */
    protected function authenticateWithoutCreateAbility(): User
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        // Solo lectura, sin 'server:create'
        Sanctum::actingAs($user, ['server:read']);

        return $user;
    }

    /** @test */
    public function it_denies_unauthenticated_users_for_products_routes()
    {
        // Index
        $this->getJson('/api/products')
            ->assertStatus(401);

        // Store
        $this->postJson('/api/products', [])
            ->assertStatus(401);

        // Show
        $this->getJson('/api/products/1')
            ->assertStatus(401);

        // Update
        $this->patchJson('/api/products/1', [])
            ->assertStatus(401);

        // Destroy
        $this->deleteJson('/api/products/1')
            ->assertStatus(401);

        // Restore
        $this->patchJson('/api/products/1/restore')
            ->assertStatus(401);

        // Filtros
        $this->getJson('/api/products/filters')
            ->assertStatus(401);

        // Export catálogo
        $this->get('/api/products/export-catalog', ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'No autenticado. Por favor, inicie sesión.',
            ]);
    }

    /** @test */
    public function it_returns_paginated_products_with_api_response_structure()
    {
        $this->authenticate();

        $category = Category::factory()->create();
        Product::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/products?per_page=10&sort_by=name&sort_direction=asc');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data', // array de productos
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
            ]);
    }

    /** @test */
    public function it_creates_a_product_and_returns_created_response_structure()
    {
        $this->authenticate();

        $payload = [
            'name'              => 'Producto de prueba',
            'buy_price'         => 100,
            'profit_percentage' => 20,
            'sale_price'        => 120,
            'current_stock'     => 10,
            'min_stock_alert'   => 1,
            // El controller crea Category::firstOrCreate(['name' => category])
            'category'          => 'Varios',
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Producto creado exitosamente.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'current_stock',
                    'min_stock_alert',
                    'category' => [
                        'id',
                        'name',
                    ],
                ],
                'meta',   // puede estar vacío
                'errors', // siempre array
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Producto de prueba',
        ]);
    }

    /** @test */
    public function it_shows_a_single_product_with_api_response_structure()
    {
        $this->authenticate();

        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Producto recuperado exitosamente.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'current_stock',
                    'min_stock_alert',
                    'category' => [
                        'id',
                        'name',
                    ],
                ],
                'meta',
                'errors',
            ]);
    }

    /** @test */
    public function it_updates_a_product_and_returns_success_response_structure()
    {
        $this->authenticate();

        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id'   => $category->id,
            'current_stock' => 5,
        ]);

        $payload = [
            'name'          => 'Producto actualizado',
            'current_stock' => 8,
        ];

        $response = $this->patchJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Producto actualizado exitosamente.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'current_stock',
                    'min_stock_alert',
                    'category' => [
                        'id',
                        'name',
                    ],
                ],
                'meta',
                'errors',
            ]);

        $this->assertDatabaseHas('products', [
            'id'            => $product->id,
            'name'          => 'Producto actualizado',
            'current_stock' => 8,
        ]);
    }

    /** @test */
    public function it_soft_deletes_a_product_and_returns_deleted_response_structure()
    {
        $this->authenticate();

        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Producto eliminado exitosamente.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'deleted_id',
                    'deleted_at',
                ],
                'meta' => [
                    'soft_delete',
                    // opcionalmente puede incluir restore_until
                ],
                'errors',
            ]);

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    /** @test */
    public function it_restores_a_soft_deleted_product_and_returns_restored_response_structure()
    {
        $this->authenticate();

        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category]);
        $product->delete();

        $response = $this->patchJson("/api/products/{$product->id}/restore");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Producto restaurado exitosamente.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'current_stock',
                    'min_stock_alert',
                    'category' => [
                        'id',
                        'name',
                    ],
                    'restored_at',
                ],
                'meta' => [
                    'was_deleted_at',
                ],
                'errors',
            ]);

        $this->assertDatabaseHas('products', [
            'id'         => $product->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_returns_filters_with_api_response_structure()
    {
        $this->authenticate();

        $category = Category::factory()->create(['name' => 'Alimentos']);
        $product  = Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/products/filters');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Datos para filtrar productos obtenidos exitosamente.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'categories' => [
                        [
                            'id',
                            'name',
                        ],
                    ],
                    'products' => [
                        [
                            'id',
                            'code',
                            'name',
                            'deleted_at',
                        ],
                    ],
                    'sort_by',
                    'sort_direction',
                ],
                'meta',
                'errors',
            ]);
    }

    /** @test */
    public function it_downloads_the_products_catalog_excel_successfully()
    {
        $this->authenticate();

        $response = $this->get('/api/products/export-catalog');

        $response->assertStatus(200);

        // Validar headers principales del Excel
        $contentType = $response->headers->get('content-type');
        $this->assertIsString($contentType);
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $contentType
        );

        $this->assertTrue($response->headers->has('X-Filename'));
    }

    /** @test */
    public function it_returns_error_response_when_exclude_category_is_not_numeric()
    {
        $this->authenticate();

        $response = $this->getJson('/api/products/export-catalog?exclude_category=abc');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'El parámetro exclude_category no es valido',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data', // debe ser null en errores
                'meta' => [
                    'error_code',
                    'timestamp',
                ],
                'errors' => [
                    'exclude_category',
                ],
            ]);
    }

    /** @test */
    public function it_returns_error_response_when_exclude_category_does_not_exist()
    {
        $this->authenticate();

        // No creamos ninguna categoría, así que un ID alto seguro no existe
        $response = $this->getJson('/api/products/export-catalog?exclude_category=999');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'El parámetro exclude_category no se encontró en la base de datos',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => [
                    'error_code',
                    'timestamp',
                ],
                'errors',
            ]);
    }

    /** @test */
    public function it_returns_validation_error_when_creating_product_with_invalid_fields()
    {
        $this->authenticate();

        $payload = [
            'name'              => '',          // required + string + max:100
            'buy_price'         => -10,        // numeric, min:0
            'profit_percentage' => 0,          // numeric, min:1
            'sale_price'        => -5,         // numeric, min:0, gte:buy_price
            'current_stock'     => -1,         // integer, min:0
            'min_stock_alert'   => -1,         // integer, min:0
            'category'          => str_repeat('X', 31), // string, max:30
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Los datos proporcionados no son válidos',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data', // debe ser null según ApiResponseService::error()
                'meta' => [
                    'error_code', // VALIDATION_ERROR
                    'timestamp',
                ],
                'errors' => [
                    'name',
                    'buy_price',
                    'profit_percentage',
                    'sale_price',
                    'current_stock',
                    'min_stock_alert',
                    'category',
                ],
            ]);
    }

    /** @test */
    public function it_returns_validation_error_when_updating_product_with_invalid_fields()
    {
        $this->authenticate();

        $category = Category::factory()->create();
        $product  = Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $payload = [
            'name'              => str_repeat('A', 101),  // max:100
            'buy_price'         => -1,                    // min:0
            'profit_percentage' => 0,                     // min:1
            'sale_price'        => -5,                    // min:0, gte:buy_price
            'current_stock'     => -1,                    // min:0
            'min_stock_alert'   => 0,                     // min:1
            'category'          => str_repeat('B', 31),   // max:30
        ];

        $response = $this->patchJson("/api/products/{$product->id}", $payload);
        // dd($response->json());
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Los datos proporcionados no son válidos',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => [
                    'error_code',
                    'timestamp',
                ],
                'errors' => [
                    'name',
                    'buy_price',
                    'profit_percentage',
                    'sale_price',
                    'current_stock',
                    'min_stock_alert',
                    'category',
                ],
            ]);
    }

    /** @test */
    public function it_returns_not_found_error_when_showing_a_non_existing_product()
    {
        $this->authenticate();

        $nonExistingId = 999999;

        $response = $this->getJson("/api/products/{$nonExistingId}");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Recurso no encontrado',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data', // null
                'meta' => [
                    'error_code',  // RESOURCE_NOT_FOUND
                    'timestamp',
                ],
                'errors',
            ]);
    }

    /** @test */
    public function it_returns_not_found_error_when_restoring_a_non_existing_product()
    {
        $this->authenticate();

        $nonExistingId = 999999;

        $response = $this->patchJson("/api/products/{$nonExistingId}/restore");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Producto eliminado no encontrado.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => [
                    'error_code',      // RESOURCE_NOT_FOUND
                    'timestamp',
                ],
                'errors',
            ]);
    }

    /** @test */
    public function it_returns_forbidden_when_user_without_server_create_ability_tries_to_create_a_product()
    {
        $this->authenticateWithoutCreateAbility();

        $payload = [
            'name'              => 'Producto de prueba',
            'buy_price'         => 100,
            'profit_percentage' => 20,
            'sale_price'        => 120,
            'current_stock'     => 10,
            'min_stock_alert'   => 1,
            'category'          => 'Varios',
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(403);

        // Opcional: asegurarse de que no se haya creado el producto
        $this->assertDatabaseCount('products', 0);
    }
}

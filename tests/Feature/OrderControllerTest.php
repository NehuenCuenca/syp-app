<?php

namespace Tests\Feature\Orders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Contact;
use App\Models\Category;
use App\Models\MovementType;
use App\Models\OrderDetail;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected MovementType $buyMovementType;
    protected MovementType $saleMovementType;

    protected function setUp(): void
    {
        parent::setUp();

        // Tipos de movimiento (compra / venta) según el enum del diagrama
        // Enum movement_types_name { compra, venta, ajuste_positivo, ajuste_negativo }
        $this->buyMovementType = MovementType::factory()->create([
            'name'           => 'compra',
            'increase_stock' => true,
        ]);

        $this->saleMovementType = MovementType::factory()->create([
            'name'           => 'venta',
            'increase_stock' => false,
        ]);
    }


    private function jsonStructureStandardSuccess(): array
    {
        // Estructura generada por ApiResponseService::success 
        return [
            'success',
            'message',
            'data',
            'meta',
            'errors',
        ];
    }

    private function jsonStructureStandardError(): array
    {
        // Estructura generada por ApiResponseService::error / validationError / notFound 
        return [
            'success',
            'message',
            'data',
            'meta',
            'errors',
        ];
    }

    /** @test */
    public function guest_cannot_access_protected_order_endpoints()
    {
        $order = Order::factory()->create();

        $endpoints = [
            'GET'    => [
                '/api/orders',
                '/api/orders/filters',
                '/api/orders/create',
                "/api/orders/{$order->id}",
                "/api/orders/{$order->id}/edit",
                "/api/orders/{$order->id}/details",
                "/api/orders/{$order->id}/stock-movements",
            ],
            'POST'   => ['/api/orders'],
            'PUT'    => ["/api/orders/{$order->id}"],
            'DELETE' => ["/api/orders/{$order->id}"],
        ];

        foreach ($endpoints as $method => $uris) {
            foreach ($uris as $uri) {
                $response = $this->json($method, $uri, ['Accept' => 'application/json', 'Authorization' => '']);
                $response->assertStatus(401); // No autenticado
                // dump([$method, $uri, $response->json()['message']]);
            }
        }
    }

    /** @test */
    public function index_returns_paginated_orders_with_valid_filters()
    {
        // Usuario autenticado por Sanctum
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact = Contact::factory()->create();
        $anotherContact = Contact::factory()->create();

        // Pedidos de compra y venta con distintos contactos, fechas
        $order1 = Order::factory()->create([
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->buyMovementType->id,
            'created_at'       => now()->subDays(5),
        ]);

        $order2 = Order::factory()->create([
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->saleMovementType->id,
            'created_at'       => now()->subDays(1),
        ]);

        $order3 = Order::factory()->create([
            'contact_id'       => $anotherContact->id,
            'movement_type_id' => $this->buyMovementType->id,
            'created_at'       => now()->subDays(2),
        ]);

        $filters = [
            'movement_type_id' => $this->buyMovementType->id,
            'contact_id'       => $contact->id,
            'before_equal_date' => now()->toDateString(),
            // 'search'           => 'c2', 
            'per_page'         => 10,
        ];

        $response = $this->getJson('/api/orders?' . http_build_query($filters));
        // dump(['url' => '/api/orders?' . http_build_query($filters)]);

        $response->assertOk();
        $response->assertJsonStructure([
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
        ]);

        $json = $response->json();
        // dump($json);

        // Estructura estándar de respuesta de paginación 
        $this->assertTrue($json['success']);
        $this->assertEquals('Pedidos filtrados recuperados exitosamente', $json['message']);

        // Solo debe venir order1 (compra, contacto = $contact, código c2)
        $this->assertCount(1, $json['data']);
        $this->assertEquals($order1->id, $json['data'][0]['id']);
    }

    /** @test */
    public function index_with_invalid_filters_returns_validation_error()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $filters = [
            'movement_type_id'  => 'not-an-integer',   // debe ser integer/exists
            'before_equal_date' => 'fecha-invalida',   // formato inválido
            'per_page'          => -5,                 // inválido
        ];

        $response = $this->getJson('/api/orders?' . http_build_query($filters));
        // dump(['url' => '/api/orders?' . http_build_query($filters)]);

        $response->assertStatus(422);
        $response->assertJsonStructure($this->jsonStructureStandardError());

        $json = $response->json();
        // dump($json);

        $this->assertFalse($json['success']);
        $this->assertEquals('Los datos proporcionados no son válidos', $json['message']);
        $this->assertEquals('VALIDATION_ERROR', $json['meta']['error_code']);
        $this->assertNotEmpty($json['errors']);
    }

    /** @test */
    public function index_supports_combined_filters_with_correct_results_and_pagination()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contactA = Contact::factory()->create();
        $contactB = Contact::factory()->create();

        // Tres pedidos con variaciones
        $orderA1 = Order::factory()->create([
            'contact_id'       => $contactA->id,
            'movement_type_id' => $this->buyMovementType->id,
            'created_at'       => now()->subDays(2),
        ]);

        $orderA2 = Order::factory()->create([
            'contact_id'       => $contactA->id,
            'movement_type_id' => $this->saleMovementType->id,
            'created_at'       => now()->subDays(1),
        ]);

        $orderB1 = Order::factory()->create([
            'contact_id'       => $contactB->id,
            'movement_type_id' => $this->buyMovementType->id,
            'created_at'       => now()->subDays(1),
        ]);

        $filters = [
            'movement_type_id'  => $this->buyMovementType->id,
            'contact_id'        => $contactA->id,
            'before_equal_date' => now()->toDateString(),
            'per_page'          => 10,
        ];

        $response = $this->getJson('/api/orders?' . http_build_query($filters));
        // dump(['url' => '/api/orders?' . http_build_query($filters)]);


        $response->assertOk();
        $json = $response->json();
        // dump($json);


        // Solo debe aparecer orderA1
        $this->assertCount(1, $json['data']);
        $this->assertEquals($orderA1->id, $json['data'][0]['id']);

        // Paginación consistente
        $this->assertEquals(1, $json['meta']['pagination']['total']);
        $this->assertEquals(1, $json['meta']['pagination']['count']);
        $this->assertEquals(1, $json['meta']['pagination']['total_pages']);
    }

    /** @test */
    public function index_before_equal_date_filter_includes_orders_up_to_and_including_the_given_date()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact = Contact::factory()->create();

        $dateExact   = now()->subDays(2)->startOfDay();
        $dateBefore  = $dateExact->copy()->subDay();
        $dateAfter   = $dateExact->copy()->addDay();
        // dump([ 'dateExact' =>$dateExact->toString(), 'dateBefore' =>$dateBefore->toString(), 'dateAfter' =>$dateAfter->toString() ]);

        $orderBefore = Order::factory()->create([
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->buyMovementType->id,
            'created_at'       => $dateBefore,
        ]);

        $orderExact = Order::factory()->create([
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->buyMovementType->id,
            'created_at'       => $dateExact,
        ]);

        $orderAfter = Order::factory()->create([
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->buyMovementType->id,
            'created_at'       => $dateAfter,
        ]);

        // Caso 1: before_equal_date = fecha exacta → incluye before + exact, excluye after
        $filters = [
            'movement_type_id'  => $this->buyMovementType->id,
            'before_equal_date' => $dateExact->toDateString(),
            'per_page'          => 10,
        ];

        $response = $this->getJson('/api/orders?' . http_build_query($filters));
        // dump(['url' => '/api/orders?' . http_build_query($filters)]);
        $ids = collect($response->json('data'))->pluck('id');

        $this->assertTrue($ids->contains($orderBefore->id));
        $this->assertTrue($ids->contains($orderExact->id));
        $this->assertFalse($ids->contains($orderAfter->id));

        // Caso 2: before_equal_date = fecha anterior → solo incluye orderBefore
        $filters['before_equal_date'] = $dateBefore->toDateString();
        $response = $this->getJson('/api/orders?' . http_build_query($filters));
        // dump(['url' => '/api/orders?' . http_build_query($filters)]);
        $ids = collect($response->json('data'))->pluck('id');

        $this->assertTrue($ids->contains($orderBefore->id));
        $this->assertFalse($ids->contains($orderExact->id));
        $this->assertFalse($ids->contains($orderAfter->id));
    }

    /** @test */
    public function create_returns_contacts_products_and_movement_types()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        Contact::factory()->count(3)->create();
        Product::factory()->count(5)->create();

        $response = $this->getJson('/api/orders/create');

        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureStandardSuccess());

        $json = $response->json();
        // dump($json);

        $this->assertTrue($json['success']);
        $this->assertEquals('Datos para crear pedido obtenidos exitosamente', $json['message']);

        $this->assertArrayHasKey('order_types', $json['data']);
        $this->assertArrayHasKey('contacts', $json['data']);
        $this->assertArrayHasKey('products', $json['data']);

        // Tipos de movimiento: compra y venta
        $this->assertCount(2, $json['data']['order_types']);
    }

    /** @test */
    public function store_creates_purchase_order_and_increases_stock()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact = Contact::factory()->create();
        $products = Product::factory()->count(5)->create([
            'current_stock' => 0,
        ]);

        $orderDetails = $products->map(function (Product $product) {
            return [
                'product_id'          => $product->id,
                'quantity'            => 2,
                'unit_price'          => 100,
                'percentage_applied'  => 0,
            ];
        })->toArray();

        $payload = [
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->buyMovementType->id,
            'notes'            => 'Compra inicial',
            'adjustment_amount' => 0,
            'order_details'    => $orderDetails,
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure($this->jsonStructureStandardSuccess());

        $json = $response->json();
        // dump($json);
        $this->assertTrue($json['success']);
        $this->assertEquals('Pedido creado exitosamente', $json['message']);

        $orderId = $json['data']['id'];

        // Se crearon detalles y movimientos de stock (uno por producto)
        $this->assertDatabaseCount('order_details', 5);
        $this->assertDatabaseCount('stock_movements', 5);

        foreach ($products as $product) {
            $this->assertDatabaseHas('order_details', [
                'order_id'   => $orderId,
                'product_id' => $product->id,
            ]);

            // Stock debe haber aumentado en 2 para cada producto
            $this->assertDatabaseHas('products', [
                'id'            => $product->id,
                'current_stock' => 2,
            ]);
        }
    }

    /** @test */
    public function store_creates_sale_order_and_decreases_stock()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact = Contact::factory()->create();
        $products = Product::factory()->count(5)->create([
            'current_stock' => 10,
        ]);

        $orderDetails = $products->map(function (Product $product) {
            return [
                'product_id'          => $product->id,
                'quantity'            => 3,
                'unit_price'          => 200,
                'percentage_applied'  => 0,
            ];
        })->toArray();

        $payload = [
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->saleMovementType->id,
            'notes'            => 'Venta inicial',
            'adjustment_amount' => 0,
            'order_details'    => $orderDetails,
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201);

        $json = $response->json();
        // dump($json);
        $orderId = $json['data']['id'];

        foreach ($products as $product) {
            // Stock debe disminuir de 10 a 7 (10 - 3)
            $this->assertDatabaseHas('products', [
                'id'            => $product->id,
                'current_stock' => 7,
            ]);

            $this->assertDatabaseHas('order_details', [
                'order_id'   => $orderId,
                'product_id' => $product->id,
            ]);

            $this->assertDatabaseHas('stock_movements', [
                'order_id'   => $orderId,
                'product_id' => $product->id,
                // quantity_moved negativo para ventas 
            ]);
        }
    }

    /** @test */
    public function store_allows_creating_order_with_new_contact_name()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $products = Product::factory()->count(2)->create([
            'current_stock' => 10,
        ]);

        $orderDetails = $products->map(function (Product $product) {
            return [
                'product_id'          => $product->id,
                'quantity'            => 1,
                'unit_price'          => 100,
                'percentage_applied'  => 0,
            ];
        })->toArray();

        $payload = [
            // contact_id omitido
            'new_contact_name' => 'Cliente Nuevo API',
            'movement_type_id' => $this->saleMovementType->id,
            'notes'            => 'Venta a cliente nuevo',
            'adjustment_amount' => 0,
            'order_details'    => $orderDetails,
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201);

        $json = $response->json();
        // dump($json);

        $orderId = $json['data']['id'];
        $contactId = $json['data']['contact_id'];

        $this->assertDatabaseHas('contacts', [
            'id'   => $contactId,
            'name' => 'Cliente Nuevo API',
        ]);

        $this->assertDatabaseHas('orders', [
            'id'         => $orderId,
            'contact_id' => $contactId,
        ]);
    }

    /** @test */
    public function store_fails_with_invalid_payload_and_returns_validation_error()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $payload = [
            // Falta contact_id y new_contact_name => required_without
            'movement_type_id' => 999999, // no existe en movement_types
            'order_details'    => [],     // min:1
            'sub_total'        => 1000,   // debe ser missing
            'total_net'        => 1000,   // debe ser missing 
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422);
        $response->assertJsonStructure($this->jsonStructureStandardError());

        $json = $response->json();
        // dump($json);

        $this->assertFalse($json['success']);
        $this->assertEquals('VALIDATION_ERROR', $json['meta']['error_code']);
        $this->assertArrayHasKey('order_details', $json['errors']);
    }

    /** @test */
    public function store_fails_when_product_does_not_exist()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact = Contact::factory()->create();

        $payload = [
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->buyMovementType->id,
            'order_details'    => [
                [
                    'product_id'         => 999999, // inexistente
                    'quantity'           => 1,
                    'unit_price'         => 100,
                    'percentage_applied' => 0,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422);
        $json = $response->json();
        // dump($json);

        $this->assertEquals('VALIDATION_ERROR', $json['meta']['error_code']);
        $this->assertArrayHasKey('order_details.0.product_id', $json['errors']);
    }

    /** @test */
    public function store_fails_when_products_are_duplicated_in_order_details()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact  = Contact::factory()->create();
        $product  = Product::factory()->create(['current_stock' => 10]);

        $payload = [
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->saleMovementType->id,
            'order_details'    => [
                [
                    'product_id'         => $product->id,
                    'quantity'           => 1,
                    'unit_price'         => 100,
                    'percentage_applied' => 0,
                ],
                [
                    'product_id'         => $product->id, // duplicado
                    'quantity'           => 1,
                    'unit_price'         => 100,
                    'percentage_applied' => 0,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        // Validación adicional de "productos no duplicados" en StoreOrderRequest
        $response->assertStatus(422);
        $json = $response->json();
        // dump($json);

        $this->assertEquals('VALIDATION_ERROR', $json['meta']['error_code']);
    }


    /** @test */
    public function store_purchase_updates_product_profit_and_sale_price_when_percentage_applied()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact = Contact::factory()->create();

        // Producto inicial sin precios “reales”
        $product = Product::factory()->create([
            'buy_price'         => 0,
            'profit_percentage' => 0,
            'sale_price'        => 0,
            'current_stock'     => 0,
        ]);

        $unitPrice         = 100;   // nuevo precio de compra
        $quantityProducts  = 2;
        $profitPercentage  = 50;

        $payload = [
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->buyMovementType->id,
            'notes'            => 'Compra con actualización de profit',
            'adjustment_amount' => 0,
            'order_details'    => [
                [
                    'product_id'         => $product->id,
                    'quantity'           => $quantityProducts,
                    'unit_price'         => $unitPrice,
                    'percentage_applied' => $profitPercentage,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);
        $response->assertStatus(201);

        $product->refresh();

        $this->assertEquals($unitPrice, $product->buy_price, 'El buy_price del producto no se actualizó');
        $this->assertEquals($profitPercentage, $product->profit_percentage, 'El profit_percentage no se actualizó');

        // Regla de negocio esperada (ajústala si tu dominio usa otra fórmula)
        $expectedSalePrice = $unitPrice * (1 + $profitPercentage / 100);
        // dump(['expectedSalePrice' => $expectedSalePrice, 'productSalePrice' =>$product->sale_price]);

        $this->assertEquals(
            $expectedSalePrice,
            $product->sale_price,
            'El sale_price no coincide con buy_price * profit_percentage'
        );
    }


    /** @test */
    public function show_returns_not_found_for_non_existing_order()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->getJson('/api/orders/999999');

        $response->assertStatus(404);
        $response->assertJsonStructure($this->jsonStructureStandardError());

        $json = $response->json();
        // dump($json);
        $this->assertFalse($json['success']);
        $this->assertEquals('RESOURCE_NOT_FOUND', $json['meta']['error_code']); // notFound() 
    }

    /** @test */
    public function store_sale_applies_percentage_discount_on_line_subtotal_and_order_totals()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact = Contact::factory()->create();
        $product = Product::factory()->create([
            'current_stock' => 10,
        ]);

        $unitPrice        = 100;
        $quantity         = 2;
        $percentage       = 10; // 10% de descuento

        $payload = [
            'contact_id'        => $contact->id,
            'movement_type_id'  => $this->saleMovementType->id,
            'notes'             => 'Venta con descuento',
            'adjustment_amount' => 0,
            'order_details'     => [
                [
                    'product_id'         => $product->id,
                    'quantity'           => $quantity,
                    'unit_price'         => $unitPrice,
                    'percentage_applied' => $percentage,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);
        $response->assertStatus(201);
        // dump($response->json());

        $orderId = $response->json('data.id');
        $order   = Order::findOrFail($orderId);
        $detail  = OrderDetail::where('order_id', $orderId)->firstOrFail();

        // Cálculos esperados según la regla de negocio
        $lineSubtotalRaw      = $unitPrice * $quantity;
        $lineDiscount         = (int)($lineSubtotalRaw * ($percentage / 100));
        $lineSubtotalNet      = $lineSubtotalRaw - $lineDiscount;
        // dump(['lineSubtotalRaw' => $lineSubtotalRaw, 'lineDiscount' => $lineDiscount, 'lineSubtotalNet' => $lineSubtotalNet, ]);

        $this->assertEquals(
            $lineSubtotalNet,
            $detail->line_subtotal,
            'El line_subtotal no refleja correctamente el descuento aplicado'
        );

        // Como solo hay 1 detalle y adjustment_amount = 0:
        $this->assertEquals($lineSubtotalNet, $order->subtotal);
        $this->assertEquals($lineSubtotalNet, $order->total_net);
    }

    /** @test */
    public function store_calculates_financial_totals_consistently_between_api_and_database()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact = Contact::factory()->create();
        $product1 = Product::factory()->create(['current_stock' => 10]);
        $product2 = Product::factory()->create(['current_stock' => 10]);

        $adjustmentAmount = -50; // descuento global, por ejemplo

        $payload = [
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->saleMovementType->id,
            'notes'            => 'Venta con ajuste',
            'adjustment_amount' => $adjustmentAmount,
            'order_details'    => [
                [
                    'product_id'         => $product1->id,
                    'quantity'           => 2,
                    'unit_price'         => 100,
                    'percentage_applied' => 0,
                ],
                [
                    'product_id'         => $product2->id,
                    'quantity'           => 1,
                    'unit_price'         => 200,
                    'percentage_applied' => 0,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);
        $response->assertStatus(201);

        $jsonOrder = $response->json('data');
        // dump($jsonOrder);
        $orderId   = $jsonOrder['id'];

        $order = Order::with('orderDetails')->findOrFail($orderId);

        $subtotalFromDetails = $order->orderDetails->sum('line_subtotal');

        // 1) Relación matemática en BD
        $this->assertEquals($subtotalFromDetails, $order->subtotal);
        $this->assertEquals(
            $order->subtotal + $order->adjustment_amount,
            $order->total_net
        );

        // 2) API responde lo mismo que la BD
        $this->assertEquals($order->subtotal, $jsonOrder['subtotal']);
        $this->assertEquals($order->adjustment_amount, $jsonOrder['adjustment_amount']);
        $this->assertEquals($order->total_net, $jsonOrder['total_net']);
    }

    /** @test */
    public function store_rejects_orders_with_invalid_prices_and_percentages()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact = Contact::factory()->create();
        $product = Product::factory()->create(['current_stock' => 10]);

        $basePayload = [
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->saleMovementType->id,
        ];

        // 1) Precio negativo
        $payloadNegativePrice = $basePayload + [
            'order_details' => [[
                'product_id'         => $product->id,
                'quantity'           => 1,
                'unit_price'         => -10,       // inválido
                'percentage_applied' => 0,
            ]],
        ];

        $response = $this->postJson('/api/orders', $payloadNegativePrice);
        // dump([ 'negative_unit_price_response' => $response->json() ]);
        $response->assertStatus(422);

        // 2) Precio excesivamente alto (por encima del max:9999999)
        $payloadTooHighPrice = $basePayload + [
            'order_details' => [[
                'product_id'         => $product->id,
                'quantity'           => 1,
                'unit_price'         => 10000000,  // inválido
                'percentage_applied' => 0,
            ]],
        ];

        $response = $this->postJson('/api/orders', $payloadTooHighPrice);
        // dump([ 'price_too_high_response' => $response->json() ]);
        $response->assertStatus(422);

        // 3) Porcentaje negativo
        $payloadNegativePercentage = $basePayload + [
            'order_details' => [[
                'product_id'         => $product->id,
                'quantity'           => 1,
                'unit_price'         => 100,
                'percentage_applied' => -5,        // inválido
            ]],
        ];

        $response = $this->postJson('/api/orders', $payloadNegativePercentage);
        // dump([ 'negative_percentage_response' => $response->json() ]);
        $response->assertStatus(422);

        // No se debe haber creado ningún pedido
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_details', 0);
    }

    /** @test */
    public function store_rejects_orders_with_invalid_quantities()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact = Contact::factory()->create();
        $product = Product::factory()->create(['current_stock' => 10]);

        $basePayload = [
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->saleMovementType->id,
        ];

        // quantity = 0
        $payloadZeroQty = $basePayload + [
            'order_details' => [[
                'product_id'         => $product->id,
                'quantity'           => 0,
                'unit_price'         => 100,
                'percentage_applied' => 0,
            ]],
        ];

        $this->postJson('/api/orders', $payloadZeroQty)
            ->assertStatus(422);

        // quantity < 0
        $payloadNegativeQty = $basePayload + [
            'order_details' => [[
                'product_id'         => $product->id,
                'quantity'           => -1,
                'unit_price'         => 100,
                'percentage_applied' => 0,
            ]],
        ];

        $this->postJson('/api/orders', $payloadNegativeQty)
            ->assertStatus(422);

        // quantity extremadamente alta (según la regla que definas, aquí solo se ilustra)
        $payloadHugeQty = $basePayload + [
            'order_details' => [[
                'product_id'         => $product->id,
                'quantity'           => 999999999, // puedes querer bloquearlo con regla custom
                'unit_price'         => 100,
                'percentage_applied' => 0,
            ]],
        ];

        // Si decides que esto debe ser inválido, deberás ajustar el FormRequest.
        // Por ahora asumimos que también debe disparar 422:
        $this->postJson('/api/orders', $payloadHugeQty)
            ->assertStatus(422);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_details', 0);
    }

    /** @test */
    public function show_returns_purchase_and_sale_orders_successfully()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact = Contact::factory()->create();
        $product = Product::factory()->create();

        $purchaseOrder = Order::factory()->create([
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->buyMovementType->id,
        ]);

        OrderDetail::factory()->create([
            'order_id'   => $purchaseOrder->id,
            'product_id' => $product->id,
        ]);

        $saleOrder = Order::factory()->create([
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->saleMovementType->id,
        ]);

        OrderDetail::factory()->create([
            'order_id'   => $saleOrder->id,
            'product_id' => $product->id,
        ]);

        // Pedido de compra
        $responsePurchase = $this->getJson("/api/orders/{$purchaseOrder->id}");
        // dump(['responsePurchase' => $responsePurchase->json()]);
        $responsePurchase->assertOk();
        $responsePurchase->assertJsonStructure($this->jsonStructureStandardSuccess());

        // Pedido de venta
        $responseSale = $this->getJson("/api/orders/{$saleOrder->id}");
        // dump(['responseSale' => $responseSale->json()]);
        $responseSale->assertOk();
        $responseSale->assertJsonStructure($this->jsonStructureStandardSuccess());
    }

    /** @test */
    public function edit_returns_order_contacts_products_and_movement_types()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        Contact::factory()->count(3)->create();
        $category = Category::factory()->create();
        $products = Product::factory()->count(2)->create([
            'category_id' => $category->id,
        ]);

        $order = Order::factory()->create([
            'movement_type_id' => $this->saleMovementType->id,
        ]);

        $details = $products->map(fn(Product $product) => OrderDetail::factory()->create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
        ]));

        $response = $this->getJson("/api/orders/{$order->id}/edit");

        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureStandardSuccess());

        $json = $response->json();
        // dump($json);

        $this->assertArrayHasKey('order', $json['data']);
        $this->assertArrayHasKey('contacts', $json['data']);
        $this->assertArrayHasKey('products', $json['data']);
        $this->assertArrayHasKey('order_types', $json['data']);
    }

    /** @test */
    public function update_returns_not_found_for_non_existing_order()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $payload = [
            'notes' => 'Nueva nota',
        ];

        $response = $this->putJson('/api/orders/999999', $payload);
        // dump($response->json());

        $response->assertStatus(404);
        $response->assertJsonStructure($this->jsonStructureStandardError());
    }

    /** @test */
    public function update_fails_for_purchase_order_with_invalid_data()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $order = Order::factory()->create([
            'movement_type_id' => $this->buyMovementType->id,
        ]);

        $payload = [
            'contact_id'       => 'no-integer',
            'new_contact_name' => null,
            'order_details'    => [], // min:1
            'movement_type_id' => $this->saleMovementType->id, // debe ser missing en UpdateOrderRequest
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $payload);
        // dump($response->json());

        $response->assertStatus(422);
        $response->assertJsonStructure($this->jsonStructureStandardError());
    }

    /** @test */
    public function update_fails_for_sale_order_with_invalid_data()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $order = Order::factory()->create([
            'movement_type_id' => $this->saleMovementType->id,
        ]);

        $payload = [
            'contact_id' => null,
            'notes'      => str_repeat('a', 300), // max:255
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $payload);
        // dump($response->json());

        $response->assertStatus(422);
        $response->assertJsonStructure($this->jsonStructureStandardError());
    }

    /** @test */
    public function update_purchase_order_reverts_old_stock_and_recalculates_totals()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact = Contact::factory()->create();
        $productsOld = Product::factory()->count(2)->create(['current_stock' => 0]);
        $productsNew = Product::factory()->count(2)->create(['current_stock' => 0]);

        // Crear pedido de compra inicial
        $order = Order::factory()->create([
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->buyMovementType->id,
            'adjustment_amount' => 0,
        ]);

        $oldDetailsPayload = $productsOld->map(function (Product $product) {
            return [
                'product_id'         => $product->id,
                'quantity'           => 5,
                'unit_price'         => 100,
                'percentage_applied' => 0,
            ];
        })->toArray();

        // Creamos el pedido vía servicio o directamente vía store endpoint,
        // pero para simplificar asumimos que ya existen detalles y movimientos
        foreach ($oldDetailsPayload as $detail) {
            $createdDetail = OrderDetail::factory()->create([
                'order_id'         => $order->id,
                'product_id'       => $detail['product_id'],
                'quantity'         => $detail['quantity'],
                'unit_price'       => $detail['unit_price'],
                'percentage_applied' => $detail['percentage_applied'],
            ]);

            // Simulamos movimientos y stock (como lo hace OrderService::createStockMovement) 
            StockMovement::factory()->create([
                'order_id'        => $order->id,
                'order_detail_id' => $createdDetail->id,
                'product_id'      => $detail['product_id'],
                'movement_type_id' => $this->buyMovementType->id,
                'quantity_moved'  => $detail['quantity'], // +5
            ]);

            Product::where('id', $detail['product_id'])->increment('current_stock', $detail['quantity']);
        }

        $order->refresh();
        $oldTotalNet = $order->total_net ?? 0;

        // Nuevo payload con otros productos
        $newDetailsPayload = $productsNew->map(function (Product $product) {
            return [
                'product_id'         => $product->id,
                'quantity'           => 3,
                'unit_price'         => 200,
                'percentage_applied' => 0,
            ];
        })->toArray();

        $payload = [
            'contact_id'    => $contact->id,
            'notes'         => 'Actualización compra',
            'adjustment_amount' => 50,
            'order_details' => $newDetailsPayload,
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $payload);
        // dump($response->json());

        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureStandardSuccess());

        $order->refresh();

        // Stock de productos reemplazados debe haberse revertido (decrementado)
        foreach ($productsOld as $oldProduct) {
            $this->assertDatabaseHas('products', [
                'id'            => $oldProduct->id,
                'current_stock' => 0, // se revierte la compra
            ]);
        }

        // Stock de nuevos productos debe haber aumentado
        foreach ($productsNew as $newProduct) {
            $this->assertDatabaseHas('products', [
                'id'            => $newProduct->id,
                'current_stock' => 3,
            ]);
        }

        // Totales recalculados (distinto al anterior)
        // dump(['old_total_order' => $oldTotalNet, 'updated_total_order' => $order->total_net]);
        $this->assertNotEquals($oldTotalNet, $order->total_net);
    }

    /** @test */
    public function destroy_deletes_order_details_and_stock_movements_and_reverts_stock()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact  = Contact::factory()->create();
        $product  = Product::factory()->create(['current_stock' => 0]);

        $order = Order::factory()->create([
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->buyMovementType->id,
        ]);

        $detail = OrderDetail::factory()->create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => 5,
        ]);

        $movement = StockMovement::factory()->create([
            'order_id'        => $order->id,
            'order_detail_id' => $detail->id,
            'product_id'      => $product->id,
            'movement_type_id' => $this->buyMovementType->id,
            'quantity_moved'  => 5,
        ]);

        // Simulamos el stock después de la compra
        $product->increment('current_stock', 5);

        $response = $this->deleteJson("/api/orders/{$order->id}");
        // dump($response->json());

        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureStandardSuccess());

        // dump([
        //     'order_exists' => Order::find($order->id) ? true : false,
        //     'detail_exists' => OrderDetail::find($detail->id) ? true : false,
        //     'stock_movement_exists' => StockMovement::find($movement->id) ? true : false,
        // ]);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('order_details', ['id' => $detail->id]);
        $this->assertDatabaseMissing('stock_movements', ['id' => $movement->id]);

        // Stock revertido
        $this->assertDatabaseHas('products', [
            'id'            => $product->id,
            'current_stock' => 0,
        ]);
    }

    /** @test */
    public function destroy_returns_not_found_for_non_existing_order()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->deleteJson('/api/orders/999999');
        // dump($response->json());

        $response->assertStatus(404);
        $response->assertJsonStructure($this->jsonStructureStandardError());
    }

    /** @test */
    public function get_order_details_returns_details_with_product_and_category()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $category = Category::factory()->create();
        $product  = Product::factory()->create(['category_id' => $category->id]);
        $order    = Order::factory()->create();
        $detail   = OrderDetail::factory()->create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
        ]);

        $response = $this->getJson("/api/orders/{$order->id}/details");
        
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureStandardSuccess());
        
        $json = $response->json();
        // dump($json);

        $this->assertTrue($json['success']);
        $this->assertNotEmpty($json['data']);
        $this->assertArrayHasKey('product', $json['data'][0]);
        $this->assertArrayHasKey('category', $json['data'][0]['product']);
    }

    /** @test */
    public function get_stock_movements_returns_movements_with_product_and_category()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $category = Category::factory()->create();
        $product  = Product::factory()->create(['category_id' => $category->id]);
        $order    = Order::factory()->create();
        $detail   = $order->orderDetails()->create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'unit_price' => 500,
            'percentage_applied' => 0,
            'line_subtotal'   => 1000,
        ]);
        $movement = StockMovement::factory()->create([
            'order_id'        => $order->id,
            'order_detail_id' => $detail->id,
            'product_id'      => $product->id,
            'movement_type_id' => $this->saleMovementType->id,
            'quantity_moved'  => -2,
        ]);

        $response = $this->getJson("/api/orders/{$order->id}/stock-movements");

        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureStandardSuccess());

        $json = $response->json();
        // dump($json);

        $this->assertTrue($json['success']);
        $this->assertNotEmpty($json['data']);
        $this->assertArrayHasKey('product', $json['data'][0]);
        $this->assertArrayHasKey('category', $json['data'][0]['product']);
    }

    /** @test */
    public function historical_order_is_accessible_even_if_product_is_soft_deleted()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $contact  = Contact::factory()->create();
        $product  = Product::factory()->create(['current_stock' => 10]);
        $category = Category::factory()->create();
        $product->update(['category_id' => $category->id]);

        // Crear pedido de venta
        $payload = [
            'contact_id'       => $contact->id,
            'movement_type_id' => $this->saleMovementType->id,
            'order_details'    => [[
                'product_id'         => $product->id,
                'quantity'           => 1,
                'unit_price'         => 100,
                'percentage_applied' => 0,
            ]],
        ];

        $createdOrderResponse = $this->postJson('/api/orders', $payload)->assertStatus(201);
        $orderId  = $createdOrderResponse->json('data.id');

        // Soft delete del producto
        $product->delete();

        // 1) show()
        $showedOrderResponse = $this->getJson("/api/orders/{$orderId}");
        // dump(['showedOrderResponse' => $showedOrderResponse->json()]);
        $showedOrderResponse->assertOk();
        $showedOrderResponse->assertJsonStructure($this->jsonStructureStandardSuccess());

        // 2) getOrderDetails()
        $showedOrderDetailsResponse = $this->getJson("/api/orders/{$orderId}/details");
        // dump(['showedOrderDetailsResponse' => $showedOrderDetailsResponse->json()]);
        $showedOrderDetailsResponse->assertOk();
        $showedOrderDetailsResponse->assertJsonStructure($this->jsonStructureStandardSuccess());
    }
}

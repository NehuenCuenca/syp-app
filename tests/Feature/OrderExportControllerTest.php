<?php

namespace Tests\Feature\Orders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\MovementType;
use App\Services\OrderExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderExportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected MovementType $saleMovementType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $this->saleMovementType = MovementType::factory()->create([
            'name'           => 'venta',
            'increase_stock' => false,
        ]);
    }

    /** @test */
    public function can_export_sale_order_to_excel()
    {
        Storage::fake('local');

        $category = Category::factory()->create([
            'name' => Category::SPECIAL_CATEGORY ?? 'Analgésicos',
        ]);

        $product = Product::factory()->create(['category_id' => $category->id]);
        $order   = Order::factory()->create([
            'movement_type_id' => $this->saleMovementType->id,
        ]);

        // Mock del servicio de exportación
        $this->mock(OrderExportService::class, function ($mock) use ($order) {
            $mock->shouldReceive('isOrderExportable')
                ->with($order->id)
                ->andReturn(true);

            $mock->shouldReceive('hasCategory')
                ->andReturn(false);

            $filePath = Storage::disk('local')->path('test-order.xlsx');
            file_put_contents($filePath, 'dummy content');

            $mock->shouldReceive('exportOrderToExcel')
                ->with($order->id, true, 'PRESUPUESTO X')
                ->andReturn($filePath);

            $mock->shouldReceive('generateFileName')
                ->andReturn('pedido.xlsx');
        });

        $response = $this->get("/api/orders/{$order->id}/export-ticket?include_header=1&ticket_type=PRESUPUESTO%20X");
        // dump($response);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('X-Filename', 'pedido.xlsx');
    }

    /** @test */
    public function cannot_export_non_exportable_order_returns_422()
    {
        $order = Order::factory()->create([
            'movement_type_id' => $this->saleMovementType->id,
        ]);

        $this->mock(OrderExportService::class, function ($mock) use ($order) {
            $mock->shouldReceive('isOrderExportable')
                ->with($order->id)
                ->andReturn(false);
        });

        $response = $this->get("/api/orders/{$order->id}/export-ticket");

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'meta',
            'errors',
        ]);

        $json = $response->json();
        // dump(['sale_order' => $order, 'json_resp' => $json]);

        $this->assertFalse($json['success']);
        $this->assertEquals('El pedido no cumple con los criterios para exportación', $json['message']);
    }

    /** @test */
    public function cannot_export_non_existing_order_returns_404()
    {
        $response = $this->get('/api/orders/999999/export-ticket');
        // dump($response->json());

        $response->assertStatus(404);
    }

    /** @test */
    public function check_order_exportability_returns_success_for_exportable_order()
    {
        $order = Order::factory()->create([
            'movement_type_id' => $this->saleMovementType->id,
        ]);

        $ticketTypes = ['PRESUPUESTO X', 'TICKET FISCAL'];

        $this->mock(OrderExportService::class, function ($mock) use ($order, $ticketTypes) {
            $mock->shouldReceive('isOrderExportable')
                ->with($order->id)
                ->andReturn(true);

            $mock->shouldReceive('getTicketTypes')
                ->andReturn($ticketTypes);
        });

        $response = $this->getJson("/api/orders/{$order->id}/check-exportable");

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'is_exportable',
                'ticket_types',
                'order_id',
            ],
            'meta' => [
                'checked_at',
            ],
            'errors',
        ]);

        $json = $response->json();
        // dump($json);
        $this->assertTrue($json['success']);
        $this->assertTrue($json['data']['is_exportable']);
        $this->assertEquals($ticketTypes, $json['data']['ticket_types']);
    }

    /** @test */
    public function check_order_exportability_returns_error_for_non_exportable_order()
    {
        $order = Order::factory()->create([
            'movement_type_id' => $this->saleMovementType->id,
        ]);

        $ticketTypes = ['PRESUPUESTO X', 'TICKET FISCAL'];

        $this->mock(OrderExportService::class, function ($mock) use ($order, $ticketTypes) {
            $mock->shouldReceive('isOrderExportable')
                ->with($order->id)
                ->andReturn(false);

            $mock->shouldReceive('getTicketTypes')
                ->andReturn($ticketTypes);
        });

        $response = $this->getJson("/api/orders/{$order->id}/check-exportable");

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'meta' => [
                'is_exportable',
                'ticket_types',
                'order_id',
            ],
            'errors',
        ]);

        $json = $response->json();
        // dump($json);
        $this->assertFalse($json['success']);
        $this->assertEquals('El pedido no cumple con los criterios para exportación', $json['message']);
        $this->assertFalse($json['meta']['is_exportable']);
    }

    /** @test */
    public function historical_order_can_be_exported_even_if_product_is_soft_deleted()
    {
        $category = Category::factory()->create([
            'name' => Category::SPECIAL_CATEGORY ?? 'Analgésicos',
        ]);

        $product = Product::factory()->create(['category_id' => $category->id]);
        $order   = Order::factory()->create([
            'movement_type_id' => $this->saleMovementType->id,
        ]);

        // Soft delete del producto después de creado el pedido
        $product->delete();

        $this->mock(OrderExportService::class, function ($mock) use ($order) {
            $mock->shouldReceive('isOrderExportable')
                ->with($order->id)
                ->andReturn(true);

            $mock->shouldReceive('hasCategory')
                ->andReturn(false);

            $filePath = Storage::disk('local')->path('historical-order.xlsx');
            file_put_contents($filePath, 'dummy');

            $mock->shouldReceive('exportOrderToExcel')
                ->with($order->id, true, 'PRESUPUESTO X')
                ->andReturn($filePath);

            $mock->shouldReceive('generateFileName')
                ->andReturn('historico.xlsx');
        });

        $response = $this->get("/api/orders/{$order->id}/export-ticket");
        // dump($response);
        $response->assertOk();
    }

    /** @test */
    public function export_sale_order_with_special_category_disables_header_in_excel()
    {
        Storage::fake('local');

        $category = Category::factory()->create([
            'name' => Category::SPECIAL_CATEGORY, // "Analgesicos"/"Analgésicos"
        ]);

        $product = Product::factory()->create(['category_id' => $category->id]);

        $order = Order::factory()->create([
            'movement_type_id' => $this->saleMovementType->id,
        ]);

        $this->mock(OrderExportService::class, function ($mock) use ($order) {
            $mock->shouldReceive('isOrderExportable')
                ->with($order->id)
                ->andReturn(true);

            // Simulamos que el pedido tiene productos de la categoría especial
            $mock->shouldReceive('hasCategory')
                ->with($order->id, \Mockery::type('int'))
                ->andReturn(true);

            $filePath = Storage::disk('local')->path('special-category-order.xlsx');
            file_put_contents($filePath, 'dummy');

            // Aquí validamos que includeHeader llegue en false
            $mock->shouldReceive('exportOrderToExcel')
                ->with($order->id, false, 'PRESUPUESTO X')
                ->andReturn($filePath);

            $mock->shouldReceive('generateFileName')
                ->andReturn('pedido_sin_header.xlsx');
        });

        $response = $this->get("/api/orders/{$order->id}/export-ticket");
        // dump($response);
        $response->assertOk();
    }
}

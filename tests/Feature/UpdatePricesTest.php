<?php

namespace Tests\Feature;

use App\Http\Requests\UpdateProductPricesRequest;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePricesTest extends TestCase
{
    use RefreshDatabase;

    private string $endpoint = '/api/products/update-prices';

    private function actingAsUser()
    {
        // Usuario con token que permita la acción (según authorize())
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('test', ['server:update'])->plainTextToken;

        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]);
    }

    /** @test */
    public function upgrade_prices_correctly_with_percentage_mode()
    {
        $productA = Product::factory()->create(['buy_price' => 100]);
        $productB = Product::factory()->create(['buy_price' => 200]);

        $payload = [
            'products_ids' => [$productA->id, $productB->id],
            'mode'        => UpdateProductPricesRequest::PERCENTAGE_MODE, // "porcentaje"
            'value'       => 10,
            'direction'   => UpdateProductPricesRequest::UPGRADE_PRICE_DIRECTION, // "subir"
        ];

        $response = $this->actingAsUser()->postJson($this->endpoint, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('products', [
            'id' => $productA->id,
            'buy_price' => 110, // 100 + 10%
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $productB->id,
            'buy_price' => 220, // 200 + 10%
        ]);
    }

    /** @test */
    public function downgrade_prices_correctly_with_absolute_price_mode()
    {
        $product = Product::factory()->create(['buy_price' => 500]);

        $payload = [
            'products_ids' => [$product->id],
            'mode'        => UpdateProductPricesRequest::ABSOLUTE_PRICE_MODE, // "precio"
            'value'       => 100,
            'direction'   => UpdateProductPricesRequest::DOWNGRADE_PRICE_DIRECTION, // "bajar"
        ];

        $response = $this->actingAsUser()->postJson($this->endpoint, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'buy_price' => 400, // 500 - 100
        ]);
    }

    /** @test */
    public function keeps_minimum_price_to_one_when_downgrading_below_zero()
    {
        $product = Product::factory()->create(['buy_price' => 50]);

        $payload = [
            'products_ids' => [$product->id],
            'mode'        => UpdateProductPricesRequest::ABSOLUTE_PRICE_MODE, // "precio"
            'value'       => 200,
            'direction'   => UpdateProductPricesRequest::DOWNGRADE_PRICE_DIRECTION,
        ];

        $response = $this->actingAsUser()->postJson($this->endpoint, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'buy_price' => 1, // nunca baja de 1
        ]);
    }

    /** @test */
    public function fails_when_mode_is_invalid()
    {
        $product = Product::factory()->create(['buy_price' => 200]);

        $payload = [
            'products_ids' => [$product->id],
            'mode'        => 'INVALIDO',
            'value'       => 10,
            'direction'   => UpdateProductPricesRequest::UPGRADE_PRICE_DIRECTION,
        ];

        $response = $this->actingAsUser()->postJson($this->endpoint, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['mode']);
    }

    /** @test */
    public function fails_when_direction_is_invalid()
    {
        $product = Product::factory()->create(['buy_price' => 200]);

        $payload = [
            'products_ids' => [$product->id],
            'mode'        => UpdateProductPricesRequest::PERCENTAGE_MODE,
            'value'       => 10,
            'direction'   => 'arriba-abajo',
        ];

        $response = $this->actingAsUser()->postJson($this->endpoint, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['direction']);
    }

    /** @test */
    public function fails_when_value_exceeds_limit_for_percentage_mode()
    {
        $product = Product::factory()->create(['buy_price' => 100]);

        $payload = [
            'products_ids' => [$product->id],
            'mode'        => UpdateProductPricesRequest::PERCENTAGE_MODE,
            'value'       => 900, // sobre 500 no permitido
            'direction'   => UpdateProductPricesRequest::UPGRADE_PRICE_DIRECTION,
        ];

        $response = $this->actingAsUser()->postJson($this->endpoint, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['value']);
    }

    /** @test */
    public function fails_when_value_exceeds_limit_for_absolute_price_mode()
    {
        $product = Product::factory()->create(['buy_price' => 100]);

        $payload = [
            'products_ids' => [$product->id],
            'mode'        => UpdateProductPricesRequest::ABSOLUTE_PRICE_MODE,
            'value'       => 999999,
            'direction'   => UpdateProductPricesRequest::UPGRADE_PRICE_DIRECTION,
        ];

        $response = $this->actingAsUser()->postJson($this->endpoint, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['value']);
    }

    /** @test */
    public function fails_when_a_product_does_not_exist()
    {
        Product::factory()->create(['buy_price' => 50]);

        $payload = [
            'products_ids' => [1, 9999], // 9999 no existe
            'mode'        => UpdateProductPricesRequest::ABSOLUTE_PRICE_MODE,
            'value'       => 10,
            'direction'   => UpdateProductPricesRequest::UPGRADE_PRICE_DIRECTION,
        ];

        $response = $this->actingAsUser()->postJson($this->endpoint, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products_ids.1']);
    }

    /** @test */
    public function fails_when_no_products_are_sent()
    {
        $payload = [
            'products_ids' => [],
            'mode'        => UpdateProductPricesRequest::ABSOLUTE_PRICE_MODE,
            'value'       => 10,
            'direction'   => UpdateProductPricesRequest::UPGRADE_PRICE_DIRECTION,
        ];

        $response = $this->actingAsUser()->postJson($this->endpoint, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products_ids']);
    }
}
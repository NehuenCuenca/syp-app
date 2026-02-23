<?php

namespace Database\Factories;

use App\Models\MovementType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MovementType>
 */
class MovementTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $movementType = fake()->unique(true)->randomElement(MovementType::getMovementTypes());
        $willIncreaseStock = match ($movementType) {
            MovementType::MOVEMENT_TYPE_BUY => true,
            MovementType::MOVEMENT_TYPE_SALE => false,
            MovementType::MOVEMENT_TYPE_POSITIVE_ADJUSTMENT => true,
            MovementType::MOVEMENT_TYPE_NEGATIVE_ADJUSTMENT => false,
            default => false
        };
        
        return [
            'name' => $movementType,
            'increase_stock' => $willIncreaseStock
        ];
    }
}

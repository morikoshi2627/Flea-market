<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    
    protected $model = Purchase::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),   // 購入者
            'item_id' => Item::factory(),   // 商品
            'payment_method' => $this->faker->randomElement(['card', 'konbini']), // Stripeのpayment_method_types
            'postal_code' => $this->faker->regexify('\d{3}-\d{4}'),
            'address' => $this->faker->address(),
            'building' => $this->faker->secondaryAddress(),
            'payment_id' => $this->faker->uuid(), // Stripe セッションIDの代わりにUUIDで疑似生成
        ];
    }
}
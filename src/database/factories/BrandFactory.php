<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Brand;

class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Brand::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
        ];
    }
}
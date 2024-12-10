<?php

namespace Database\Factories\Modules\Product\Entities;

use Modules\Product\Entities\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'code' => $this->faker->word.$this->faker->randomFloat(2, 10, 1000),
            'product_name' => $this->faker->word,
            'product_description' => $this->faker->sentence,
            'product_price' => $this->faker->randomFloat(2, 10, 1000),
            'main_url' => $this->faker->url,
            'url_1' => $this->faker->url,
        ];
    }
}

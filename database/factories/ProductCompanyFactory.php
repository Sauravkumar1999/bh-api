<?php

namespace Database\Factories;

use App\Models\ProductCompany;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductCompanyFactory extends Factory
{
    protected $model = ProductCompany::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->company,
            'status' => 1,
            'url' => $this->faker->url,
            'business_name' => $this->faker->company,
            'representative_name' => $this->faker->name,
            'registration_number' => Str::random(10),
            'address' => $this->faker->address,
            'registration_date' => $this->faker->date,
        ];
    }
}

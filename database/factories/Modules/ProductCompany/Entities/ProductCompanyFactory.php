<?php

namespace Database\Factories\Modules\ProductCompany\Entities;

use App\Models\ProductCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCompanyFactory extends Factory
{
    protected $model = ProductCompany::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'representative_name' => $this->faker->name,
            // Add other fields as needed
        ];
    }
}

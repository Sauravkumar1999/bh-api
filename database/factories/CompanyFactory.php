<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'code' => 'C0'.Str::random(3),
            'business_name' => $this->faker->companySuffix,
            'representative_name' => $this->faker->name,
            'registration_number' => $this->faker->numerify(str_repeat('#', 10)),
            'address' => $this->faker->address,
            "scope_of_disclosure" => $this->faker->text(),
            "registration_date" => date('Y/m/d')
        ];
    }
}

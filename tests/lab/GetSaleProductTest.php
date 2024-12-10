<?php

namespace Tests\Feature;

use Faker\Factory as Faker;
use App\Models\User;
use Tests\TestCase;



class GetSaleProductTest extends TestCase
{
    public function test_get_sale_product_list_found()
    {
        $randomUser = User::inRandomOrder()->first();
        $response = $this->get(route('get.channels', ['user_code' => $randomUser->code]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_get_sale_product_list_not_found()
    {
        $faker = Faker::create();
        $user_code =  $faker->randomNumber();
        $response = $this->get(route('get.channels', ['user_code' => $user_code]));
        $response->assertStatus(404);
    }

}

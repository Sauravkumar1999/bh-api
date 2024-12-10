<?php

namespace Tests\Feature;

use Faker\Factory;
use Tests\TestCase;

class ReferralCodeCheckTest extends TestCase
{
    protected $form_data;
    private $existing_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->existing_user = \App\Models\User::inRandomOrder()->first();
        $this->faker = Factory::create('en_US');
        $this->form_data = [
            'referral_code' => $this->existing_user->code,
        ];

    }

    public function test_valid_code()
    {
        $data = $this->form_data;
        $response = $this->post(route('referral_code.check'), $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }
    public function test_invalid_code()
    {
        $data = $this->form_data;
        $data['referral_code'] = rand(1,999999999);
        $response = $this->post(route('referral_code.check'), $data);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                "errors"=> [
                    "referral_code"
                ]
            ]
        ]);
    }
//    public function test_referral_code_field_not_provided()
//    {
//        $data = $this->form_data;
//        $data['referral_code'] = '';
//        $response = $this->post(route('referral_code.check'), $data);
//        $response->assertStatus(422);
//        $response->assertJsonStructure([
//            'success',
//            'message',
//            'data' => [
//                "errors"=> [
//                    "referral_code"
//                ]
//            ]
//        ]);
//    }
}


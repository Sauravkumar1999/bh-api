<?php

namespace Tests\Feature;

use App\Models\User;
use Faker\Factory;
use Tests\TestCase;

class EmailCheckTest extends TestCase
{
    protected $form_data;
    private $existing_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->existing_user = \App\Models\User::inRandomOrder()->first();
        $this->faker = Factory::create('en_US');
        $this->form_data = [
            'email' => $this->faker->unique()->safeEmail(),
        ];
        while (User::where('email', $this->form_data['email'])->exists()) {
            $this->form_data['email'] = $this->faker->unique()->safeEmail();
        }

    }

    public function test_available_email()
    {
        $data = $this->form_data;
        $response = $this->post(route('email_available.check'), $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }
    public function test_exists_email()
    {
        $data = $this->form_data;
        $data['email'] = $this->existing_user->email;
        $response = $this->post(route('email_available.check'), $data);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                "errors"=> [
                    "email"
                ]
            ]
        ]);
    }
    public function test_email_field_not_provided()
    {
        $data = $this->form_data;
        $data['email'] = '';
        $response = $this->post(route('email_available.check'), $data);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                "errors"=> [
                    "email"
                ]
            ]
        ]);
    }
}


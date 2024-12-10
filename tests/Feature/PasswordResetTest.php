<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    protected $form_data;
    private $existing_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->existing_user = User::factory()->create();
        $userPassword = 'Password@123';

        $this->form_data = [
            'password'              => $userPassword,
            'password_confirmation' => $userPassword,
        ];

    }

    public function test_valid_password()
    {
        $data = $this->form_data;
        $response = $this->post(route('user.reset-password', ['id' => $this->existing_user->id, 'email' => $this->existing_user->email]), $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }
    public function test_invalid_password()
    {
        $data = $this->form_data;
        $data['password'] = rand(1,10);
        $response = $this->post(route('user.reset-password', ['id' => $this->existing_user->id]), $data);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                "errors"=> [
                    "password"
                ]
            ]
        ]);
    }
    public function test_invalid_password_confirmation()
    {
        $data = $this->form_data;
        $data['password_confirmation'] = rand(1,10);
        $response = $this->post(route('user.reset-password', ['id' => $this->existing_user->id]), $data);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                "errors"=> [
                    "password"
                ]
            ]
        ]);
    }
    public function test_password_field_not_provided()
    {
        $data = $this->form_data;
        $data['password'] = '';
        $response = $this->post(route('user.reset-password', ['id' => $this->existing_user->id]), $data);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                "errors"=> [
                    "password"
                ]
            ]
        ]);
    }
    public function test_user_not_found()
    {
        $data = $this->form_data;
        $maxUserId = User::max('id');
        $nonExistentUserId = $maxUserId + 1;

        $response = $this->post(route('user.reset-password', ['id' => $nonExistentUserId]), $data);

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'errors'
            ]
        ]);
    }

    protected function tearDown(): void
    {
        // Delete the created user after each test
        if ($this->existing_user) {
            $this->existing_user->forceDelete();
        }
        parent::tearDown();
    }
}


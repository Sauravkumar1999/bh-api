<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeviceTokenUpdateTest extends TestCase
{
    private $form_data;
    protected static $test_device_token;
    protected static $token;
    protected static $wasSetup = false;

    public function setUp(): void
    {
        parent::setup();
        $this->sharedSetUp();
    }

    /**
     * Shared setup that runs only once before all tests.
     */
    protected function sharedSetUp(): void
    {
        // Get tokens for admin
        $this->setDeviceToken();
        self::$test_device_token = $this->getDeviceToken();
        self::$token = $this->getToken();

        $this->form_data = [
            'uuid'  => $this->faker->uuid(),
            'fcm_token' => $this->faker->uuid(),
        ];

    }

    /**
     * Create device token test.
     *
     * @return void
     */
    public function test_create_device_token()
    {
        $response = $this->save($this->form_data);

        $response->assertStatus(200);
    }


    /**
     * Update device token test.
     *
     * @return void
     */

    public function test_update_device_token()
    {
        $data = $this->form_data;
        $data['uuid'] = self::$test_device_token->uuid;
        $data['fcm_token'] = $this->faker->uuid();

        $response = $this->save($data);

        $response->assertStatus(200);
    }

    /**
     * device token invalid data test.
     *
     * @return void
     */

    public function test_invalid_device_token()
    {
        $data = [];
        $response = $this->save($data);

        $response->assertStatus(422);
    }

    protected function save($data)
    {
        return $this->withToken(self::$token)->post(route('update_fcm_token'), $data);
    }
}

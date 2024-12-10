<?php

namespace Tests\Feature;

use App\Models\DeviceToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLogoutTest extends TestCase
{
    private $query;
    protected static $token;
    protected static $wasSetup = true;

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
        self::$token = $this->getToken();

        $this->query = [
            'uuid'  => DeviceToken::whereNotNull('uuid')->where('user_id', $this::$adminUser->id)->value('uuid'),
        ];
    }

    /**
     * Test user logout with device token to be deleted
     *
     * @return void
     */
    public function test_user_logout_with_device_token_uuid_deleted()
    {
        $response = $this->logout($this->query['uuid']);

        $this->assertDatabaseMissing('device_tokens', [
            'user_id' => $this::$adminUser->id,
            'uuid'  => $this->query['uuid']
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test user logout without deleting device token
     *
     * @return void
     */

    public function test_user_logout_without_device_token_uuid_deleted()
    {
        $response = $this->logout(null);

        $this->assertDatabaseHas('device_tokens', [
            'user_id' => $this::$adminUser->id,
            'uuid'  => $this->query['uuid']
        ]);

        $response->assertStatus(200);
    }

    private function logout($uuid)
    {
        return $this->withToken($this::$token)->post(route('logout'), ['uuid' => $uuid]);
    }
}

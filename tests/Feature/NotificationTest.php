<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;
use App\Models\PushNotification;

class NotificationTest extends TestCase
{
    protected static $accessToken;
    protected static $adminToken;

    /**
     * Set up before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->sharedSetUp();
    }

     /**
     * Shared setup that runs only once before all tests.
     */
    protected function sharedSetUp(): void
    {   // Ensure tokens and users are set up only once
        if (!isset(self::$accessToken)) {
            self::$accessToken = $this->getTokenNonAdmin(); 
        }
        if (!isset(self::$adminToken)) {
            self::$adminToken = $this->getToken();
        }
    }

    public function test_notification_listing_admin()
    {
        $response = $this->withToken(self::$adminToken, 'Bearer')
            ->getJson(route('notifications', ['per_page' => 10]));

        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'contents',
                    'device',
                    'date',
                ],
            ],
            'pagination' => [
                'current_page',
                'first_page_url',
                'from',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
            ],
        ]);
    }

    public function test_notification_with_id_admin()
    {
        $notification = PushNotification::inRandomOrder()->first();

        $response = $this->withToken(self::$adminToken, 'Bearer')
            ->getJson(route('notifications.view', ['id' => $notification->id]));
        if ($response->status() == 200) {
            return $response->assertStatus(200)->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'contents',
                    'device',
                    'date',
                ],
            ]);
        }
        $response->assertStatus(404)->assertJsonStructure([
            'success',
            'message',
        ]);
    }

    public function test_notification_with_invalid_id_admin()
    {
        $response = $this->withToken(self::$adminToken, 'Bearer')
            ->getJson(route('notifications.view', ['id' => 999999]));

        $response->assertStatus(404)->assertJsonStructure([
            'success',
            'message',
        ]);
    }

    // with user login
    public function test_notification_listing_user()
    {
        $response = $this->withToken(self::$testUserToken, 'Bearer')
            ->getJson(route('notifications', ['per_page' => 10]));

        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'contents',
                    'device',
                    'date',
                ],
            ],
            'pagination' => [
                'current_page',
                'first_page_url',
                'from',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
            ],
        ]);;
    }

    public function test_notification_with_id_user()
    {
        $notification = PushNotification::inRandomOrder()->first();

        $response = $this->withToken(self::$testUserToken, 'Bearer')
            ->getJson(route('notifications.view', ['id' => $notification->id]));
        if ($response->status() == 200) {
            return $response->assertStatus(200)->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'contents',
                    'device',
                    'date',
                ],
            ]);
        }
        $response->assertStatus(404)->assertJsonStructure([
            'success',
            'message',
        ]);
    }

    public function test_notification_with_invalid_id_user()
    {
        $response = $this->withToken(self::$testUserToken, 'Bearer')
            ->getJson(route('notifications.view', ['id' => 9099999]));

        $response->assertStatus(404)->assertJsonStructure([
            'success',
            'message',
        ]);
    }

}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class BulletinListTest extends TestCase
{
    protected static $token;

    protected function setUp(): void
    {
        parent::setUp();
        // Get tokens for admin and normal user
        self::$token = $this->getToken();
    }

    public function test_bulletin_listing_authenticated()
    {
        $response = $this->withToken(self::$token, 'Bearer')
            ->get(route('bulletin'), ['per_page' => 10]);
        $response->assertStatus(200);
    }
}

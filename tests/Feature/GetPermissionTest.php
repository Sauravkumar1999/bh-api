<?php

namespace Tests\Feature;

use Tests\TestCase;

class GetPermissionTest extends TestCase
{
    protected static $accessToken;
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
    {
        self::$accessToken = $this->getToken();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_permission_list_found()
    {
        $response = $this->withToken(self::$accessToken)->get(route('permissions.index'),['per_page' => 1]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [[
                'id',
                'name',
                'display_name',
                'description',
                'ltpm',
                'updated_at',
                'created_at'
                ]]
            ]
        ]);
    }

    public function test_get_permission_list_not_found()
    {
        // per page must be interger given string
        $code = "C003";
        $response = $this->withToken(self::$accessToken)->get(route('permissions.index', ['code' => $code]));
        $response->assertStatus(404);
    }
}

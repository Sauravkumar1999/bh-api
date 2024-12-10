<?php

namespace Tests\Feature;

use Tests\TestCase;
use Faker\Factory as Faker;

class RoleTest extends TestCase
{
    protected $form_data;
    protected $token;
    protected $existing_role;
    protected $role_id;

    /**
     * Set up before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $faker = Faker::create();
        $this->token = $this->getToken();
        $this->form_data = [
            'name' => $faker->name(),
            'display_name' => $faker->firstName(),
            'description' => $faker->sentence(),
            'order' => $faker->numberBetween(10, 100)
        ];

        $this->role_id = \App\Models\Role::insertGetId($this->form_data);
    }

    protected function tearDown(): void
    {
        $this->closeDBConnection();
        parent::tearDown();
    }

    public function test_role_creation_on_storage()
    {
        $this->form_data['name'] = $this->form_data['name'] . rand(200, 1000);
        $response = $this->withToken($this->token, 'Bearer')
            ->post(route('role.create'), $this->form_data);
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [[
                'id',
                'name',
                'display_name',
                'description',
                'order',
                'personnel',
                'created_at',
                'updated_at'
            ]]
        ]);        

        $data = $response->decodeResponseJson();
        // assert that new roles are at the top of role table
        $response = $this->withToken($this->token,'Bearer')
                    ->get(route('role.index'),['per_page' => 1]);
        $data2 = $response->decodeResponseJson();
        $this->assertEquals($data['data'][0]['id'], $data2['data'][0]['id']);

    }

    public function test_role_indexing_from_storage()
    {
        $response = $this->withToken($this->token, 'Bearer')
            ->get(route('role.index'), ['per_page' => 1]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [[
                'id',
                'name',
                'display_name',
                'description',
                'order',
                'personnel',
                'created_at',
                'updated_at'
            ]]
        ]);        
    }

    public function test_view_role_from_storage()
    {
        $response = $this->withToken($this->token, 'Bearer')
            ->get(route('role.show', ['id' => $this->role_id]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [[
                'id',
                'name',
                'display_name',
                'description',
                'order',
                'personnel',
                'created_at',
                'updated_at'
            ]]
        ]);
    }

    public function test_role_deletion_from_storage()
    {
        $response = $this->withToken($this->token, 'Bearer')
            ->delete(route('role.delete', ['id' => $this->role_id]));
        $response->assertStatus(200);
    }
}

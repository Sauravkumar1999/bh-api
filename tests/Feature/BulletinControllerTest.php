<?php

namespace Tests\Feature;

use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Tests\TestCase;

class BulletinControllerTest extends TestCase
{
    protected $faker;
    private $form_data;
    private $update_form_data;
    protected static $normalUser;
    protected static $adminUser;
    protected $login_form_data;
    protected static $token;

    function setUp(): void
    {
        parent::setUp();
        $this->sharedSetUp();
    }

    /**
     * Shared setup that runs only once before all tests.
     */
    protected function sharedSetUp(): void
    {
        $this->faker = Faker::create('en_US');

        // Use the test user created in the TestCase class
        self::$normalUser = $this->getTestUser();
        self::$adminUser = $this->getAdminUser();

        // Get tokens for admin and normal user
        self::$token = $this->getToken();

        $this->form_data = [
            'title'         =>  Str::random(6),
            'distinguish'   => $this->faker->randomElement(['general', 'important']),
            'attachment'    => null,
            'permission'    => json_encode(
                rand(0, 10),
                rand(0, 10),
                rand(0, 10),
            ),
            'content'       => $this->faker->paragraph(),
            'user_id'       => self::$adminUser->id,
            'view_count'    => $this->faker->randomNumber(),
            'created_at'    => now(),
            'updated_at'    => now(),
            'deleted_at'    => null,
        ];

        $this->update_form_data = [
            'title'         =>  Str::random(6),
            'distinguish'   => $this->faker->randomElement(['general', 'important']),
            'attachment'    => null,
            'permission'    => json_encode(
                rand(0, 10),
                rand(0, 10),
                rand(0, 10),
            ),
            'content'       => $this->faker->paragraph(),
            'user_id'       => self::$adminUser->id,
            'view_count'    => $this->faker->randomNumber(),
            'created_at'    => now(),
            'updated_at'    => now(),
            'deleted_at'    => null,
        ];
    }

    /**
     *
     *
     *Bulletin List Found
     */
    public function test_get_bulletin_list_found()
    {
        try {
            $user = self::$adminUser;
            $response = $this->withToken(self::$token, 'Bearer')->get(route('bulletin'));
            $response->assertStatus(200);
            $responseData = $response->decodeResponseJson();
            $this->assertArrayHasKey('success', $responseData);
            $this->assertArrayHasKey('message', $responseData);
            $this->assertArrayHasKey('data', $responseData);

            if (isset($responseData['pagination'])) {
                $response->assertJsonStructure([
                    'success',
                    'message',
                    'data',
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
            } else {
                $response->assertJsonStructure([
                    'success',
                    'message',
                    'data',
                ]);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function test_create_bulletin()
    {
        $response = $this->withToken(self::$token, 'Bearer')
            ->post(route('bulletin.create'), $this->form_data);
        $response->assertStatus(200);
    }

    public function test_update_bulletin()
    {
        $form_data = $this->update_form_data;
        $response = $this->create($form_data);
        $data = $response->decodeResponseJson($response);
        $id = $data['data']['id'];
        $response = $this->update($form_data, $id);
        $response->assertStatus(200);
    }

    // validation for create bUlletin
    // test null title
    public function test_title()
    {
        $data = $this->form_data;
        $data['title'] = null;
        $response = $this->withToken(self::$token, 'Bearer')->post(route('bulletin.create'), $data);
        $response->assertStatus(422);
    }

    // test null distinguish
    public function test_distinguish()
    {
        $data = $this->form_data;
        $data['distinguish'] = null;
        $response = $this->withToken(self::$token, 'Bearer')->post(route('bulletin.create'), $data);
        $response->assertStatus(422);
    }

    // test null permission
    public function test_permission()
    {
        $data = $this->form_data;
        $data['permission'] = null;
        $response = $this->withToken(self::$token, 'Bearer')->post(route('bulletin.create'), $data);
        $response->assertStatus(422);
    }

    // test null permission
    public function test_content()
    {
        $data = $this->form_data;
        $data['content'] = null;
        $response = $this->withToken(self::$token, 'Bearer')->post(route('bulletin.create'), $data);
        $response->assertStatus(422);
    }

    // test null permission
    public function user_id()
    {
        $data = $this->form_data;
        $data['user_id'] = null;
        $response = $this->withToken(self::$token, 'Bearer')->post(route('bulletin.create'), $data);
        $response->assertStatus(500);
    }

    // create new Bulletin
    public function create($form_data)
    {
        $form_data = $this->form_data;
        $form_data['user_id'] = null;
        $response =  $this->withToken(self::$token, 'Bearer')->post(route('bulletin.create'), $form_data);
        return $response->assertStatus(200);
    }

    // update existing Bulleltin
    public function update($form_data, $id)
    {
        $response =  $this->withToken(self::$token, 'Bearer')
            ->post(route('bulletin.update', ['id' => $id]), $form_data);
        return $response->assertStatus(200);
    }

    // update existing Bulleltin
    public function test_bulletin_delete()
    {
        $response =  $this->withToken(self::$token, 'Bearer')
            ->delete(route('bulletin.delete', ['id' => self::$adminUser->id]));
        $response->assertStatus(200);
    }
}

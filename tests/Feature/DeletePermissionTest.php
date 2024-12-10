<?php

namespace Tests\Feature;

use Faker\Factory as Faker;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\Permission;

class DeletePermissionTest extends TestCase
{
    protected static $accessToken;
    protected $faker;
    private static $permission_id;

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
        $this->faker = Faker::create();

        // Generate a base name
        $baseName = $this->faker->unique()->name();

        // Check if the base name already exists in the database
        $uniqueName = $baseName;
        while (Permission::where('name', $uniqueName)->exists()) {
            $uniqueName = $baseName . '-' . Str::random(5);
        }

        $form_data = [
            'name'             => $uniqueName,
            'display_name'     => $this->faker->title(),
            'description'      => $this->faker->paragraph(),
            'ltpm'             => $this->faker->title()
        ];
        self::$accessToken = $this->getToken();
        $this->setTestPermission();
        self::$permission_id = $this->getTestPermission()->id;
    }

    /**
     * Test successful delete.
     *
     * @return void
     */
    public function test_successful_delete()
    {
        $response = $this->delete_permission(self::$permission_id);

        $response->assertStatus(200);
    }

    /**
     * Test unsuccessful delete.
     *
     * @return void
     */
    public function test_unsuccessful_delete()
    {
        $id = 9999999; //invalid id
        $response = $this->delete_permission($id);
        $response->assertStatus(401);
    }

    public function delete_permission($id)
    {
        return $this->withToken(self::$accessToken)->delete(route('permissions.delete', ['id' => $id]));
    }
}

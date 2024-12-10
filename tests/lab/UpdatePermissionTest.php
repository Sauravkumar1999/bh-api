<?php

namespace Tests\Feature;

use Tests\TestCase;
use Faker\Factory as Faker;
use App\Models\Permission;
use Illuminate\Support\Str;

class UpdatePermissionTest extends TestCase
{
    protected static $accessToken;
    protected $faker;
    private $form_data;
    protected static $testPermission;

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

        $this->faker = Faker::create('en_US');

        $this->setTestPermission();
        self::$testPermission = $this->getTestPermission();

        // Generate a base name
        $baseName = $this->faker->unique()->name();

        // Check if the base name already exists in the database
        $uniqueName = $baseName;
        while (Permission::where('name', $uniqueName)->exists()) {
            $uniqueName = $baseName . '-' . Str::random(5);
        }

        $this->form_data = [
            'name'             => $uniqueName,
            'display_name'     => $this->faker->title(),
            'description'      => $this->faker->paragraph(),
            'ltpm'             => $this->faker->title()
        ];
    }

    /**
     * test valid data.
     *
     * @return void
     */
    public function test_valid_data()
    {
        $response = $this->update($this->form_data, self::$testPermission->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    /**
     * test existing name.
     *
     * @return void
     */
    public function test_existing_name()
    {
        $data = $this->form_data;
        $data['name'] = self::$testPermission->name;
        $response = $this->update($data, self::$testPermission->id);

        $response->assertStatus(422);
    }

    /**
     * test required name.
     *
     * @return void
     */
    public function test_required_name()
    {
        $data = $this->form_data;
        $data['name'] = null;
        $response = $this->update($data, self::$testPermission->id);

        $response->assertStatus(422);
    }

    /**
     * test invalid display_name.
     *
     * @return void
     */
    public function test_required_display_name()
    {
        $data = $this->form_data;
        $data['display_name'] = null;
        $response = $this->update($data, self::$testPermission->id);

        $response->assertStatus(422);
    }

    /**
     * test required ltpm.
     *
     * @return void
     */
    public function test_required_ltpm()
    {
        $data = $this->form_data;
        $data['ltpm'] = null;
        $response = $this->update($data, self::$testPermission->id);

        $response->assertStatus(422);
    }

    /**
     * update permission record
     */
    public function update($data, $id)
    {
        return $this->withToken(self::$accessToken)->patch(route('permissions.update', ['id' => $id]), $data);
    }
}

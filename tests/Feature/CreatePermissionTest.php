<?php

namespace Tests\Feature;

use Faker\Factory as Faker;
use App\Models\Permission;
use Tests\TestCase;
use Illuminate\Support\Str;

class CreatePermissionTest extends TestCase
{
    protected static $token;
    protected static $testPermission;
    protected $faker;
    private $form_data;

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
        $this->faker = Faker::create('en_US');

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

        // echo json_encode($this->form_data);
        // echo " \n\n ";

        self::$token = $this->getToken();
        $this->setTestPermission();
        self::$testPermission = $this->getTestPermission();

    }

    /**
     * test valid data.
     *
     * @return void
     */
    public function test_valid_data()
    {
        $response = $this->create($this->form_data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' =>
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
        $response = $this->create($data);
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
        $response = $this->create($data);

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
        $response = $this->create($data);
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
        $response = $this->create($data);
        $response->assertStatus(422);
    }

    /**
     * create permission record
     */
    public function create($data)
    {
        return $this->withToken(self::$token)->post(route('permissions.create'), $data);
    }
}

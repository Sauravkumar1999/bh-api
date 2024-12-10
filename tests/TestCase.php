<?php

namespace Tests;

use App\Models\User;
use App\Models\Role;
use App\Models\AllowancePayment;
use App\Models\Permission;
use App\Models\Company;
use App\Models\DeviceToken;
use App\Models\ProductCompany;
use App\Models\Product;
use App\Models\FAQ;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Database\Factories\AllowancePaymentFactory;
use Database\Factories\AllowanceFactory;
use Database\Factories\DeviceTokenFactory;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $faker;
    protected static $adminUser;
    protected static $testUser;
    protected static $adminToken;
    protected static $adminRefreshToken;
    protected static $testUserToken;
    protected static $adminEmail;
    protected static $adminPwd;
    protected static $testAllowancePayment;
    protected static $testAllowance;
    protected static $testCompany;
    protected static $testPermission;
    protected static $testProduct;
    protected static $testProductCompany;
    protected static $testFaq;

    protected static $deviceToken;

    protected static $wasSetup = false;

    protected function setUp(): void
    {
        parent::setUp();

        self::$adminEmail = "developer@developer.com";
        self::$adminPwd = "123developer@123";
        $this->faker = Faker::create('en_US');

        if (!static::$wasSetup) {
            static::$wasSetup = true;
            $this->initializeSharedState();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // $this->cleanupDatabase();
        $this->closeDBConnection();
    }

    protected function initializeSharedState()
    {
        self::$adminToken = $this->getToken();
        self::$testUserToken = $this->getTokenNonAdmin();
    }

    protected function getToken()
    {
        $this->setAdminUser();

        $loginFormData = [
            'email' => self::$adminEmail,
            'password' => self::$adminPwd
        ];

        $response = self::post(route('login'), $loginFormData);
        $response->assertStatus(200);

        $data = $response->original;
        self::$adminToken = $data['data']['access_token'];

        return self::$adminToken;
    }

    protected function getTokenNonAdmin()
    {
        $this->setTestUser();

        $loginFormData = [
            'email' => self::$testUser->email,
            'password' => self::$testUser->plain_password
        ];

        $response = self::post(route('login'), $loginFormData);

        $response->assertStatus(200);

        $data = $response->original;
        self::$testUserToken = $data['data']['access_token'];

        return self::$testUserToken;
    }

    public function setDeviceToken()
    {
        self::$deviceToken = DeviceToken::factory()->create();
    }

    protected function getRefreshTokenAdmin()
    {
        $loginFormData = [
            'email' => self::$adminEmail,
            'password' => self::$adminPwd
        ];

        $response = self::post(route('login'), $loginFormData);
        $response->assertStatus(200);

        $data = $response->original;
        self::$adminRefreshToken = $data['data']['refresh_token'];

        return self::$adminRefreshToken;
    }

    public function setTestUser()
    {
        if (!isset(self::$testUser)) {
            // Generate a random email & code
            $code = rand(100000000, 999999999);
            $email = $this->faker->unique()->safeEmail;

            // Check if the email or code already exists in the database
            while (User::where('code', $code)->exists()) {
                $code = rand(100000000, 999999999);
            }
            while (User::where('email', $email)->exists()) {
                $email = $this->faker->unique()->safeEmail;
            }

            self::$testUser = User::factory()->create(['code' => $code, 'email' => $email, 'user_type' => 'member', 'member_status' => 'Active']);
        }
    }

    public function setAdminUser()
    {
        if (!isset(self::$adminUser)) {
            self::$adminUser = User::where('email', self::$adminEmail)->first();
        }
    }

    public function setTestAllowancePayment()
    {
        self::$testAllowancePayment = AllowancePaymentFactory::new()->create(['user_id' => self::$testUser->id]);
    }

    public function setTestAllowance()
    {
        self::$testAllowance = AllowanceFactory::new()->create(['member_id' => self::$testUser->id]);
    }

    public function setTestCompany()
    {
        self::$testCompany = Company::factory()->create();
    }

    public function setTestPermission()
    {
        // Generate a base name
        $baseName = $this->faker->unique()->name();

        // Check if the base name already exists in the database
        $uniqueName = $baseName;
        while (Permission::where('name', $uniqueName)->exists()) {
            $uniqueName = $baseName . '-' . Str::random(5);
        }

        self::$testPermission = Permission::factory(['name' => $uniqueName])->create();
    }

    public function setTestProductCompany()
    {
        // Generate a base name
        $baseName = $this->faker->unique()->name();

        // Check if the base name already exists in the database
        $uniqueName = $baseName;
        while (ProductCompany::where('name', $uniqueName)->exists()) {
            $uniqueName = $baseName . '-' . Str::random(5);
        }

        self::$testProductCompany = ProductCompany::factory(['name' => $uniqueName])->create();
    }

    public function setTestProduct()
    {
        self::$testProduct = Product::factory()->create();
    }

    public function setTestFaq()
    {
        self::$testFaq = FAQ::factory()->create();
    }

    public function getTestFaq()
    {
        return self::$testFaq;
    }

    public function getTestProduct()
    {
        return self::$testProduct;
    }

    public function getTestProductCompany()
    {
        return self::$testProductCompany;
    }

    public function getTestPermission()
    {
        return self::$testPermission;
    }

    public function getTestCompany()
    {
        return self::$testCompany;
    }

    public function getTestAllowance()
    {
        return self::$testAllowance;
    }

    public function getTestAllowancePayment()
    {
        return self::$testAllowancePayment;
    }

    public function getAdminUser()
    {
        return self::$adminUser;
    }

    public function getTestUser()
    {
        return self::$testUser;
    }

    public function getDeviceToken()
    {
        return self::$deviceToken;
    }

    protected function cleanupDatabase()
    {
        if (self::$testAllowancePayment && self::$testAllowancePayment->exists) {
            self::$testAllowancePayment->forceDelete();
        }

        if (self::$testUser && self::$testUser->exists) {
            self::$testUser->forceDelete();
        }
    }

    protected function closeDBConnection()
    {
        unset($this->app['db']);
    }
}

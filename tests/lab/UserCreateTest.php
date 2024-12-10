<?php

namespace Tests\Feature;

use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\Contact;
use App\Models\User;
use Tests\TestCase;

class UserCreateTest extends TestCase
{
    private $faker;
    private $form_data;
    protected $accessToken;
    protected $testLoginUser;
    protected $testcreateUser;

    function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create('en_US');
        $this->accessToken = $this->getToken();

        $referralCode = User::inRandomOrder()->first()->value('code');
        $password = $this->faker->password(8, 20);

        $this->form_data = [
            'name'             => $this->faker->firstName(),
            'email'                  => $this->faker->unique()->safeEmail(),
            'user_type'              => $this->faker->randomElement(['agency', 'member']),
            'status'                 => $this->faker->randomElement([0, 1]),
            'password'               => $password,
            'contact'                => $this->faker->numerify(str_repeat('#', 10)),
            'dob'                    => $this->faker->date(),
            'gender'                 => $this->faker->randomElement(['male', 'female']),
            'address'                => $this->faker->address(),
            'address_detail'         => $this->faker->secondaryAddress(),
            'account_number'         => $this->faker->numerify(str_repeat('#', 12)),
            'bank_id'                => null,
            'recommender'            => $referralCode,
        ];
    }

    protected function tearDown(): void
    {
        $this->closeDBConnection();
        parent::tearDown();
    }

    // Test valid data
    public function test_valid_data()
    {
        $data = $this->form_data;
        $res = $this->createUser($data);
        $res->assertStatus(200);
        $res->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user_id',
                'code',
                'name',
                'telephone_1',
                'email',
                'dob',
                'gender',
                'final_confirmation',
                'company',
                'submitted_date',
                'deposit_date',
                'start_date',
                'end_date',
                'status',
                'role',
                'referral_user',
                'memberApplication',
                'registration_date',
                'bankbook',
                'idCard',
                'sns',
                'qr_code_url'
            ]
        ]);
        $this->testcreateUser = User::where('email', $data['email'])->first();
    }

    // Test invalid email test
    public function test_invalid_email()
    {
        $data = $this->form_data;
        $data['email'] =  Str::random(10);
        $response = $this->createUser($data);
        $response->assertStatus(422);
    }
    // Test existing email test
    public function test_existing_email()
    {
        $data = $this->form_data;
        $data['email'] = User::inRandomOrder()->first()->value('email');
        $response = $this->createUser($data);
        $response->assertStatus(422);
    }
    // Test invalid password
    public function test_invalid_password()
    {
        $data = $this->form_data;
        $data['password'] = Str::random(6);
        $response = $this->createUser($data);
        $response->assertStatus(422);
    }

    // Test existing phone
    public function test_existing_phone()
    {
        $data = $this->form_data;
        $data['contact'] = Contact::inRandomOrder()->first()->value('telephone_1');
        $response = $this->createUser($data);
        $response->assertStatus(422);
    }

    // test null first_name
    public function test_first_name()
    {
        $data = $this->form_data;
        $data['name'] = null;
        $response = $this->createUser($data);
        $response->assertStatus(422);
    }
    // test invalid recommender
    public function test_invalid_recommender()
    {
        $data = $this->form_data;
        $data['recommender'] = $this->faker->numerify(str_repeat('#', 10));
        $response = $this->createUser($data);
        $response->assertStatus(422);
    }

    // comman method for testing
    public function createUser($form_data)
    {
        $res = $this->withToken($this->accessToken, 'Bearer')
            ->post(route('user.create'), $form_data);
        return $res;
    }
}

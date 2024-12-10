<?php

namespace Tests\Feature;

use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\Contact;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    private $faker;
    private $form_data;
    protected $accessToken;
    protected $testLoginUser;
    protected $user;
    protected $recommender;

    function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create('en_US');
        $this->accessToken = $this->getToken();
        $this->user = \App\Models\User::inRandomOrder()->first();
        $this->recommender = \App\Models\User::inRandomOrder()->pluck('code')->first();
        $referralCode = $this->user->code;
        $password = $this->faker->password(8, 20);

        $this->form_data = [
            'name'                   => $this->faker->firstName(),
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
        $res = $this->updateUser($data);
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
    }

    // Test invalid email test
    public function test_invalid_email()
    {
        $data = $this->form_data;
        $data['email'] =  Str::random(10);
        $response = $this->updateUser($data);
        $response->assertStatus(422);
    }
    // Test existing email test
    public function test_existing_email()
    {
        $data = $this->form_data;
        $data['email'] = $this->user->email;
        $response = $this->updateUser($data);
        $response->assertStatus(422);
    }
    // Test invalid password
    public function test_invalid_password()
    {
        $data = $this->form_data;
        $data['password'] = Str::random(6);
        $response = $this->updateUser($data);
        $response->assertStatus(422);
    }
    // Test existing phone
    public function test_existing_phone()
    {
        $data = $this->form_data;
        $data['contact'] = Contact::inRandomOrder()->first()->value('telephone_1');
        $response = $this->updateUser($data);
        $response->assertStatus(422);
    }
    // test null first_name
    public function test_first_name()
    {
        $data = $this->form_data;
        $data['name'] = null;
        $response = $this->updateUser($data);
        $response->assertStatus(422);
    }
    // test invalid recommender
    public function test_invalid_recommender()
    {
        $data = $this->form_data;
        $data['recommender'] = $this->faker->numerify(str_repeat('#', 10));
        $response = $this->updateUser($data);
        $response->assertStatus(422);
    }

    // comman method for testing
    public function updateUser($form_data)
    {
        $res = $this->withToken($this->accessToken, 'Bearer')
            ->post(route('user.update', $this->user->id), $form_data);
        return $res;
    }

    public function test_update_user_recommender()
    {
        $this->form_data['recommender'] = $this->recommender;
        $response = $this->withToken($this->accessToken,'Bearer')
                    ->post(route('user.update',['user_id' => $this->user->id]),$this->form_data);
        $response->assertStatus(200);
        
    }
}

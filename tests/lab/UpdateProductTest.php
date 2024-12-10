<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductCompany;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    protected static $accessToken;
    protected static $testUser;
    protected static $adminUser;
    protected $faker;
    private $form_data;
    protected static $test_product;
    protected static $test_company;
    protected static $test_product_company;


    protected function setUp(): void
    {
        parent::setUp();
        $this->sharedSetUp();
    }

     /*
    * Shared setup that runs only once before all tests.
    */
   protected function sharedSetUp(): void
   {

        $this->faker = Faker::create('en_US');

        self::$accessToken = $this->getToken();

        self::$testUser = $this->getTestUser();
        self::$adminUser = $this->getAdminUser();

        $this->setTestProduct();
        self::$test_product = $this->getTestProduct();

        $this->setTestCompany();
        self::$test_company = $this->getTestCompany();

        $this->setTestProductCompany();
        self::$test_product_company = $this->getTestProductCompany();

        $this->form_data = [
            'product_name'                  => $this->faker->title(),
            'product_company_id'            => self::$test_product_company->id,
            'product_description'           => $this->faker->paragraph(),
            'product_price'                 => $this->faker->numberBetween(0, 9999999),
            'main_url'                      => $this->faker->url(),
            'url_params'                    => $this->faker->randomElements(["bhid={01033527689}", "bhid={01033527681}", "bhid={01033527616}"], 1),
            'url_1'                         => $this->faker->url(),
            'sale_rights'                   => $this->faker->randomElement(['full_disclosure', 'partial_disclosure']),
            'approval_rights'               => [self::$testUser->id],
            'other_fees'                    => $this->faker->randomFloat(2, 0, 99999.99),
            'user_id'                       => self::$testUser->id,
            'company_id'                    => [self::$test_company->id],
            'exposer_order'                 => $this->faker->randomNumber(),
            'sale_status'                   => $this->faker->randomElement(['normal', 'pause', 'stop-selling', 'onetime-sell']),
            'contact_notifications'         => $this->faker->randomNumber(2),
            'commission_type'               => 'with-ratio',
            'referral_bonus'                => $this->faker->numberBetween(0, 99),
            'bh_sale_commissions'           => $this->faker->numberBetween(0, 99),
            'bp'                            => $this->faker->numberBetween(0, 99),
            'ba'                            => $this->faker->numberBetween(0, 99),
            'md'                            => $this->faker->numberBetween(0, 99),
            'pmd'                           => $this->faker->numberBetween(0, 99),
            'h_md'                          => $this->faker->numberBetween(0, 99),
            'h_pmd'                         => $this->faker->numberBetween(0, 99),
            'referral_bonusRadioOptions'    => $this->faker->randomElement(['applied', 'not-applied']),
            'h_pmdRadioOptions'             => $this->faker->randomElement(['applied', 'not-applied']),
            'h_mdRadioOptions'              => $this->faker->randomElement(['applied', 'not-applied']),
        ];   
    }

    /**
     * A valid data with-ratio.
     *
     * @return void
     */
    public function test_valid_data()
    {
        $data = $this->form_data;
        //Storage::fake('banner');
        //$file = UploadedFile::fake()->image(time() . 'banner.jpg');
        //$data['banner'] = $file;
        $response = $this->update($data, self::$test_product->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'data' => [
                    'id',
                    'code',
                    'product_name',
                    'product_description',
                    'product_price',
                    'main_url',
                    'url_params',
                    'url_1',
                    'url_2',
                ]
            ]
        ]);
    }

    /**
     * A invalid data.
     *
     * @return void
     */
    public function test_invalid_data()
    {
        $data = $this->form_data;
        Storage::fake('banner');
        $file = UploadedFile::fake()->image(time() . 'banner.jpg');
        $data['banner'] = $file;
        unset($data['product_name']); //product_name is required
        $response = $this->update($data, self::$test_product->id);
        $response->assertStatus(422);
    }

    /**
     * create permission record
     */
    public function update($data, $id)
    {
        return $this->withToken(self::$accessToken)->post(route('products.update', ['id' => $id]), $data);
    }
}

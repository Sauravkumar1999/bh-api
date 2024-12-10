<?php

namespace App\Services;

use App\Events\ProductCreated;
use App\Models\Channel;
use App\Models\Company;
use App\Traits\MediaHandler;
use App\Traits\ProductSequenceManager;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\User;

class ProductService
{
    use MediaHandler, ProductSequenceManager;
    private $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function createProduct($request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            //save raw product columns
            $product = $this->save($data);

            // save approval and sales rights
            $this->saveApprovalRights($product, $data['approval_rights']);
            $this->saveSaleRights($product, $data['sale_rights'], isset($data['company_id']) ? $data['company_id'] : []);

            event(new ProductCreated($product));

            // upload banner and attach to product
            $banner = $this->handleBannerImage($request);
            if($banner) $product->attachMedia($banner, 'banner');

            DB::commit();

            return [
                'data'    => $product,
                'success' => true,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateProduct($request, $id)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            //save raw product columns
            $product = $this->save($data, $id);

            // save approval and sales rights
            $this->saveApprovalRights($product, $data['approval_rights']);
            $this->saveSaleRights($product, $data['sale_rights'], isset($data['company_id']) ? $data['company_id'] : []);

            // event(new ProductUpdated($product)); i don't think we need it

            // upload banner and attach to product
            $this->handleBannerImage($request, $id);

            DB::commit();

            return [
                'data'    =>$product,
                'success' => true,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteProduct($id)
    {
        try {
            $this->model->findOrFail($id)->delete();

            return [
                'success' => true,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function save($data, mixed $product = null)
    {
        // Fetch channel defaults
        $channelDefaults = $this->getChannelDefaults($data['channel_id']);

        $commissiondata = [
            "normal" => [
                'commission_bp'  => $this->getValidCommission($data, 'bp', $channelDefaults['normal']['bp']),
                'commission_ba'  => $this->getValidCommission($data, 'ba', $channelDefaults['normal']['ba']),
                'commission_md'  => $this->getValidCommission($data, 'md', $channelDefaults['normal']['md']),
                'commission_pmd' => $this->getValidCommission($data, 'pmd', $channelDefaults['normal']['pmd']),
            ],
            "headquarter" => [
                'md'  => $this->getValidCommission($data, 'h_md', $channelDefaults['headquarter']['md']),
                'pmd' => $this->getValidCommission($data, 'h_pmd', $channelDefaults['headquarter']['pmd']),
            ]
        ];

        if (!$product) {
            $product = $this->model;
            $product->code = $this->getNextProductCode();
        }

        $product->product_name = $data['product_name'];
        $product->channel_id = $data['channel_id'];
        $product->commission_type = $this->getValidCommission($data, 'commission_type', $channelDefaults['commission_type']);
        $product->product_description = $data['product_description'];
        $product->referral_bonus = $this->getValidCommission($data, 'referral_bonus', $channelDefaults['referral_bonus']);
        $product->other_fees = str_replace(',', '', $data['other_fees']);
        $product->bh_sale_commissions = str_replace(',', '', $data['bh_sale_commissions']);
        $product->main_url = $data['main_url'];
        $product->user_id = $data['user_id'] ?? auth()->id();
        $product->exposer_order = $data['exposer_order'];
        $product->sale_status = $data['sale_status'];
        $product->contact_notifications = $data['contact_notifications'];
        $product->product_price = ($data['product_price'] == '') ? 0.00 : str_replace(',', '', $data['product_price']);
        $product->product_commissions = $commissiondata;
        $product->save();

        return $product;
    }

    private function getChannelDefaults($channel_id)
    {
        $channel = Channel::find($channel_id);

        if (!$channel) {
            return [
                'normal' => [
                    'bp' => 0,
                    'ba' => 0,
                    'md' => 0,
                    'pmd' => 0,
                ],
                'headquarter' => [
                    'md' => 0,
                    'pmd' => 0,
                ],
                'commission_type' => 0,
                'referral_bonus' => 0,
            ];
        }

        $channelCommissions = $channel->channel_commissions;

        return [
            'normal' => [
                'bp' => $channelCommissions['normal']['commission_bp'] ?? 0,
                'ba' => $channelCommissions['normal']['commission_ba'] ?? 0,
                'md' => $channelCommissions['normal']['commission_md'] ?? 0,
                'pmd' => $channelCommissions['normal']['commission_pmd'] ?? 0,
            ],
            'headquarter' => [
                'md' => $channelCommissions['headquarter']['md'] ?? 0,
                'pmd' => $channelCommissions['headquarter']['pmd'] ?? 0,
            ],
            'commission_type' => $channel->commission_type ?? 0,
            'referral_bonus' => $channel->referral_bonus ?? 0,
        ];
    }

    private function getValidCommission($data, $key, $default)
    {
        return isset($data[$key]) && $data[$key] !== '' && $data[$key] !== '0'
            ? str_replace(',', '', $data[$key])
            : str_replace(',', '', $default);
    }

    private function saveApprovalRights(?Product $product, mixed $approval_rights)
    {
        if (!empty($approval_rights)) {

            $product->approvalRights()->detach();

            if ($approval_rights[0] === 'all_user') {
                $product->update(['approval_rights_disclosure' => 'full']);
                $approval_rights = User::all()->pluck('id')->toArray();
            } else {
                $product->update(['approval_rights_disclosure' => 'partial']);
            }

            foreach ($approval_rights as $right_id) {

                $user = User::find($right_id);
                if ($user && $user->productRights()->exists()) {
                    $product->approvalRights()->attach($right_id, [
                        'type'    => 'approval_rights',
                        'odr_app' => $user->productRights()->max('odr_app') + 1
                    ]);
                } else {
                    $product->approvalRights()->attach($right_id, [
                        'type'    => 'approval_rights',
                        'odr_app' => 1
                    ]);
                }
            }
        }
    }


    private function saveSaleRights(?Product $product, string $sale_rights, mixed $companies)
    {
        if (!empty($sale_rights)) {

            $product->saleRights()->detach();

            if ($sale_rights === 'full_disclosure') {
                $product->update(['sale_rights_disclosure' => 'full']);
                $companies = Company::all()->pluck('id')->toArray();
            } else {
                $product->update(['sale_rights_disclosure' => 'partial']);
            }

            foreach ($companies as $cid) {
                $company = Company::find($cid);
                if ($company && $company->productRights()->exists()) {
                    $product->saleRights()->attach($cid, [
                        'type'    => 'sale_rights',
                        'odr_app' => $company->productRights()->max('odr_app') + 1
                    ]);
                } else {
                    $product->saleRights()->attach($cid, [
                        'type'    => 'sale_rights',
                        'odr_app' => 1
                    ]);
                }
            }
        }
    }


    private function handleBannerImage($request, Product $product = null)
    {
        if ($request->hasFile('banner')) {
            $productBanner = $this->uploadBannerImage($request->file('banner'));
            if ($product) {
                $product->syncMedia($productBanner, 'banner');
            }
            else  return $productBanner;
        }
        return null;
    }
}

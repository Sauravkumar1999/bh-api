<?php

namespace App\Services;

use App\Events\ChannelCreated;
use App\Events\ProductCreated;
use App\Models\Channel;
use App\Models\Company;
use App\Traits\MediaHandler;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\User;
use App\Traits\ChannelSequenceManager;

class ChannelService
{
    use MediaHandler, ChannelSequenceManager;
    private $model;

    public function __construct(Channel $channel)
    {
        $this->model = $channel;
    }

    public function createChannel($request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            //save raw channel columns
            $channel = $this->save($data);

            // save approval and sales rights
            $this->saveApprovalRights($channel, $data['approval_rights']);
            $this->saveSaleRights($channel, $data['sale_rights'], isset($data['company_id']) ? $data['company_id'] : []);

            event(new ChannelCreated($channel));

            // upload banner and attach to channel
            $banner = $this->handleBannerImage($request);
            if($banner) $channel->attachMedia($banner, 'banner');

            DB::commit();

            return [
                'data'    => $channel,
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

    public function updateChannel($request, $channel)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            //save raw channel columns
            $channel = $this->save($data, $channel);

            // save approval and sales rights
            $this->saveApprovalRights($channel, $data['approval_rights']);
            $this->saveSaleRights($channel, $data['sale_rights'], isset($data['sr']) ? $data['sr'] : []);

            // event(new ChannelUpdated($channel)); i don't think we need it

            // upload banner and attach to channel
            $this->handleBannerImage($request, $channel);

            DB::commit();

            return [
                'data'    => $channel,
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

    public function deleteChannel($id)
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

    private function save($data, mixed $channel = null)
    {
        $commissiondata = [
            "normal"      => [
                'commission_bp'  => str_replace(',', '', $data['bp']),
                'commission_ba'  => str_replace(',', '', $data['ba']),
                'commission_md'  => str_replace(',', '', $data['md']),
                'commission_pmd' => str_replace(',', '', $data['pmd']),
            ],
            "headquarter" => [
                'md'  => str_replace(',', '', isset($data['h_md']) ? $data['h_md'] : 0),
                'pmd' => str_replace(',', '', isset($data['h_pmd']) ? $data['h_pmd'] : 0),
            ]
        ];


        if (!$channel) {
            $channel = $this->model;
            $channel->code = $this->getNextChannelCode();
        }

        $channel->fill([
            "channel_name" => $data['channel_name'],
            "company_id" => $data['company_id'],
            "commission_type" => $data['commission_type'],
            "channel_description" => $data['channel_description'],
            "referral_bonus" => (isset($data['referral_bonus']) ? str_replace(',', '', $data['referral_bonus']) : 0),
            "other_fees" => str_replace(',', '', $data['other_fees']),
            "bh_sale_commissions" => str_replace(',', '', $data['bh_sale_commissions']),
            "main_url" => $data['main_url'],
            "url_1" => $data['url_1'],
            "user_id" => $data['user_id'] ?? auth()->id(),
            "exposer_order" => $data['exposer_order'],
            "sale_status" => $data['sale_status'],
            "url_params" => $data['url_params'],
            "contact_notifications" => $data['contact_notifications'],
            "channel_price" => ($data['channel_price'] == '') ? 0.00 : str_replace(',', '', $data['channel_price']),
            "channel_commissions" => $commissiondata
        ]);

        $channel->save();


        return $channel;
    }

    private function saveApprovalRights(?Channel $channel, mixed $approval_rights)
    {
        if (!empty($approval_rights)) {

            $channel->approvalRights()->detach();

            if ($approval_rights[0] === 'all_user') {
                $channel->update(['approval_rights_disclosure' => 'full']);
                $approval_rights = User::all()->pluck('id')->toArray();
            } else {
                $channel->update(['approval_rights_disclosure' => 'partial']);
            }

            foreach ($approval_rights as $right_id) {
                $user = User::find($right_id);
                if ($user && $user->channelRights()->exists()) {
                    $channel->approvalRights()->attach($right_id, [
                        'type'    => 'approval_rights',
                        'odr_app' => $user->channelRights()->max('odr_app') + 1
                    ]);
                } else {
                    $channel->approvalRights()->attach($right_id, [
                        'type'    => 'approval_rights',
                        'odr_app' => 1
                    ]);
                }
            }
        }
    }


    private function saveSaleRights(?Channel $channel, string $sale_rights, mixed $companies)
    {

        if (!empty($sale_rights)) {

            $channel->saleRights()->detach();

            if ($sale_rights === 'full_disclosure') {
                $channel->update(['sale_rights_disclosure' => 'full']);
                $companies = Company::all()->pluck('id')->toArray();
            } else {
                $channel->update(['sale_rights_disclosure' => 'partial']);
            }

            foreach ($companies as $cid) {
                $company = Company::find($cid);
                if ($company && $company->channelRights()->exists()) {
                    $channel->saleRights()->attach($cid, [
                        'type'    => 'sale_rights',
                        'odr_app' => $company->channelRights()->max('odr_app') + 1
                    ]);
                } else {
                    $channel->saleRights()->attach($cid, [
                        'type'    => 'sale_rights',
                        'odr_app' => 1
                    ]);
                }
            }
        }
    }


    private function handleBannerImage($request, Channel $channel = null)
    {
        if ($request->hasFile('banner')) {
            $channelBanner = $this->uploadBannerImage($request->file('banner'));

            if ($channel) {
                $channel->syncMedia($channelBanner, 'banner');
            }
            else  return $channelBanner;
        }
        return null;
    }
}

<?php

    namespace App\Http\Resources;

    use Illuminate\Http\Resources\Json\JsonResource;

    class ChannelResource extends JsonResource
    {
        /**
         * Transform the resource into an array.
         *
         * @param  \Illuminate\Http\Request  $request
         *
         * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
         */
        public function toArray($request)
        {
            return [
                'id'                            => $this->id,
                'code'                          => $this->code,
                'channel_name'                  => $this->channel_name,
                'channel_description'           => $this->channel_description,
                'channel_price'                 => $this->channel_price,
                // 'commission_type'               => $this->commission_type,
                // 'bh_sale_commissions'           => $this->bh_sale_commissions,
                'main_url'                      => $this->main_url,
                'url_params'                    => $this->url_params,
                'url_1'                         => $this->url_1,
                'url_2'                         => $this->url_2,
                // 'referral_bonus'                => $this->referral_bonus,
                // 'other_fees'                    => $this->other_fees,
                // 'exposer_order'                 => $this->exposer_order,
                // 'sale_rights_disclosure'        => $this->sale_rights_disclosure,
                // 'approval_rights_disclosure'    => $this->approval_rights_disclosure,
                // 'channel_commissions'           => $this->channel_commissions,
                'banner'                        => $this->banner(),
                'urls_open_mode'                => $this->urls_open_mode,
                'sale_status'                   => $this->sale_status,
                'created_at'                    => $this->created_at,
                'updated_at'                    => $this->updated_at,
                'company'                       => $this->company ? new ProductCompanyResource($this->company) : null,
            ];
        }
    }

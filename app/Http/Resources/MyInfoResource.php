<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MyInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $foramtedDate = Carbon::now()->addMonth();
        return [

            'id' => $this->resource['user']->id,
            'code' => $this->resource['user']->code,
            'email' => $this->resource['user']->email,
            'name'   => $this->resource['user']->first_name,
            'company'=>  $this->resource['user']->company?->name,
            'role'  => $this->resource['user']->roles()->exists() ? $this->resource['user']->roles()->first()->name : null,
            'sale_month' => __('messages.sale_month', [
                   'year' => $foramtedDate->format('Y'),
                   'month' => $foramtedDate->isoFormat('MM'),
                   'month_name' =>  $foramtedDate->format('F'),
            ]),
            'sales_amount'  => '25,562,000원',
            'is_royal_member'  => is_royal_member() ? __('messages.royal_member') : __('messages.normal_member'),
            'userSetting' => UserSettingResource::make(['user_setting' => $this->resource['user']->userSetting, 'sales_person_image' => $this->resource['user']->salesPersonImage()]),
            'qr_code_url'        => env('APP_ERP_URL') . '/bhid/' . $this->resource['user']->code,
            'app_url' => env('APP_ERP_URL') . '/get-app/?referrer=' . $this->resource['user']->code,
            'my_info_url' => env('APP_ERP_URL') . '/admin/my-info/'.$this->resource['user']->id.'/edit',
            ...(isset($this->resource['channels']) && $this->resource['channels']->isNotEmpty() ? [
                'channels' => [
                    'data' => MyInfoChannelsResource::collection($this->resource['channels']),
                ]
            ] : [])
            ];
    }
}

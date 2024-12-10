<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Plank\Mediable\Mediable;
use App\Services\UserService;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     title="UserResource",
 *     description="User resource schema",
 *     @OA\Property(property="code", type="string"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="dob", type="string", format="date"),
 *     @OA\Property(property="gender", type="string"),
 *     @OA\Property(property="final_confirmation", type="boolean"),
 *     @OA\Property(property="company", type="string"),
 *     @OA\Property(property="role", type="string"),
 * )
 */
class UserResource extends JsonResource
{
    use Mediable;


    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $userService = new UserService($this->resource);

        $hasUserSettings = $this->userSetting()->exists();

        return [
            'user_id'            => $this->id,
            'code'               => $this->code,
            'name'               => $this->full_name,
            'telephone_1'        => $this->contacts()->exists() ? $this->contacts()->first()->telephone_1 : null,
            'email'              => $this->email,
            'dob'                => $this->dob,
            'gender'             => ucfirst($this->gender),
            'final_confirmation' => $this->final_confirmation,
            'company'            => $this->company?->name,
            'submitted_date'     => $this->submitted_date,
            'deposit_date'       => $this->deposit_date,
            'start_date'         => $this->start_date,
            'end_date'           => $this->end_date,
            'status'             => $this->status,
            'role'               => $this->roles()->exists() ? $this->roles()->first()->name : null,
            // 'referral_user'      => $userService->getReferralUser($this->parent_id),
            'referral_user'      => [],
            'memberApplication'  => $userService->memberApplication($request),
            'registration_date'  => $this->created_at,
            'bankbook'           => $this->bankbook(),
            'idCard'             => $this->idCard(),
            'recommender'        => $this->parent?->code,
            'sns'                => $hasUserSettings ? new SnsResource($this->userSetting()->first()->sns) : null,
            'qr_code_url'        => env('APP_ERP_URL') . '/bhid/' . $this->code,
            'profile_pic'        => $hasUserSettings ? ($this->userSetting()->first()->image_register ? $this->salesPersonImage() : '') : '',
            'portfolio'          => $hasUserSettings ? $this->userSetting()->first()->portfolio : '',
            'telephone_formatted'=> $this->contacts()->exists() ? format_phone_number($this->contacts()->first()->telephone_1) : null,
            "is_bio_enable" => $this->userSetting->text_register ?? false,
            "bio_text" => $this->userSetting->text_registration ?? '',
        ];
    }

}

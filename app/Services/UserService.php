<?php

namespace App\Services;

use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Plank\Mediable\Mediable;

class UserService
{
    private $model;
    private $phone_number;
    private array $validatedData;

    use Mediable;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function verifyUser($request)
    {
        try {
            $sessionService = new SessionService();
            $session = $sessionService->getSessionByKey('otp', $request['otp']);
            if (!$session) {
                return [
                    'success' => false,
                    'message' => __('messages.invalid_otp')
                ];
            }
            $sessionService->markOtpAsUsed($request['otp']);

            $user = $this->model->where(function ($query) use ($request) {
                $query->where('email', $request['email']);
                $query->where('first_name', $request['name']);
            })->whereHas('contacts', function ($query) use ($request) {
                $query->where('telephone_1', $request['phone']);
            })->first();

            if ($user) {
                return [
                    'user' => $user,
                    'success' => true,
                ];
            }
            return [
                'success' => false,
                'message' => __('messages.user_not_found')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function resetPassword($request, $id)
    {
        try {
            $user = $this->model->where('id', $id)->first();

            if ($user) {
                $user->password = Hash::make($request['password']);
                $user->save();
                return [
                    'user' => $user,
                    'success' => true,
                ];
            }
            return [
                'success' => false,
                'message' => __('messages.user_not_found')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::where('id', $id)->first();
            $user->status = -1;
            $user->save();

            $user->delete();
            return [
                'message' => 'messages.delete_success',
                'user' => UserResource::make($user),
                'success' => true,
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
    }

    public function createUser($request)
    {
        DB::beginTransaction();
        try {
            $this->validatedData = $request->validated();
            $this->handleTrashedUserByPhoneNumber();

            $user = $this->save();
            $this->createContact($user);

            DB::commit();

            return ['user' => UserResource::make($user->load('contacts')), 'success' => true];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateUser($request, $id)
    {
        try {
            $this->validatedData = $request->validated();
            $user = $this->model->find($id);
            $user = $this->save($user);

            return ['user' => UserResource::make($user->load('contacts')), 'success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function save(User $user = null): User
    {
        $isNewUser = !$user;
        if ($isNewUser) {
            $user = new User();
        }

        if (isset($this->validatedData['email'])) {
            $user->email = $this->validatedData['email'];
        }
        if (isset($this->validatedData['contact'])) {
            $user->code = sanitizeNumber($this->validatedData['contact'] ?? '');
        }
        if (isset($this->validatedData['name'])) {
            $user->first_name = $this->validatedData['name'];
        }
        if (isset($this->validatedData['password'])) {
            $user->password = Hash::make($this->validatedData['password']);
        }
        if (isset($this->phone_number)) {
            $user->code = $this->phone_number;
        }
        if (isset($this->validatedData['status'])) {
            $user->status = $this->validatedData['status'];
        }
        if (isset($this->validatedData['company_id'])) {
            $user->company_id = $data['company_id'] ?? User::where('code', $this->validatedData['recommender'] ?? '')->value('company_id');
        }
        if (isset($this->validatedData['bank_id'])) {
            $user->bank_id = $this->validatedData['bank_id'];
        }
        if (isset($this->validatedData['account_number'])) {
            $user->bank_account_no = $this->validatedData['account_number'];
        }
        if (isset($this->validatedData['dob'])) {
            $user->dob = $this->validatedData['dob'];
        }
        if (isset($this->validatedData['gender'])) {
            $user->gender =  strtolower($this->validatedData['gender']);
        }
        if (isset($this->validatedData['parent_id'])) {
            $user->parent_id = User::where('code', $this->validatedData['recommender'] ?? '')->value('id');
        }

        $user->save();

        event($isNewUser ? new UserCreated($this->model, $user) : new UserUpdated($this->model, $user));

        return $user;
    }

    private function createContact(User $user)
    {
        $contactData = array_filter([
            'telephone_1'    => sanitizeNumber($this->validatedData['contact'] ?? '') ?? null,
            'state'          => $this->validatedData['state'] ?? null,
            'post_code'      => $this->validatedData['post_code'] ?? null,
            'address'        => $this->validatedData['address'] ?? null,
            'address_detail' => $this->validatedData['address_detail'] ?? null,
        ], function ($value) {
            return !is_null($value);
        });

        $user->contacts()->create($contactData);
    }

    private function handleTrashedUserByPhoneNumber()
    {
        $existingUser = User::withTrashed()->where('code', $this->phone_number)->first();
        if ($existingUser && $existingUser->trashed()) {
            $existingUser->forceDelete();
        }
    }

    public function singleUser($id)
    {
        try {
            $user = User::where('id', $id)->first();

            return [
                'user' => UserResource::make($user->load('contacts')),
                'success' => true,
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
    }
    public function getReferralUser(mixed $parent_id)
    {
        if ($parent_id) {
            $refuser = User::find($parent_id);
            return $refuser ? [
                'code' => $refuser->code,
                'name' => $this->model->full_name,
                'telephone_1' => $this->model->contacts()->exists() ? $this->model->contacts()->first()->telephone_1 : null,
            ] : [];

        }

        return [];
    }
    public function getChannel($user_id)
    {
        try {
            $user = User::with('contacts')->find($user_id);
            if (!$user) {
                return ['success' => false, 'message' => __('messages.user_not_found')];
            }
            $userAdminChannelSettings = merge_all_channel_data($user->adminChannelSettings);
            if (!count($userAdminChannelSettings)) {
                return ['success' => false, 'message' => __('messages.user_channel_setting_not_found')];
            }

            return [
                'user' => UserResource::make($user),
                'data' => $userAdminChannelSettings,
                'success' => true,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param $request
     * @return string
     */

    public function memberApplication($request): string
    {
        $currentDate = now();
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        if ($currentDate >= $startDate && $currentDate <= $endDate) {
            return 'Royal';
        } else {
            return 'N';
        }
    }

    public function updateChannelSettings(array $data, $user_id)
    {
        $syncData = [];
        try {
            $user = User::find($user_id);
            if (!empty($data) && !empty($data['products'])) {
                foreach ($data['products'] as $pid) {
                    $syncData[$pid] = [
                        'url'    => $data['url_' . $pid] ? $data['url_' . $pid] : '',
                        'status' => $data['prod_expose_' . $pid] == 'on'
                    ];
                }
            }
            $user->adminChannelSettings()->sync($syncData, false);
            return [
                'products' => $user->adminChannelSettings()->whereIn('product_id', $data['products'])->get(),
                'success' => true,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

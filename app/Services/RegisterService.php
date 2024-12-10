<?php


    namespace App\Services;


    use App\Events\UserCreated;
    use App\Models\User;
    use Illuminate\Support\Facades\DB;

    class RegisterService
    {
        protected $phone_number;
        private array $validatedRequestData;
        private user $user;

        private $registered_platform;

        public function registerUser($request)
        {
            DB::beginTransaction();
            try{
                $this->validatedRequestData = $request->validated();
                $this->phone_number = sanitizeNumber($this->validatedRequestData['phone']);

                // check if user is mobile or web
                $userAgent = $request->header('User-Agent');
                $this->registered_platform = 'web';
                if (preg_match('/mobile/i', $userAgent) || preg_match('/dart/i', $userAgent)) {
                    $this->registered_platform = 'mobile';
                }

                $this->handleTrashedUserByPhoneNumber();
                $this->prepareData();
                $this->createUser();
                $this->createUserContact();


                DB::commit();
                return [
                    'user' => $this->user->load('contacts'),
                    'message' => 'user registred',
                    'success' => true,
                ];

                // if ($request->file('bankbook_photo')) {
                //     $media = $this->uploadBankbookImage($request->file('bankbook_photo'));
                //     $user->attachMedia($media, ['bankbook']);
                // }
                // if ($request->file('id_photo')) {
                //     $media = $this->uploadIdCardImage($request->file('id_photo'));
                //     $user->attachMedia($media, ['idCard']);
                // }
            } catch(\Exception $e){
                DB::rollback();
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];;
            }
        }

        private function prepareData()
        {
            $this->decryptPassword();
            $this->setStatus();
            $this->updateUserWithRecommender();

        }

        private function createUser()
        {
            $data = [
                'email'              => $this->validatedRequestData['email'],
                'first_name'         => $this->validatedRequestData['first_name'],
                'password'           => $this->validatedRequestData['password'],
                'code'               => $this->phone_number,
                'status'             => $this->validatedRequestData['status'],
                'company_id'         => $this->validatedRequestData['company_id'],
                // 'id_photo' => $idPhotoPath,
                //'bankbook_photo' => $bankbookPhotoPath,
                'bank_id'            => ($this->validatedRequestData['bank_id']) ?? '',
                'bank_account_no'    => ($this->validatedRequestData['account_number']) ?? '',
                'dob'                => ($this->validatedRequestData['dob']) ?? '1991-01-01',
                'gender'             => ($this->validatedRequestData['gender']) ?? 'male',
                'registered_platform' => $this->registered_platform,
            ];

            $this->user = User::create($data);
            $data = [
                'recommender'     => $this->validatedRequestData['referral_code'] ?? '',
                'biz-planner-reg' => true
            ];
            event(new UserCreated($this->user, $data));
        }

        private function createUserContact()
        {
            $this->user->contacts()->create([
                'telephone_1'    => $this->validatedRequestData['phone'] ?? null,
                'state'          => $this->validatedRequestData['state'] ?? null,
                'post_code'      => $this->validatedRequestData['post_code'] ?? null,
                'address'        => $this->validatedRequestData['address'] ?? null,
                'address_detail' => $this->validatedRequestData['address_detail'] ?? null,
            ]);
        }

        private function updateUserWithRecommender()
        {
            $this->validatedRequestData['company_id'] = NULL;
            if (isset($this->validatedRequestData['referral_code_verified'])) {
                $recommender = User::where('code', $this->validatedRequestData['referral_code'])->first();
                if ($recommender && $recommender->company_id) {
                    $this->validatedRequestData['company_id'] = $recommender->company_id;
                }
            }
        }

        private function setStatus()
        {
            $this->validatedRequestData['status'] = 0;
        }

        private function handleTrashedUserByPhoneNumber()
        {
            $existingUser = User::withTrashed()->where('code', $this->phone_number)->first();
            if ($existingUser && $existingUser->trashed()) {
                $existingUser->forceDelete();
            }
        }

        private function decryptPassword()
        {
            $this->validatedRequestData['password'] = bcrypt($this->validatedRequestData['password']);
        }

    }

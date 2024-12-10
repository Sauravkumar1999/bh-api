<?php

namespace App\Services;

use App\Events\UserLoggedOut;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserNotFoundException;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HelpersTraits;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationService
{
    const REFRESH_TOKEN = 'refreshToken';
    private $apiConsumer;
    private $auth;
    private $cookie;
    private $db;
    private $request;

    public function __construct(Application $app)
    {
        $this->apiConsumer = $app->make('apiconsumer');
        $this->auth = $app->make('auth');
        $this->cookie = $app->make('cookie');
        $this->db = $app->make('db');
        $this->request = $app->make('request');
    }

    /**
     * Attempt to create an access token using user credentials.
     *
     * @param string $email
     * @param string $password
     * @return array
     */

    public function attemptLogin(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::where('code', $email)->first();
            if (!$user) {
                throw new UserNotFoundException();
            }
        }

        if (!password_verify($password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        return $this->proxy('password', [
            'user' => $user,
            'username' => $user->email,
            'password' => $password
        ]);
    }

    /**
     * Attempt to refresh the access token used a refresh token that
     * has been saved in a cookie.
     */
    public function attemptRefresh($refresh_token = null)
    {
        $refreshToken = $refresh_token ??    $this->request->cookie(self::REFRESH_TOKEN);

        try {
            return $this->proxy('refresh_token', [
                'refresh_token' => $refreshToken
            ]);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => '`refreshToken` cookie is missing !',
                'status_code' => 422
            ];
        }
    }

    /**
     * Attempt to refresh the access token using username & password.
     */
    public function attemptRefreshMobile(string $email, string $password)
    {
        try {
            return $this->proxy('password', [
                'username'      => $email,
                'password'      => $password,
                'scope'         => '',
            ]);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Proxy a request to the OAuth server.
     *
     * @param string $grantType what type of grant type should be proxied.
     * @param array $datas the data to send to the server.
     */
    public function proxy(string $grantType, array $datas = []): array
    {
        $data = array_merge($datas, [
            'client_id'     => config('passport.password_client_id'),
            'client_secret' => config('passport.password_client_secret'),
            'grant_type'    => $grantType
        ]);

        $response = $this->apiConsumer->post('/oauth/token', $data);

        if (!$response->isSuccessful()) {
            throw new Exception($response);
        }

        $data = json_decode($response->getContent());

        // Create a refresh token cookie
        // The reason why you should save the refresh token as a HttpOnly cookie is to prevent Cross-site scripting (XSS) attacks.
        // The HttpOnly flag tells the browser that this cookie should not be accessible through javascript.
        $cookie = cookie(
            self::REFRESH_TOKEN,
            $data->refresh_token,
            864000, // 10 days
            null,
            null,
            false,
            true // HttpOnly
        );
        $user = isset($datas['user']) ? new UserResource($datas['user']) : null;

        return [
            'access_token' => $data->access_token,
            'expires_in'   => $data->expires_in,
            'user_data'    => $user,
            'cookie'       => $cookie,
            'refresh_token' => $data->refresh_token,
            'status_code' => 200
        ];
    }

    /**
     * Logs out the user. We revoke access token and refresh token.
     * Also instruct the client to forget the refresh cookie.
     */
    public function logout()
    {
        try {
            event(new UserLoggedOut($this->auth->user()));
            $user = $this->auth->user();
            $accessToken =  $user->token();

            $this->db
                ->table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->update([
                    'revoked' => true
                ]);

            $accessToken->revoke();

            $this->cookie->queue($this->cookie->forget(self::REFRESH_TOKEN));
            return [
                'success' => true,
                'user' => $user,
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
    }

    public function checkContact($name, $email, $phone, $type = null)
    {
        try {
            $query = User::whereHas('contacts', function ($query) use ($phone) {
                $query->where('telephone_1', $phone);
            });

            if ($type === 'find-password') {
                $query->where('email', $email)
                    ->where('first_name', $name);
            } else {
                $query->where('first_name', 'LIKE', "%$name%");
            }

            $user = $query->first();

            return [
                'success' => (bool) $user,
                'user' => $user ?: null,
                'message' => $user ? __('messages.user_verified') : __('messages.user_not_found')
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
    }

    public function checkUserApproved(string $email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::where('code', $email)->first();
            if (!$user) {
                throw new UserNotFoundException("User not found.");
            }
        }
        if ($user->status != 1) {
            return false;
        }

        return true;
    }

    public function checkAccountDeleted(string $email)
    {
        $user = User::where('email', $email)->withTrashed()->first();
        if (!$user) {
            $user = User::where('code', $email)->withTrashed()->first();
            if (!$user) {
                return false;
            }
        }
        return $user->trashed();
    }
}

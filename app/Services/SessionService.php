<?php

namespace App\Services;

use App\Models\Session;
use Illuminate\Database\Eloquent\SoftDeletes;


class SessionService
{
    use SoftDeletes;

    private function validateSessionData($data)
    {
        return validator($data, [
            'user_id' => 'required|integer',
            'key' => 'required|string',
            'value' => 'required|string',
            'expires_at' => 'required|date',
            'is_used' => 'required|boolean',
        ])->validate();
    }

    public function createSession($data)
    {
        $validatedData = $this->validateSessionData($data);

        return Session::create($validatedData);
    }

    public function updateSession($sessionId, $data)
    {
        $session = Session::find($sessionId);

        if ($session) {
            $validatedData = $this->validateSessionData($data);

            $session->update($validatedData);

            return $session;
        }

        return null;
    }

    public function deleteSession($sessionId)
    {
        $session = Session::find($sessionId);

        if ($session) {
            $session->delete();
            return true;
        }

        return false;
    }

    public function markOtpAsUsed($key)
    {
        $session = Session::where('value', $key)->first();

        if ($session) {
            $session->update(['is_used' => true]);
            return true;
        }

        return false;
    }

    public function getSessionById($sessionId)
    {
        return Session::find($sessionId);
    }

    public function getSessionByKey($key, $value)
    {
        return Session::where('key', $key)->where('value', $value)->first();
    }

}
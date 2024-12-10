<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class DeviceToken extends Model
{
    use HasFactory;

    protected $table = 'device_tokens';
    protected $fillable = ['user_id', 'fcm_token', 'push_yn','uuid'];

    /**
     * User relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class UserSetting extends Model
{
    use Mediable;

    protected $table = 'user_settings';

    protected $fillable = [
        'image_register',
        'text_register',
        'portfolio',
        'email',
        'telephone',
        'text_registration',
        'sns',
        'channel_ordering'
    ];

    protected $casts = [
        'sns'              => 'array',
        'channel_ordering' => 'array',
        'image_register'   => 'boolean',
        'text_register'    => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

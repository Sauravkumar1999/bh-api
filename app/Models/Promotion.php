<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use App\Helpers\Helpers;

class Promotion extends Model
{
    use  Mediable;

    protected $table = 'promotions';

    protected $fillable = [
        'slug',
        'promo_type',
        'title',
        'content',
        'agent',
        'section',
        'banner_img',
        'status',
        'start_at',
        'expired_at',
        'user_id',
    ];

    protected $casts = [
        'section'=> 'array',
        'agent' => 'array'
    ];

    // get the promotion_img
    public function promotion_img()
    {
        return mediaUrl($this->firstMedia('promotion_img'));
    }
}

<?php

namespace App\Models;

use App\Traits\BaseModelTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;
use App\Helpers\Helpers;


class Channel extends Model
{
    use HasFactory;
    use SoftDeletes, Mediable, BaseModelTraits;

    protected $table = 'channels';

    protected $fillable = [
        'code',
        'channel_name',
        'channel_description',
        'channel_price',
        'commission_type',
        'main_url',
        'url_params',
        'url_1',
        'url_2',
        'urls_open_mode',
        'sale_rights_disclosure',
        'approval_rights_disclosure',
        'group',
        'branch_representative',
        'referral_bonus',
        'other_fees',
        'bh_operating_profit',
        'user_id',
        'exposer_order',
        'channel_commissions',
        'bh_sale_commissions',
        'sale_status',
        'contact_notifications',
    ];

    protected $casts = [
        'sale_rights'         => 'array',
        'approval_rights'     => 'array',
        'channel_commissions' => 'array',
        'url_params'          => 'array',
    ];

    // get the banner image
    public function banner()
    {
        return mediaUrl($this->firstMedia('banner'), asset('another-default-image.png'));
    }

    // get the mobile banner image
    public function mobileBanner()
    {
        return $this->hasMedia('mobileBanner') ? mediaUrl($this->firstMedia('mobileBanner')): $this->banner();
    }
    // ===== Relations section ======

    //channel created user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }
    public function saleRights()
    {
        return $this->morphedByMany(Company::class, 'rightable')
            ->withPivot(['type', 'odr_app', 'channel_expose']);
    }

    public function approvalRights()
    {
        return $this->morphedByMany(User::class, 'rightable')
            ->withPivot(['type', 'odr_app', 'channel_expose']);
    }

    public function adminUserSettings()
    {
        return $this->belongsToMany(User::class, 'channel_user_settings', 'channel_id', 'user_id')
            ->withPivot('url', 'status');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

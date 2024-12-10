<?php

namespace App\Models;

use App\Traits\BaseModelTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes, Mediable, BaseModelTraits;
    protected $table = 'products';

    protected $fillable = [
        'code',
        'product_name',
        'product_description',
        'product_price',
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
        'company_id',
        'channel_id',
        'exposer_order',
        'product_commissions',
        'bh_sale_commissions',
        'sale_status',
        'contact_notifications',
    ];

    protected $casts = [
        'sale_rights'         => 'array',
        'approval_rights'     => 'array',
        'product_commissions' => 'array',
        'url_params'          => 'array',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function banner()
    {
        return mediaUrl($this->firstMedia('banner'), asset('another-default-image.png'));
    }

    public function saleRights()
    {
        return $this->morphedByMany(Company::class, 'product_rightables')
            ->withPivot(['type', 'odr_app', 'product_expose']);
    }

    public function approvalRights()
    {
        return $this->morphedByMany(User::class, 'product_rightables')
            ->withPivot(['type', 'odr_app', 'product_expose']);
    }

    public function company()
    {
        return $this->belongsTo(ProductCompany::class, 'company_id');
    }

    public function adminUserSettings()
    {
        return $this->belongsToMany(User::class, 'channel_user_settings', 'channel_id', 'user_id')
            ->withPivot('url', 'status');
    }
}

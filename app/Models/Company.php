<?php

namespace App\Models;

use App\Traits\BaseModelTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class Company extends Model
{

    use HasFactory;
    use SoftDeletes, Mediable, BaseModelTraits;

    protected $table = 'companies';

    protected $fillable = [
        'name', 'code', 'business_name', 'representative_name', 'registration_number', 'address',  'status'
    ];

    protected $casts = [];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function channels()
    {
        return $this->hasMany(Channel::class);
    }
    public function logo()
    {
        return $this->hasMedia('logo') ? route('media.image.display', $this->firstMedia('logo')) : null;
    }

    public function contract()
    {
        return $this->hasMedia('contract') ? route('media.image.display', $this->firstMedia('contract')) :
            asset('images/no-image.png');
    }

    public function channelRights()
    {
        return $this->morphToMany(Channel::class, 'rightable')
            ->when(!is_admin_user(), function ($q) {
                return $q->where('sale_status', 'normal');
            })
            ->withPivot(['type', 'odr_app', 'channel_expose']);
    }

    public function productRights()
    {
        return $this->morphToMany(Product::class, 'product_rightables')
            ->when(!is_admin_user(), function ($q) {
                return $q->where('sale_status', 'normal');
            })
            ->withPivot(['type', 'odr_app', 'product_expose']);
    }
}

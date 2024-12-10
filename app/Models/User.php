<?php

namespace App\Models;

use App\Traits\BaseModelTraits;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kalnoy\Nestedset\NodeTrait;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Passport\HasApiTokens;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use BaseModelTraits, LaratrustUserTrait, HasApiTokens, Notifiable, Mediable, NodeTrait, SoftDeletes, LogsActivity, BaseModelTraits, HasFactory {
        Mediable::newCollection insteadof NodeTrait;
    }

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code', 'first_name', 'last_name', 'username', 'email', 'dob', 'gender', 'password', 'status', 'last_login', 'final_confirmation', 'company_id', 'parent_id', 'user_type', 'submitted_date', 'deposit_date', 'start_date', 'end_date', 'registered_platform'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login'        => 'datetime',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isProAdmin()
    {
        return $this->hasRole('admin|developer');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin|developer|chief');
    }

    public function scopeWhereEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    public function scopeWhereCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function userWhereRole($role)
    {
        return $this->hasRole($role);
    }
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    public function userSetting()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function allowance()
    {
        return $this->hasMany(Allowance::class, 'member_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(
                [
                    'first_name',
                    'dob',
                    'gender',
                    'password',
                    'company_id',
                    'user_type',
                    'submitted_date',
                    'deposit_date',
                    'start_date',
                    'end_date'
                ])->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'prod_user_settings', 'user_id', 'product_id');
    }

    public function channelRights()
    {
        return $this->morphToMany(Channel::class, 'rightable')
            ->withPivot(['type', 'odr_app', 'channel_expose']);
    }

    public function productRights()
    {
        return $this->morphToMany(Product::class, 'product_rightables')
            ->withPivot(['type', 'odr_app', 'product_expose']);
    }
    public function bankbook()
    {
        return $this->hasMedia('bankbook') ? route('media.image.display', $this->firstMedia('bankbook')) :
            asset('images/no-image.png');
    }
    public function idCard()
    {
        return $this->hasMedia('idCard') ? route('media.image.display', $this->firstMedia('idCard')) :
            asset('images/no-image.png');
    }

    public function salesPersonImage()
    {
        return mediaUrl($this->firstMedia('sales-person'), asset('another-default-image.png'));
    }
    public function adminChannelSettings()
    {
        return $this->belongsToMany(Product::class, 'channel_user_settings', 'user_id', 'channel_id')
            ->withPivot('url', 'status');
    }

    public function parent()
    {
        return $this->belongsTo(User::class,'parent_id');
    }
}

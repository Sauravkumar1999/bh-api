<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;
use App\Traits\BaseModelTraits;


//use Modules\PushNotification\Entities\PushNotification;
//use Modules\User\Scopes\ExcludeDevRoles;

class Role extends LaratrustRole
{
    use BaseModelTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'description', 'order',
    ];

    public static $PRO_ADMIN_ROLES = ['developer', 'admin'];

//    protected static function booted()
//    {
//        // global scope to exclude dev roles for system logins
//        if (auth()->check() && !auth()->user()->isProAdmin()) {
//            static::addGlobalScope(new ExcludeDevRoles());
//        }
//    }

    public function scopeProAdminRoles($query)
    {
        return $query->whereIn('name', self::$PRO_ADMIN_ROLES);
    }

    public function scopeExcludeProAdminRoles($query)
    {
        return $query->whereNotIn('name', self::$PRO_ADMIN_ROLES);
    }

//    public function notifications()
//    {
//        return $this->morphToMany(PushNotification::class, 'notifiable');
//    }
}

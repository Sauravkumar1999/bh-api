<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    public $timestamps = false;


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'value', 'is_doc'];

    /**
     * Scope to only include by prefix.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $prefix
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrefix($query, $prefix = 'default')
    {
        return $query->where('key', 'like', $prefix . '.%');
    }
}

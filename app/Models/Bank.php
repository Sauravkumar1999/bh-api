<?php

namespace App\Models;

use App\Traits\BaseModelTraits;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use BaseModelTraits;

    protected $fillable = [
        'bank_name', 'display_name', 'status'
    ];

    public function scopeWhereStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}

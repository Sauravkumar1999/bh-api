<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BaseModelTraits;

class Allowance extends Model
{
    use HasFactory;
    use SoftDeletes, BaseModelTraits;

    protected $guarded = ['id'];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    
}

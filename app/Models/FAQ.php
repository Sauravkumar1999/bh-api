<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BaseModelTraits;


class FAQ extends Model
{
    use HasFactory;
    use BaseModelTraits;

    protected $table = 'faqs';

    protected $fillable = [
        'title', 'description', 'user_id', 'status'
    ];
}

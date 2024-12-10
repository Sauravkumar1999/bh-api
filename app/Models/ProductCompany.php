<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class ProductCompany extends Model
{
    use HasFactory, SoftDeletes, Mediable;

    protected $table = 'product_companies';
    protected $fillable = [
        'name',
        'status',
        'url',
        'business_name',
        'representative_name',
        'registration_number',
        'address',
        'registration_date',
    ];
    protected $casts = [];


}

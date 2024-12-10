<?php

namespace App\Models;


use App\Traits\BaseModelTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class Sale extends Model
{
    use HasFactory;
    use SoftDeletes, Mediable, BaseModelTraits;
    protected $table = 'sales';

    protected $fillable = [
        'product_id',
        'product_sale_day',
        'remark',
        'user_id',
        'fee_type',
        'product_price',
        'sales_price',
        'sales_information',
        'seller_id',
        'number_of_sales',
        'take',
        'operating_income',
        'sales_status',
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }



}

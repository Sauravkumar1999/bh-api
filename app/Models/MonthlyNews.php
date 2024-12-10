<?php

namespace App\Models;

use App\Traits\BaseModelTraits;
use Illuminate\Database\Eloquent\Model;

class MonthlyNews extends Model
{
    use BaseModelTraits;
    protected $table = 'monthly_news';

    protected $fillable = ['detail', 'form', 'posting_date'];
}

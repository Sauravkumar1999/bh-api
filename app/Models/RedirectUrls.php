<?php

namespace App\Models;

use App\Traits\BaseModelTraits;
use Illuminate\Database\Eloquent\Model;

class RedirectUrls extends Model
{
    use BaseModelTraits;

    protected $fillable = ['url', 'type', 'name'];

}
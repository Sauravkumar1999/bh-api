<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BaseModelTraits;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class AllowancePayment extends Model
{
    use Mediable, BaseModelTraits, HasFactory;
    protected $guarded = ['id'];
    protected $fillable = ['title','detail','user_id'];

    public function writer()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes();
    }
    public function attachment()
    {
        return $this->hasMedia('allowance-payment') ? route('media.image.display', $this->firstMedia('allowance-payment')) :
            asset('images/no-image.png');
    }
}

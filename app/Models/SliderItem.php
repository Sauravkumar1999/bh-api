<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Plank\Mediable\Mediable;
use App\Helpers\Helpers;

class SliderItem extends Model
{
    use HasFactory, Mediable;

    protected $fillable = [
        'name',
        'title',
        'caption',
        'url',
        'custom_html',
        'status',
        'slider_id',
    ];
    public function slider()
    {
        return $this->belongsTo(Slider::class, 'slider_id');
    }

    public function image()
    {
        return mediaUrl($this->firstMedia('image'));
    }
}

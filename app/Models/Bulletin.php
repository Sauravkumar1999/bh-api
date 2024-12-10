<?php

namespace App\Models;

use App\Traits\BaseModelTraits;
use App\Traits\UploadMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class Bulletin extends Model
{
    use HasFactory;
    use SoftDeletes, Mediable, BaseModelTraits, UploadMedia;

    protected $table = 'bulletins';
    protected $fillable = ['title', 'distinguish', 'attachment', 'permission', 'content', 'user_id', 'deleted_at', 'type' ,'due_date'];

    public function bulletin()
    {
        return mediaUrl($this->firstMedia('attachment'), null);
    }

}

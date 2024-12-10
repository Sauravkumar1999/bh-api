<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BaseModelTraits;

class Notifiable extends Model 
{
  use BaseModelTraits;

  protected $table = "notifiables";
}
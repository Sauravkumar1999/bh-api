<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BaseModelTraits;

class PushNotification extends Model 
{
  use BaseModelTraits;

  protected $table = "push_notifications";
}
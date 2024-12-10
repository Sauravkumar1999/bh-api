<?php 

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id'            => $this->id,
      'title'         => $this->title,
      'contents'      => $this->contents,
      'device'        => $this->device,
      'date'          => $this->created_at
    ];
  }
}
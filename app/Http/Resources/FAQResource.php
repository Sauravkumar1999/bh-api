<?php 
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FAQResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id'          => $this->id,
      'title'       => $this->title,
      'description' => $this->description,
      'user_id'     => $this->user_id,
      'status'      => $this->status,
      'created_at'  => $this->created_at,
      'updated_at'  => $this->updated_at
    ];
  }
}
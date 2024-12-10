<?php

// app/Http/Resources/ReferralResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'company' => optional($this->company)->name,
            'role' => $this->roles->isNotEmpty() ? $this->roles->first()->name : 'NA',
            'personal_code' => $this->code,
            'children' => $this->children
        ];
    }
}

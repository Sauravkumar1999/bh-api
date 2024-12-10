<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SnsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = json_decode($this->resource, true);
        if (!$data) {
            return null;
        }
        $platforms = [];
        if ($data['facebook']['status'] !== "0") {
            $platforms['facebook_url'] = isset($data['facebook']['url']) ? $data['facebook']['url'] : null;
        }
        if ($data['instagram']['status'] !== "0") {
            $platforms['instagram_url'] = isset($data['instagram']['url']) ? $data['instagram']['url'] : null;
        }
        if ($data['kakaotalk']['status'] !== "0") {
            $platforms['kakaotalk_url'] = isset($data['kakaotalk']['url']) ? $data['kakaotalk']['url'] : null;
        }
        if ($data['blog']['status'] !== "0") {
            $platforms['blog_url'] = isset($data['blog']['url']) ? $data['blog']['url'] : null;
        }
        return $platforms;
    }
}

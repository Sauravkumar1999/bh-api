<?php

    namespace App\Http\Resources;

    use Illuminate\Http\Resources\Json\JsonResource;

    class SliderItemsResource extends JsonResource
    {
        /**
         * Transform the resource into an array.
         *
         * @param  \Illuminate\Http\Request  $request
         *
         * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
         */
        public function toArray($request)
        {
            if($this->status){
                return [
                    'id' => $this->id,
                    'custom_html' => $this->custom_html,
                    'image' => $this->image(),
                    'url' => $this->url,
                ];
            }
        }
    }

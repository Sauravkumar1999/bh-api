<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="MonthlyNewsResource",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Monthly News successfully created"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="detail", type="string", example="Testing Detail"),
 *         @OA\Property(property="form", type="string", example="Testing Form String"),
 *         @OA\Property(property="posting_date", type="string", example="2024-10-15"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-19T14:01:33.000000Z"),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-19T14:01:33.000000Z"),
 *         @OA\Property(property="id", type="integer", example=12)
 *     )
 * )
 */

class MonthlyNewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'detail' => $this->detail,
            'form' => $this->form,
            'posting_date' => $this->posting_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}

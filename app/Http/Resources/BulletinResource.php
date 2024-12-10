<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

    class BulletinResource extends JsonResource
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

            $foramtedDate = Carbon::parse($this->due_date);

            return [
                'id' => $this->id,
                'title' => $this->title,
                'attachment' => $this->bulletin(),
                'type'    => $this->type,
                'content' => $this->content,
                'created_at' => $this->created_at,
                'due_date' => isset($this->due_date) ? $this->due_date : null,
                'due_date_formatted' => isset($this->due_date) ? __('messages.due_date', [
                   'year' => $foramtedDate->format('Y'),
                   'month' => $foramtedDate->isoFormat('MMMM'),
                   'month_name' =>  $foramtedDate->format('F'),
               ]) : null,
            ];
        }

        public function get_roles()
        {
            $permissionString = $this->permission;
            $cleanPermissionString = trim($permissionString, '[]"');
            $roleIds = explode(',', $cleanPermissionString);
            $roleIds = array_map('intval', $roleIds);
            return DB::table('roles')
                ->whereIn('id', $roleIds)
                ->pluck('display_name')
                ->toArray();
        }
    }

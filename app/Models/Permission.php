<?php

namespace App\Models;

use App\Traits\BaseModelTraits;
use Illuminate\Support\Facades\DB;
use Laratrust\Models\LaratrustPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//use Modules\Core\View\Components\Form\CheckBoxes;

class Permission extends LaratrustPermission
{
    use BaseModelTraits, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'description', 'ltpm'
    ];

    public function scopeGrouped($query)
    {
        DB::statement('SET group_concat_max_len = 1000000'); // default group_concat_max_len is 1024

        return $query->groupBy('ltpm')->selectRaw("SUBSTRING_INDEX(`ltpm`, '\\\\', -1) AS parent")
            ->selectRaw("CONCAT('[', GROUP_CONCAT(JSON_OBJECT('id', id, 'name', name, 'ltpm', SUBSTRING_INDEX(`ltpm`, '\\\\', -1), 'display_name', display_name,'description', description)), ']') AS children")
            ->orderBy('parent');

    }

    public static function permissionCheckBoxes()
    {
//        $fields = [];
//
//        $permissions = Permission::grouped()->get();
//
//        foreach ($permissions as $permission) {
//
//            $options = collect(json_decode($permission['children']));
//
//            $options = $options->pluck('display_name', 'name')->toArray();
//
//            $fields[] = Checkboxes::make(strtolower($permission['parent']), $permission['parent'])->options($options)
//                ->inline(true)->selectAll(true)->hr(true);
//        }
//
//        return $fields;
    }
}

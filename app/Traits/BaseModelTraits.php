<?php


namespace App\Traits;

use Carbon\Carbon;

trait BaseModelTraits
{
    public function scopeFilter($query, $filters)
    {
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                switch ($key) {
                    case 'code':
                        $query->where('code', $value);
                        break;
                    case 'product_name':
                        $query->where('product_name', 'like', '%' . $value . '%');
                        break;
                    case 'status':
                        $query->where('status', $value);
                        break;
                    case 'role':
                        $query->whereHas('roles', function ($query) use ($value) {
                            $query->where('display_name', 'LIKE', '%' . $value . '%')
                                ->orWhere('name', 'LIKE', '%' . $value . '%');
                        });
                        break;
                    case 'royal_member_application':
                        if (!is_numeric($value)) {
                            $lowercaseKeyword = strtolower($value);
                            if ($lowercaseKeyword === 'royal') {
                                $query->whereRaw('? BETWEEN start_date AND end_date', [now()]);
                            } elseif ($lowercaseKeyword === 'n') {
                                $query->where(function ($query) {
                                    $query->where('start_date', '>', now())
                                        ->orWhere('end_date', '<', now());
                                });
                            }
                        }
                        break;
                    case 'confirm_start_date':
                        if (!is_numeric($value)) {
                            $query->whereBetween('final_confirmation', [$filters['confirm_start_date'], $filters['confirm_end_date']])
                                ->orWhereBetween('created_at', [$filters['confirm_start_date'], $filters['confirm_end_date']]);
                        }
                        break;
                    case 'type':
                        $query->where('type', $value);
                        break;
                    case 'permission':
                        $query->where(function ($query) use ($value) {
                            $query->whereJsonContains('permission', $value)->orWhereNull('permission');
                        });
                        break;
                        // Add more cases as needed
                }
            }
        }
    }


    public function scopeFilterAndPaginate($query, $filters)
    {
        return filterAndPagination($query, $filters);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Contact extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'contacts';

    protected $fillable = [
        'telephone_1', 'telephone_2', 'address', 'state', 'post_code', 'user_id', 'address_detail'
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    // Log model changes
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(
                [
                    'telephone_1',
                    'address',
                    'address_detail',
                    'post_code',
                    'user_type',
                    'submitted_date',
                    'deposit_date',
                    'start_date',
                    'end_date'
                ]
            )->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

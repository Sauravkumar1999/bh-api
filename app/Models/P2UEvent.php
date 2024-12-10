<?php

namespace App\Models;

use App\Traits\BaseModelTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class P2UEvent extends Model
{
    use HasFactory, BaseModelTraits;

    protected $table = 'p2u_events';
    protected $fillable = ['p2u_amount', 'event_name_id', 'user_id', 'expires_at', 'transfer_status', 'device_id'];

    /**
     * Get the event that owns the P2UEvent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_name_id');
    }
}

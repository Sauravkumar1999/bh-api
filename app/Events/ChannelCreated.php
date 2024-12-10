<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class ChannelCreated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public $product)
    {
        //
    }
}

<?php

namespace App\Listeners;

use App\Events\ChannelCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\ChannelSequenceManager;

class IncreaseNextChannelCode
{
    Use ChannelSequenceManager;
    /**
     * Handle the event.
     *
     * @param ChannelCreated $event
     * @return void
     */
    public function handle(ChannelCreated $event)
    {
        // Update next product code
        $this->increaseNextChannelCode();
    }
}

<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Traits\ProductSequenceManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class IncreaseNextProductCode
{
    Use ProductSequenceManager;
    /**
     * Handle the event.
     *
     * @param ProductCreated $event
     * @return void
     */
    public function handle(ProductCreated $event)
    {
        // Update next product code
        $this->increaseNextProductCode();
    }
}

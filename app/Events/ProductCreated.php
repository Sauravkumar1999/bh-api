<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class ProductCreated
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

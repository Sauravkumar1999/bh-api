<?php


namespace App\Events;

use Illuminate\Queue\SerializesModels;

class UserCreated
{
    use SerializesModels;

    public $user;
    public $data;

    /**
     * Create a new event instance.
     *
     * @param  $user
     * @param $data
     */
    public function __construct($user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }
}

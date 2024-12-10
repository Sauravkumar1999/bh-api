<?php

namespace App\Traits;

trait ChannelSequenceManager
{
    /**
     * Generate next channel code
     *
     * @return string
     */
    public function getNextChannelCode()
    {
        $prefix = setting('channels.code_prefix', 'P');
        $next = setting('channels.code_next', '1');
        $digit = setting('channels.code_digit', '7');

        return $prefix . str_pad($next, $digit, '0', STR_PAD_LEFT);
    }

    /**
     * Increase the next channel code
     */
    public function increaseNextChannelCode()
    {
        $next = setting('channels.code_next', 1) + 1;

        setting(['channels.code_next' => $next])->save();
    }
}

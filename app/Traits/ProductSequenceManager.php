<?php

namespace App\Traits;


trait ProductSequenceManager
{
    /**
     * Generate next product code
     *
     * @return string
     */
    public function getNextProductCode()
    {
        $prefix = setting('products.code_prefix', 'P');
        $next = setting('products.code_next', '1');
        $digit = setting('products.code_digit', '7');

        return $prefix . str_pad($next, $digit, '0', STR_PAD_LEFT);
    }

    /**
     * Increase the next product code
     */
    public function increaseNextProductCode()
    {
        $next = setting('products.code_next', 1) + 1;

        setting(['products.code_next' => $next])->save();
    }
}

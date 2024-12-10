<?php

namespace App\Traits;

trait ManageCompanySquence
{
    /**
     * Generate Company next code
     * @return string
     */
    public function getCompanyNextCode()
    {
        $prefix = setting('companies.code_prefix', 'C');
        $next = setting('companies.code_next', '1');
        $digit = setting('companies.code_digit', '3');
        return $prefix . str_pad($next, $digit, '0', STR_PAD_LEFT);
    }


    /**
     * Increase the next Company code
     */
    public function increaseNextCompanyCode()
    {
        $next = setting('companies.code_next', 1) + 1;
        setting(['companies.code_next' => $next])->save();
    }  
}
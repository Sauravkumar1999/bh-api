<?php

namespace App\Listeners;


use App\Events\CompanyCreated;
use App\Traits\ManageCompanySquence;

class IncreaseNextCompanyCode
{
    use ManageCompanySquence;

    /**
     * Handle the event.
     *
     * @param CompanyCreated $event
     * @return void
     */
    public function handle(CompanyCreated $event)
    {
        // Update next company code
        $this->increaseNextCompanyCode();
    }
}

<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Models\User;

class SaveRecommender
{
    /**
     * Handle the event.
     *
     * @param UserCreated|UserUpdated $event
     * @return void
     */
    public function handle(UserCreated|UserUpdated $event)
    {
        $data = $event->data;
        $user = $event->user;
                
        if (isset($data['recommender']) && $data['recommender'] != '') {
            $recommender = User::query()->where('code', $data['recommender'])->first();

            if ($recommender) {
                $user->appendToNode($recommender)->save();

            }

        } else {
            // assign default MD user as the recommender if no recommender is mentioned
            $bh_official_recommender = User::query()->where([
                ['email', '=', 'bh.official-md@businesshub.co.kr'],
                ['code', '=', '01022222222'],
            ])->first();

            if ($bh_official_recommender && $bh_official_recommender->isNot($user)) {
                $user->appendToNode($bh_official_recommender)->save();
            }
        }
    }
}

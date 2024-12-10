<?php

namespace App\Services;

use App\Models\EventName;
use App\Models\P2UEvent;
use Carbon\Carbon;
use Exception;

class P2UEventService
{

    public function addDailyAttendance($request)
    {
        $get_category =  $this->getEventCategory(1);
        try {
            $p2uEvent = P2UEvent::create([
                'event_name_id'   => $get_category->id,
                'user_id'         => auth()->user()->id,
                'expires_at'      => null,
                'transfer_status' => 'pending',
                'device_id'       => isset($request->device_id) ? $request->device_id : null
            ]);
            if ($p2uEvent) {
                return $p2uEvent->load('event');
            }
            return false;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function fetchP2UEventPoint($filters)
    {
        return  P2UEvent::with('event')->where('user_id', auth()->user()->id)->filterAndPaginate($filters);
    }

    public function checkAttendenceForToday()
    {
        return P2UEvent::where('user_id', auth()->user()->id)->whereDate('created_at', Carbon::today())->exists();
    }

    public function getEventCategory($event_id){
        return EventName::findOrFail($event_id);
    }




}

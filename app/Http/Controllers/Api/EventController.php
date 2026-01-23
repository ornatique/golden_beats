<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function event_list()
    {
        $events = Event::orderBy('event_date', 'asc')
            ->get()
            ->map(function ($event) {
                return [
                    'id'          => $event->id,
                    'title'       => $event->title,
                    'event_date'  => $event->event_date,
                    'location'    => $event->location,
                    'event_type'  => $event->event_type,
                    'map_link'    => $event->map_link,
                    'description' => $event->description,
                    'image_url'   => $event->image
                        ? asset($event->image)
                        : null,
                    'created_at'  => $event->created_at->format('d M Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Events fetched successfully',
            'data'    => $events,
        ], 200);
    }
}

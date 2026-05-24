<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::active();

        if ($request->boolean('upcoming')) {
            $query->upcoming();
        } elseif ($request->boolean('past')) {
            $query->past();
        } else {
            $query->orderByDesc('event_date');
        }

        if ($cat = $request->string('category')->toString()) {
            if (array_key_exists($cat, Event::CATEGORIES)) {
                $query->where('category', $cat);
            }
        }

        $perPage = min(max((int) $request->input('per_page', 25), 1), 100);

        return EventResource::collection($query->paginate($perPage));
    }

    public function show(Event $event, Request $request)
    {
        abort_unless($event->is_active, 404);

        $request->merge(['with_content' => true]);

        return new EventResource($event);
    }
}

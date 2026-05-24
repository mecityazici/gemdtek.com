<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'summary' => $this->summary,
            'description' => $this->when($request->boolean('with_content'), $this->description),
            'event_date' => $this->event_date?->toIso8601String(),
            'location' => $this->location,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'registration_url' => $this->registration_url,
            'cover_url' => $this->cover_url,
            'is_upcoming' => $this->is_upcoming,
            'url' => route('events.show', $this),
        ];
    }
}

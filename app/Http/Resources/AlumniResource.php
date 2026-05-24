<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumniResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'bio' => $this->when($request->boolean('with_content'), $this->bio),
            'graduation_year' => $this->graduation_year,
            'sector' => $this->sector,
            'sector_label' => $this->sector_label,
            'company' => $this->company,
            'city' => $this->city,
            'linkedin_url' => $this->linkedin_url,
            'photo_url' => $this->photo_url,
        ];
    }
}

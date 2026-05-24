<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'summary' => $this->summary,
            'description' => $this->when($request->boolean('with_content'), $this->description),
            'problem_statement' => $this->when($request->boolean('with_content'), $this->problem_statement),
            'year' => $this->year,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'hero_url' => $this->hero_url,
            'specs' => $this->when(
                $request->boolean('with_specs') && $this->relationLoaded('specs'),
                fn () => $this->specs->map(fn ($s) => [
                    'category' => $s->category,
                    'key' => $s->key,
                    'value' => $s->value,
                ])
            ),
            'members' => $this->when(
                $request->boolean('with_members') && $this->relationLoaded('members'),
                fn () => $this->members->map(fn ($m) => [
                    'name' => $m->name,
                    'role' => $m->role,
                    'linkedin_url' => $m->linkedin_url,
                    'is_captain' => $m->is_captain,
                ])
            ),
            'url' => route('projects.show', $this),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->when($request->boolean('with_content'), $this->content),
            'category' => $this->category,
            'category_label' => $this->category_label,
            'published_at' => $this->published_at?->toIso8601String(),
            'cover_url' => $this->cover_url,
            'url' => route('news.show', $this),
        ];
    }
}

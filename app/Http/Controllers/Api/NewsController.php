<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsPostResource;
use App\Models\NewsPost;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsPost::published();

        if ($cat = $request->string('category')->toString()) {
            if (array_key_exists($cat, NewsPost::CATEGORIES)) {
                $query->where('category', $cat);
            }
        }

        $perPage = min(max((int) $request->input('per_page', 25), 1), 100);

        return NewsPostResource::collection($query->paginate($perPage));
    }

    public function show(NewsPost $post, Request $request)
    {
        abort_unless($post->is_published, 404);

        $request->merge(['with_content' => true]);

        return new NewsPostResource($post);
    }
}

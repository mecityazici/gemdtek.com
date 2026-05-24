<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::active();

        if ($status = $request->string('status')->toString()) {
            if (array_key_exists($status, Project::STATUSES)) {
                $query->where('status', $status);
            }
        }

        $perPage = min(max((int) $request->input('per_page', 25), 1), 100);

        return ProjectResource::collection($query->paginate($perPage));
    }

    public function show(Project $project, Request $request)
    {
        abort_unless($project->is_active, 404);

        $project->load(['specs', 'members']);

        $request->merge([
            'with_content' => true,
            'with_specs' => true,
            'with_members' => true,
        ]);

        return new ProjectResource($project);
    }
}

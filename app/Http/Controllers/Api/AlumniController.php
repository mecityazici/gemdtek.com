<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlumniResource;
use App\Models\Alumni;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $query = Alumni::public()->orderBy('order')->orderByDesc('graduation_year');

        if ($sector = $request->string('sector')->toString()) {
            if (array_key_exists($sector, Alumni::SECTORS)) {
                $query->where('sector', $sector);
            }
        }

        if ($year = (int) $request->input('year')) {
            if ($year >= 1980 && $year <= 2100) {
                $query->where('graduation_year', $year);
            }
        }

        $perPage = min(max((int) $request->input('per_page', 25), 1), 100);

        return AlumniResource::collection($query->paginate($perPage));
    }
}

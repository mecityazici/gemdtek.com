<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SponsorResource;
use App\Models\Sponsor;

class SponsorController extends Controller
{
    public function index()
    {
        return SponsorResource::collection(Sponsor::active()->get());
    }
}

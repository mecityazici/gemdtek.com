<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SponsorLead extends Model
{
    public const TIERS = [
        'platinum' => 'Platin',
        'gold'     => 'Altın',
        'silver'   => 'Gümüş',
        'bronze'   => 'Bronz',
        'destek'   => 'Destekleyen',
        'belirsiz' => 'Henüz karar verilmedi',
    ];

    protected $fillable = [
        'company_name', 'contact_name', 'contact_email', 'contact_role',
        'interest_tier', 'message', 'ip_address', 'source',
    ];
}

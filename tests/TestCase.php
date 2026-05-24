<?php

namespace Tests;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Seed the database once per migration cycle so feature tests
     * have realistic Sponsor / Project / Form / Event / Alumni rows.
     */
    protected $seed = true;

    protected $seeder = DatabaseSeeder::class;
}

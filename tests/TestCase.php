<?php

namespace Tests;

use Database\Seeders\TestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Test'ler hem altyapı (admin, ayarlar, izinler) hem demo içerik bekliyor.
     * Production DatabaseSeeder demo içeriği çağırmadığı için test'lerde
     * TestSeeder bundle'ını kullanıyoruz.
     */
    protected $seed = true;

    protected $seeder = TestSeeder::class;
}

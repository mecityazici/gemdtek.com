<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedInteger('capacity')->nullable()->after('registration_url');
            $table->boolean('registration_enabled')->default(false)->after('capacity');
            $table->timestamp('registration_deadline')->nullable()->after('registration_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['capacity', 'registration_enabled', 'registration_deadline']);
        });
    }
};

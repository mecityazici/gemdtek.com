<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('position');
            $table->json('bio')->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->string('sector')->default('diger');
            $table->string('company')->nullable();
            $table->string('city')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->boolean('is_public')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['is_public', 'order']);
            $table->index('sector');
            $table->index('graduation_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};

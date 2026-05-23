<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_leads', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_role')->nullable();
            $table->string('interest_tier')->nullable();
            $table->text('message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('source')->default('site');
            $table->timestamps();

            $table->index(['interest_tier', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_leads');
    }
};

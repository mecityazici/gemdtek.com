<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('name');
            $table->string('label');
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['form_id', 'order']);
            $table->unique(['form_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};

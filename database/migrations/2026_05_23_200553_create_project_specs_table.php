<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('category')->default('genel');
            $table->string('key');
            $table->string('value');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'category', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_specs');
    }
};

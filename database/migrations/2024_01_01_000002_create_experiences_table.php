<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('role');
            $table->string('period'); // e.g., '2023.07 - Now'
            $table->text('description');
            $table->json('technologies'); // ['Laravel', 'Vue.js', ...]
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};

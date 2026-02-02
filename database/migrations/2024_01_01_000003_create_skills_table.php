<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // 'Magento', 'GraphQL', etc.
            $table->string('icon'); // SVG path or icon name
            $table->text('description');
            $table->json('sub_skills'); // ['Apollo', 'Schema Design', ...]
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['category', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};

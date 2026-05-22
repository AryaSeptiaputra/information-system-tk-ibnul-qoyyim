<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('holidays')) {
            return;
        }

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('name', 150);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};

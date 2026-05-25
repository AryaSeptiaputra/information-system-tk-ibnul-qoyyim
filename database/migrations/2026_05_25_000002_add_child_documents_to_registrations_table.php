<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('photo_file_path')->nullable()->after('kk_file_path');
            $table->string('birth_certificate_file_path')->nullable()->after('photo_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['photo_file_path', 'birth_certificate_file_path']);
        });
    }
};

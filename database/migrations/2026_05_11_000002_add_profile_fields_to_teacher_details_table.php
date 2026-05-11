<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_details')) {
            return;
        }

        Schema::table('teacher_details', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_details', 'position')) {
                $table->string('position', 120)->nullable()->after('name');
            }
            if (!Schema::hasColumn('teacher_details', 'nip')) {
                $table->string('nip', 50)->nullable()->after('position');
            }
            if (!Schema::hasColumn('teacher_details', 'nuptk')) {
                $table->string('nuptk', 50)->nullable()->after('nip');
            }
            if (!Schema::hasColumn('teacher_details', 'birth_place')) {
                $table->string('birth_place', 100)->nullable()->after('nuptk');
            }
            if (!Schema::hasColumn('teacher_details', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('birth_place');
            }
            if (!Schema::hasColumn('teacher_details', 'start_work_date')) {
                $table->date('start_work_date')->nullable()->after('birth_date');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('teacher_details')) {
            return;
        }

        Schema::table('teacher_details', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_details', 'start_work_date')) {
                $table->dropColumn('start_work_date');
            }
            if (Schema::hasColumn('teacher_details', 'birth_date')) {
                $table->dropColumn('birth_date');
            }
            if (Schema::hasColumn('teacher_details', 'birth_place')) {
                $table->dropColumn('birth_place');
            }
            if (Schema::hasColumn('teacher_details', 'nuptk')) {
                $table->dropColumn('nuptk');
            }
            if (Schema::hasColumn('teacher_details', 'nip')) {
                $table->dropColumn('nip');
            }
            if (Schema::hasColumn('teacher_details', 'position')) {
                $table->dropColumn('position');
            }
        });
    }
};

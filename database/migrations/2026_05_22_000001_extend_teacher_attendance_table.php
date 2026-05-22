<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_attendance')) {
            return;
        }

        Schema::table('teacher_attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_attendance', 'check_in_time')) {
                $table->dateTime('check_in_time')->nullable()->after('date');
            }
            if (!Schema::hasColumn('teacher_attendance', 'is_late')) {
                $table->boolean('is_late')->default(false)->after('check_in_time');
            }
            if (!Schema::hasColumn('teacher_attendance', 'late_minutes')) {
                $table->unsignedSmallInteger('late_minutes')->nullable()->after('is_late');
            }
            if (!Schema::hasColumn('teacher_attendance', 'attachment_path')) {
                $table->string('attachment_path', 255)->nullable()->after('information');
            }
            if (!Schema::hasColumn('teacher_attendance', 'source')) {
                $table->enum('source', ['self', 'admin', 'auto_holiday'])
                    ->default('admin')
                    ->after('attachment_path');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('teacher_attendance')) {
            return;
        }

        Schema::table('teacher_attendance', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_attendance', 'source')) {
                $table->dropColumn('source');
            }
            if (Schema::hasColumn('teacher_attendance', 'attachment_path')) {
                $table->dropColumn('attachment_path');
            }
            if (Schema::hasColumn('teacher_attendance', 'late_minutes')) {
                $table->dropColumn('late_minutes');
            }
            if (Schema::hasColumn('teacher_attendance', 'is_late')) {
                $table->dropColumn('is_late');
            }
            if (Schema::hasColumn('teacher_attendance', 'check_in_time')) {
                $table->dropColumn('check_in_time');
            }
        });
    }
};

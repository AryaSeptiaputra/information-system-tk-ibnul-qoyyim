<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_honors')) {
            return;
        }

        Schema::table('teacher_honors', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_honors', 'workday_count')) {
                $table->unsignedSmallInteger('workday_count')->nullable()->after('absence_count');
            }
            if (!Schema::hasColumn('teacher_honors', 'holiday_credit_count')) {
                $table->unsignedSmallInteger('holiday_credit_count')->nullable()->after('workday_count');
            }
            if (!Schema::hasColumn('teacher_honors', 'effective_attendance_count')) {
                $table->unsignedSmallInteger('effective_attendance_count')->nullable()->after('holiday_credit_count');
            }
            if (!Schema::hasColumn('teacher_honors', 'late_count')) {
                $table->unsignedSmallInteger('late_count')->nullable()->after('effective_attendance_count');
            }
            if (!Schema::hasColumn('teacher_honors', 'late_penalty')) {
                $table->decimal('late_penalty', 12, 2)->default(0)->after('late_count');
            }
            if (!Schema::hasColumn('teacher_honors', 'permission_penalty')) {
                $table->decimal('permission_penalty', 12, 2)->default(0)->after('late_penalty');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('teacher_honors')) {
            return;
        }

        Schema::table('teacher_honors', function (Blueprint $table) {
            foreach (['permission_penalty', 'late_penalty', 'late_count', 'effective_attendance_count', 'holiday_credit_count', 'workday_count'] as $col) {
                if (Schema::hasColumn('teacher_honors', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

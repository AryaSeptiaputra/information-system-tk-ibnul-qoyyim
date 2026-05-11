<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('facilities')) {
            return;
        }

        Schema::table('facilities', function (Blueprint $table) {
            if (!Schema::hasColumn('facilities', 'fund_source')) {
                $table->string('fund_source', 120)->nullable()->after('condition');
            }
            if (!Schema::hasColumn('facilities', 'acquisition_year')) {
                $table->unsignedSmallInteger('acquisition_year')->nullable()->after('fund_source');
            }
            if (!Schema::hasColumn('facilities', 'category')) {
                $table->string('category', 120)->nullable()->after('acquisition_year');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('facilities')) {
            return;
        }

        Schema::table('facilities', function (Blueprint $table) {
            if (Schema::hasColumn('facilities', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('facilities', 'acquisition_year')) {
                $table->dropColumn('acquisition_year');
            }
            if (Schema::hasColumn('facilities', 'fund_source')) {
                $table->dropColumn('fund_source');
            }
        });
    }
};

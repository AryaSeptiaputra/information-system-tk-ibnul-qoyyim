<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'role')) {
            return;
        }

        // MySQL: ubah enum role agar menerima 'bendahara'. Pakai DB::statement
        // karena Schema builder Laravel tidak punya helper untuk MODIFY ENUM.
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('guest','superadmin','teacher','administration','headmaster','bendahara') NOT NULL DEFAULT 'guest'");
    }

    public function down(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'role')) {
            return;
        }

        // Demote semua row 'bendahara' ke 'administration' agar ALTER tidak gagal.
        DB::table('users')->where('role', 'bendahara')->update(['role' => 'administration']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('guest','superadmin','teacher','administration','headmaster') NOT NULL DEFAULT 'guest'");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop kolom role lama dan buat ulang
        DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS role');
        DB::statement("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'staff' CHECK (role IN ('direktur', 'manajer', 'staff'))");

        // Tambah manager_id
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'manager_id')) {
                $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS role');
        DB::statement("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'sales' CHECK (role IN ('admin', 'manager', 'sales'))");
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn('manager_id');
        });
    }
};
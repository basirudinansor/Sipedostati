<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('dosens', 'kuota_terpakai_p1')) {
            Schema::table('dosens', function (Blueprint $table) {
                $table->unsignedInteger('kuota_terpakai_p1')->default(0)->after('kuota');
            });
        }

        if (! Schema::hasColumn('dosens', 'kuota_terpakai_p2')) {
            Schema::table('dosens', function (Blueprint $table) {
                $table->unsignedInteger('kuota_terpakai_p2')->default(0)->after('kuota_terpakai_p1');
            });
        }

        if (Schema::hasColumn('dosens', 'kuota_terpakai')) {
            DB::statement('UPDATE dosens SET kuota_terpakai_p1 = kuota_terpakai');

            Schema::table('dosens', function (Blueprint $table) {
                $table->dropColumn('kuota_terpakai');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('dosens', 'kuota_terpakai')) {
            Schema::table('dosens', function (Blueprint $table) {
                $table->unsignedInteger('kuota_terpakai')->default(0)->after('kuota');
            });
        }

        if (Schema::hasColumn('dosens', 'kuota_terpakai_p1') && Schema::hasColumn('dosens', 'kuota_terpakai_p2')) {
            DB::statement('UPDATE dosens SET kuota_terpakai = kuota_terpakai_p1 + kuota_terpakai_p2');
        }

        $columnsToDrop = [];
        if (Schema::hasColumn('dosens', 'kuota_terpakai_p1')) {
            $columnsToDrop[] = 'kuota_terpakai_p1';
        }
        if (Schema::hasColumn('dosens', 'kuota_terpakai_p2')) {
            $columnsToDrop[] = 'kuota_terpakai_p2';
        }

        if ($columnsToDrop !== []) {
            Schema::table('dosens', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $indexName): bool
    {
        return count(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName])) > 0;
    }

    public function up(): void
    {
        if (! Schema::hasColumn('pilihans', 'jenis')) {
            Schema::table('pilihans', function (Blueprint $table) {
                $table->enum('jenis', ['pembimbing_1', 'pembimbing_2'])
                    ->default('pembimbing_1')
                    ->after('dosen_id');
            });
        }

        // Sediakan index biasa untuk foreign key mahasiswa_id sebelum unique lama dilepas.
        if (! $this->indexExists('pilihans', 'pilihans_mahasiswa_id_index')) {
            Schema::table('pilihans', function (Blueprint $table) {
                $table->index('mahasiswa_id', 'pilihans_mahasiswa_id_index');
            });
        }

        if ($this->indexExists('pilihans', 'pilihans_mahasiswa_id_unique')) {
            Schema::table('pilihans', function (Blueprint $table) {
                $table->dropUnique('pilihans_mahasiswa_id_unique');
            });
        }

        if (! $this->indexExists('pilihans', 'pilihans_mahasiswa_id_jenis_unique')) {
            Schema::table('pilihans', function (Blueprint $table) {
                $table->unique(
                    ['mahasiswa_id', 'jenis'],
                    'pilihans_mahasiswa_id_jenis_unique'
                );
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('pilihans', 'pilihans_mahasiswa_id_jenis_unique')) {
            Schema::table('pilihans', function (Blueprint $table) {
                $table->dropUnique('pilihans_mahasiswa_id_jenis_unique');
            });
        }

        if (! $this->indexExists('pilihans', 'pilihans_mahasiswa_id_unique')) {
            Schema::table('pilihans', function (Blueprint $table) {
                $table->unique('mahasiswa_id', 'pilihans_mahasiswa_id_unique');
            });
        }

        if (Schema::hasColumn('pilihans', 'jenis')) {
            Schema::table('pilihans', function (Blueprint $table) {
                $table->dropColumn('jenis');
            });
        }

        // Biarkan index biasa mahasiswa_id karena tetap berguna untuk foreign key.
    }
};

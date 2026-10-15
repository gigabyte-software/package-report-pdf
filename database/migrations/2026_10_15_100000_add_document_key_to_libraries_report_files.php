<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const STREAM_INDEX = 'libraries_report_files_stream_version_unique';

    private const LEGACY_INDEX = 'libraries_report_files_reportable_type_reportable_id_version_unique';

    /**
     * Earl Kendrick Core already added this column in
     * 2026_10_09_100000_add_document_key_to_libraries_report_files.
     * This migration is a no-op there, and applies the same change for other installs.
     */
    public function up(): void
    {
        if (! Schema::hasTable('libraries_report_files')) {
            return;
        }

        if (! Schema::hasColumn('libraries_report_files', 'document_key')) {
            Schema::table('libraries_report_files', function (Blueprint $table) {
                $table->string('document_key', 64)->nullable();
            });
        }

        if ($this->indexNames()->contains(self::STREAM_INDEX)) {
            return;
        }

        if ($this->indexNames()->contains(self::LEGACY_INDEX)) {
            Schema::table('libraries_report_files', function (Blueprint $table) {
                $table->dropUnique(['reportable_type', 'reportable_id', 'version']);
            });
        }

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            // COALESCE makes a null key one stream, so survey PDFs still have a single version sequence.
            DB::statement('CREATE UNIQUE INDEX '.self::STREAM_INDEX.' ON libraries_report_files (reportable_type, reportable_id, (COALESCE(document_key, \'\')), version)');

            return;
        }

        Schema::table('libraries_report_files', function (Blueprint $table) {
            $table->unique(['reportable_type', 'reportable_id', 'document_key', 'version'], self::STREAM_INDEX);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('libraries_report_files')) {
            return;
        }

        if (DB::table('migrations')->where('migration', '2026_10_09_100000_add_document_key_to_libraries_report_files')->exists()) {
            return;
        }

        if ($this->indexNames()->contains(self::STREAM_INDEX)) {
            if (Schema::getConnection()->getDriverName() === 'pgsql') {
                DB::statement('DROP INDEX IF EXISTS '.self::STREAM_INDEX);
            } else {
                Schema::table('libraries_report_files', function (Blueprint $table) {
                    $table->dropUnique(self::STREAM_INDEX);
                });
            }
        }

        if (Schema::hasColumn('libraries_report_files', 'document_key')) {
            Schema::table('libraries_report_files', function (Blueprint $table) {
                $table->dropColumn('document_key');
            });
        }

        if (! $this->indexNames()->contains(self::LEGACY_INDEX)) {
            Schema::table('libraries_report_files', function (Blueprint $table) {
                $table->unique(['reportable_type', 'reportable_id', 'version']);
            });
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function indexNames(): \Illuminate\Support\Collection
    {
        return collect(Schema::getIndexes('libraries_report_files'))->pluck('name');
    }
};

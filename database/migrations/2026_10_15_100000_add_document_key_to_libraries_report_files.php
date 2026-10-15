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
     * PostgreSQL stores identifiers truncated to 63 bytes.
     */
    private const POSTGRES_IDENTIFIER_LIMIT = 63;

    /**
     * Earl Kendrick Core already added document_key in
     * 2026_10_09_100000_add_document_key_to_libraries_report_files.
     * The column step is skipped there. The unique index is still aligned so each stream has its own version sequence.
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

        if (! $this->indexNames()->contains(self::STREAM_INDEX)) {
            $this->createStreamUniqueIndex();
        }

        $this->dropLegacyUniqueIndex();
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
            $this->dropStreamUniqueIndex();
        }

        if (Schema::hasColumn('libraries_report_files', 'document_key')) {
            Schema::table('libraries_report_files', function (Blueprint $table) {
                $table->dropColumn('document_key');
            });
        }

        if ($this->legacyIndexName() === null) {
            Schema::table('libraries_report_files', function (Blueprint $table) {
                $table->unique(['reportable_type', 'reportable_id', 'version']);
            });
        }
    }

    /**
     * One version sequence per parent and stream.
     * COALESCE makes a null document key a single survey stream on every driver.
     * A plain unique index treats each NULL as distinct on MySQL and SQLite.
     */
    private function createStreamUniqueIndex(): void
    {
        DB::statement(sprintf(
            "CREATE UNIQUE INDEX %s ON libraries_report_files (reportable_type, reportable_id, (COALESCE(document_key, '')), version)",
            self::STREAM_INDEX,
        ));
    }

    private function dropStreamUniqueIndex(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS '.self::STREAM_INDEX);

            return;
        }

        Schema::table('libraries_report_files', function (Blueprint $table) {
            $table->dropUnique(self::STREAM_INDEX);
        });
    }

    private function dropLegacyUniqueIndex(): void
    {
        $name = $this->legacyIndexName();

        if ($name === null) {
            return;
        }

        Schema::table('libraries_report_files', function (Blueprint $table) use ($name) {
            $table->dropUnique($name);
        });
    }

    /**
     * The legacy unique name is 67 characters. PostgreSQL stores the truncated form,
     * so an exact match on the Laravel name misses the constraint and leaves it in place.
     */
    private function legacyIndexName(): ?string
    {
        $names = $this->indexNames();

        foreach ([self::LEGACY_INDEX, substr(self::LEGACY_INDEX, 0, self::POSTGRES_IDENTIFIER_LIMIT)] as $candidate) {
            if ($names->contains($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function indexNames(): \Illuminate\Support\Collection
    {
        return collect(Schema::getIndexes('libraries_report_files'))->pluck('name');
    }
};

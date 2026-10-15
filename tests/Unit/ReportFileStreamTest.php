<?php

use Gigabyte\ReportPdf\Concerns\HasReportFiles;
use Gigabyte\ReportPdf\Models\ReportFile;
use Gigabyte\ReportPdf\Services\ReportFileGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    if (! Schema::hasTable('surveys')) {
        Schema::create('surveys', function (Blueprint $table) {
            $table->uuid('id')->primary();
        });
    }
});

it('keeps one survey version sequence and a separate sequence per document key', function () {
    $survey = reportStreamSurvey();

    reportStreamFile($survey, null, 1);

    expect(fn () => reportStreamFile($survey, null, 1))->toThrow(QueryException::class);

    reportStreamFile($survey, 'letter', 1);
    reportStreamFile($survey, null, 2);

    expect(Schema::hasColumn('libraries_report_files', 'document_stream_key'))->toBeFalse();
});

it('drops a postgres-truncated legacy unique index', function () {
    $truncated = substr(
        'libraries_report_files_reportable_type_reportable_id_version_unique',
        0,
        63,
    );

    Schema::table('libraries_report_files', function (Blueprint $table) use ($truncated) {
        $table->unique(['reportable_type', 'reportable_id', 'version'], $truncated);
    });

    reportStreamDocumentKeyMigration()->up();

    expect(collect(Schema::getIndexes('libraries_report_files'))->pluck('name'))
        ->not->toContain($truncated);

    $survey = reportStreamSurvey();
    reportStreamFile($survey, null, 1);
    reportStreamFile($survey, 'letter', 1);
});

it('restores the legacy parent version constraint when rolled back', function () {
    $migration = reportStreamDocumentKeyMigration();

    try {
        $migration->down();

        expect(Schema::hasColumn('libraries_report_files', 'document_key'))->toBeFalse();

        $survey = reportStreamSurvey();
        $attributes = [
            'reportable_type' => $survey->getMorphClass(),
            'reportable_id' => $survey->getKey(),
            'version' => 1,
            'file_path' => 'reports/file.pdf',
            'generated_at' => now(),
        ];

        ReportFile::query()->create($attributes);

        expect(fn () => ReportFile::query()->create($attributes))->toThrow(QueryException::class);
    } finally {
        if (! Schema::hasColumn('libraries_report_files', 'document_key')) {
            $migration->up();
        }
    }
});

it('returns the survey pdf when a named document has a higher version', function () {
    $survey = reportStreamSurvey();
    reportStreamFile($survey, null, 2, 'reports/survey-v2.pdf');

    (new ReportFileGenerator)->storeDocument($survey, 'reports/letter-v9.pdf', 'letter', 9);

    $latest = $survey->latestReportFile;

    expect($latest)->not->toBeNull()
        ->and($latest->version)->toBe(2)
        ->and($latest->document_key)->toBeNull()
        ->and($latest->file_path)->toBe('reports/survey-v2.pdf')
        ->and($survey->reportFiles)->toHaveCount(1)
        ->and($survey->reportFiles->first()->version)->toBe(2);
});

function reportStreamSurvey(): Model
{
    $survey = new ReportStreamSurvey;
    $survey->id = (string) Str::uuid();
    $survey->save();

    return $survey;
}

function reportStreamFile(Model $survey, ?string $documentKey, int $version, string $path = 'reports/file.pdf'): ReportFile
{
    return ReportFile::query()->create([
        'reportable_type' => $survey->getMorphClass(),
        'reportable_id' => $survey->getKey(),
        'document_key' => $documentKey,
        'version' => $version,
        'file_path' => $path,
        'generated_at' => now(),
    ]);
}

function reportStreamDocumentKeyMigration(): object
{
    return require dirname(__DIR__, 2).'/database/migrations/2026_10_15_100000_add_document_key_to_libraries_report_files.php';
}

class ReportStreamSurvey extends Model
{
    use HasReportFiles;

    protected $table = 'surveys';

    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}

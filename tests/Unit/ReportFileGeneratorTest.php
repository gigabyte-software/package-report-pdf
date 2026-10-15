<?php

use Gigabyte\ReportPdf\Services\ReportFileGenerator;
use Illuminate\Database\Eloquent\Model;

it('rejects a blank document key', function () {
    $reportable = new class extends Model {};

    expect(fn () => (new ReportFileGenerator)->storeDocument($reportable, 'reports/a.pdf', ' ', 1))
        ->toThrow(InvalidArgumentException::class, 'A document key is required.');
});

it('rejects an empty document path', function () {
    $reportable = new class extends Model {};

    expect(fn () => (new ReportFileGenerator)->storeDocument($reportable, '  ', 'letter', 1))
        ->toThrow(InvalidArgumentException::class, 'A document path is required.');
});

it('rejects a document version below 1', function () {
    $reportable = new class extends Model {};

    expect(fn () => (new ReportFileGenerator)->storeDocument($reportable, 'reports/a.pdf', 'letter', 0))
        ->toThrow(InvalidArgumentException::class, 'A document version starts at 1.');
});

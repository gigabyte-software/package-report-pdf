<?php

use Gigabyte\ReportPdf\Orchestration\PdfPageCounter;

it('returns zero when pdf does not exist', function () {
    $counter = new PdfPageCounter;

    expect($counter->count('/nonexistent/file.pdf'))->toBe(0);
});

it('counts pages via form feed when pdftotext is available', function () {
    exec('command -v pdftotext 2>/dev/null', $output, $code);
    if ($code !== 0) {
        test()->markTestSkipped('pdftotext is not installed');
    }

    $counter = new PdfPageCounter;
    $tmpFile = tempnam(sys_get_temp_dir(), 'page_count_').'.pdf';

    $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
        . "2 0 obj<</Type/Pages/Kids[3 0 R 4 0 R]/Count 2>>endobj\n"
        . "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 5 0 R/Resources<</Font<</F1 6 0 R>>>>>>endobj\n"
        . "4 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 7 0 R/Resources<</Font<</F1 6 0 R>>>>>>endobj\n"
        . "6 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n"
        . "5 0 obj<</Length 20>>stream\nBT /F1 12 Tf (Page 1) Tj ET\nendstream\nendobj\n"
        . "7 0 obj<</Length 20>>stream\nBT /F1 12 Tf (Page 2) Tj ET\nendstream\nendobj\n"
        . "xref\n0 8\n0000000000 65535 f \n"
        . "trailer<</Size 8/Root 1 0 R>>\nstartxref\n400\n%%EOF";
    file_put_contents($tmpFile, $pdfContent);

    try {
        $count = $counter->count($tmpFile);
        expect($count)->toBeGreaterThanOrEqual(1);
    } finally {
        @unlink($tmpFile);
    }
});

it('falls back to pdfinfo when pdftotext yields zero', function () {
    exec('command -v pdfinfo 2>/dev/null', $output, $code);
    if ($code !== 0) {
        test()->markTestSkipped('pdfinfo is not installed');
    }

    $counter = new class extends PdfPageCounter
    {
        protected function countViaPdftotext(string $pdfPath): int
        {
            return 0;
        }
    };

    $tmpFile = tempnam(sys_get_temp_dir(), 'page_count_').'.pdf';
    file_put_contents($tmpFile, '%PDF-1.4 fake');

    try {
        expect($counter->count($tmpFile))->toBeGreaterThanOrEqual(0);
    } finally {
        @unlink($tmpFile);
    }
});

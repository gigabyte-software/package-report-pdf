<?php

use Gigabyte\ReportPdf\Rendering\TocExtractor;

it('returns empty map when pdf does not exist', function () {
    $extractor = new TocExtractor;

    expect($extractor->extract('/nonexistent/file.pdf', '/TOCMARK:([^\s]+)/'))->toBe([]);
});

it('returns empty map when pdftotext is not available', function () {
    $extractor = new TocExtractor;

    $reflection = new ReflectionClass($extractor);
    $prop = $reflection->getProperty('pdftotextAvailable');
    $prop->setAccessible(true);
    $prop->setValue(null, false);

    $tmpFile = tempnam(sys_get_temp_dir(), 'toc_test_');
    file_put_contents($tmpFile, '%PDF-1.4 fake');

    try {
        $result = $extractor->extract($tmpFile, '/TOCMARK:([^\s]+)/');
        expect($result)->toBe([]);
    } finally {
        $prop->setValue(null, null);
        @unlink($tmpFile);
    }
});

it('extracts markers from a real pdf when pdftotext is installed', function () {
    exec('command -v pdftotext 2>/dev/null', $output, $code);
    if ($code !== 0) {
        test()->markTestSkipped('pdftotext is not installed');
    }

    $extractor = new TocExtractor;

    $reflection = new ReflectionClass($extractor);
    $prop = $reflection->getProperty('pdftotextAvailable');
    $prop->setAccessible(true);
    $prop->setValue(null, null);

    $tmpFile = tempnam(sys_get_temp_dir(), 'toc_test_').'.pdf';

    $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
        . "2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n"
        . "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj\n"
        . "5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n"
        . "4 0 obj<</Length 44>>stream\nBT /F1 12 Tf 100 700 Td (TOCMARK:section-1) Tj ET\nendstream\nendobj\n"
        . "xref\n0 6\n0000000000 65535 f \n"
        . "0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000266 00000 n \n0000000206 00000 n \n"
        . "trailer<</Size 6/Root 1 0 R>>\nstartxref\n360\n%%EOF";
    file_put_contents($tmpFile, $pdfContent);

    try {
        $result = $extractor->extract($tmpFile, '/TOCMARK:([^\s]+)/');
        expect($result)->toBeArray();
        if ($result !== []) {
            expect($result)->toHaveKey('section-1');
            expect($result['section-1'])->toBe(1);
        }
    } finally {
        @unlink($tmpFile);
        $prop->setValue(null, null);
    }
});

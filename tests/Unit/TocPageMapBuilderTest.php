<?php

use Gigabyte\ReportPdf\Orchestration\TocPageMapBuilder;

it('merges section maps with page offsets', function () {
    $builder = new TocPageMapBuilder;

    $merged = $builder->merge([
        ['map' => ['section-a' => 1, 'section-b' => 3], 'pageOffset' => 0],
        ['map' => ['section-c' => 1], 'pageOffset' => 10],
    ]);

    expect($merged)->toBe([
        'section-a' => 1,
        'section-b' => 3,
        'section-c' => 11,
    ]);
});

it('keeps the first occurrence when duplicate marker ids appear', function () {
    $builder = new TocPageMapBuilder;

    $merged = $builder->merge([
        ['map' => ['dup' => 2], 'pageOffset' => 0],
        ['map' => ['dup' => 1], 'pageOffset' => 5],
    ]);

    expect($merged)->toBe(['dup' => 2]);
});

it('returns an empty map when no sections are provided', function () {
    $builder = new TocPageMapBuilder;

    expect($builder->merge([]))->toBe([]);
});

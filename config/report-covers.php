<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Earl Kendrick logo colour spectrum (from ek-logo.svg)
    |--------------------------------------------------------------------------
    */
    'spectrum' => [
        '#e3bf28',
        '#ca8b0b',
        '#e36139',
        '#952531',
        '#dc4e65',
        '#89205b',
        '#961918',
        '#52121c',
        '#ad8abf',
        '#371e4c',
        '#a8c813',
        '#008259',
        '#009fe3',
        '#005da9',
    ],

    'footer' => [
        'tagline' => 'National Surveying Services',
        'url' => 'earlkendrick.com',
    ],

    'pdf_document_footer' => [
        'url' => 'www.earlkendrick.com',
        'report_label' => 'Planned Maintenance Report',
    ],

    /*
    |--------------------------------------------------------------------------
    | Per-module cover themes
    |--------------------------------------------------------------------------
    |
    | Each report module selects a theme key via ReportCoverPresenter. Colours
    | can diverge per department later. FC uses the main dark blue from
    | ek-logo-default.svg; PMP keeps its original cover palette.
    |
    */
    'themes' => [
        'fc' => [
            'label' => 'Fire Compartmentation',
            'primary' => '#1b1464',
            'dark' => '#1b1464',
            'accent' => '#00a7e0',
            'logo' => 'colour',
        ],
        'pmp' => [
            'label' => 'Planned Maintenance Programmes',
            'primary' => '#005da9',
            'dark' => '#1b1464',
            'accent' => '#009fe3',
            'logo' => 'colour',
        ],
    ],
];

<?php

return [
    'morph_map' => [],

    /*
    |--------------------------------------------------------------------------
    | Package asset path
    |--------------------------------------------------------------------------
    |
    | Directory containing bundled logos (ek-logo.svg, rics-logo.png, etc.).
    | Falls back to public_path('images/...') when a file is not found here.
    |
    */
    'assets_path' => dirname(__DIR__).'/resources/assets',

    /*
    |--------------------------------------------------------------------------
    | Browsershot / Puppeteer timeouts (seconds)
    |--------------------------------------------------------------------------
    |
    | Heavy reports (many photos, large HTML) can take a long time to render
    | to PDF. Two separate timeouts must both be large enough:
    |
    |   - timeout:          the Symfony process wrapper timeout. If this fires
    |                       first it kills the whole Chrome/Node process.
    |   - protocol_timeout: Puppeteer's CDP timeout for a single command such
    |                       as Page.printToPDF. Its default (~30s) is what
    |                       raised the "Page.printToPDF timed out" error, so we
    |                       give it plenty of head-room here.
    |
    | protocol_timeout should stay <= timeout so the CDP command can fail with
    | a clear error before the process wrapper hard-kills Chrome.
    |
    */
    'browsershot' => [
        'timeout' => (int) env('BROWSERSHOT_PDF_TIMEOUT', 300),
        'protocol_timeout' => (int) env('BROWSERSHOT_PDF_PROTOCOL_TIMEOUT', 240),
    ],
];

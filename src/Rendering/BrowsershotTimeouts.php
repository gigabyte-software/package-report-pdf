<?php

namespace Gigabyte\ReportPdf\Rendering;

/**
 * Applies the configured process and CDP protocol timeouts to a Browsershot
 * instance so heavy report renders do not hit Puppeteer's low default
 * protocolTimeout (~30s) and fail with "Page.printToPDF timed out".
 */
class BrowsershotTimeouts
{
    public static function apply(object $browsershot): void
    {
        $timeout = (int) config('report-pdf.browsershot.timeout', 300);
        $protocolTimeout = (int) config('report-pdf.browsershot.protocol_timeout', 240);

        // Symfony process wrapper timeout (seconds).
        $browsershot->timeout($timeout);

        // Puppeteer CDP timeout for a single command, e.g. Page.printToPDF (ms).
        $browsershot->setOption('protocolTimeout', $protocolTimeout * 1000);
    }
}

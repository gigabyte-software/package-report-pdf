<?php

namespace Gigabyte\ReportPdf\Rendering;

use Illuminate\Support\Facades\Log;

class TocExtractor
{
    private static ?bool $pdftotextAvailable = null;

    /**
     * @return array<string, int> Map of marker ID => page number
     */
    public function extract(string $pdfPath, string $markerRegex): array
    {
        if (! file_exists($pdfPath)) {
            return [];
        }

        if (! $this->pdftotextAvailable()) {
            Log::warning('TOC extraction skipped: pdftotext is not installed (install poppler-utils)');

            return [];
        }

        $lines = [];
        exec('pdftotext '.escapeshellarg($pdfPath).' - 2>/dev/null', $lines, $code);

        if ($code !== 0) {
            Log::warning('TOC extraction: pdftotext failed', ['code' => $code]);

            return [];
        }

        $text = implode("\n", $lines);
        $pages = explode("\f", $text);
        $map = [];

        foreach ($pages as $pageIndex => $pageText) {
            $pageNumber = $pageIndex + 1;
            if (preg_match_all($markerRegex, $pageText, $matches)) {
                foreach ($matches[1] as $id) {
                    if (! isset($map[$id])) {
                        $map[$id] = $pageNumber;
                    }
                }
            }
        }

        return $map;
    }

    private function pdftotextAvailable(): bool
    {
        if (self::$pdftotextAvailable !== null) {
            return self::$pdftotextAvailable;
        }

        exec('command -v pdftotext 2>/dev/null', $output, $code);
        self::$pdftotextAvailable = $code === 0;

        return self::$pdftotextAvailable;
    }
}

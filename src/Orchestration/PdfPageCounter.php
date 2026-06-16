<?php

namespace Gigabyte\ReportPdf\Orchestration;

use Illuminate\Support\Facades\Log;

class PdfPageCounter
{
    public function count(string $pdfPath): int
    {
        if (! file_exists($pdfPath)) {
            return 0;
        }

        $viaFormFeed = $this->countViaPdftotext($pdfPath);
        if ($viaFormFeed > 0) {
            return $viaFormFeed;
        }

        return $this->countViaPdfinfo($pdfPath);
    }

    private function countViaPdftotext(string $pdfPath): int
    {
        exec('command -v pdftotext 2>/dev/null', $output, $available);
        if ($available !== 0) {
            return 0;
        }

        $lines = [];
        exec('pdftotext '.escapeshellarg($pdfPath).' - 2>/dev/null', $lines, $code);
        if ($code !== 0) {
            return 0;
        }

        $text = implode("\n", $lines);
        if ($text === '') {
            return 0;
        }

        return count(explode("\f", $text));
    }

    private function countViaPdfinfo(string $pdfPath): int
    {
        exec('command -v pdfinfo 2>/dev/null', $output, $available);
        if ($available !== 0) {
            Log::warning('PDF page count skipped: neither pdftotext nor pdfinfo produced a count');

            return 0;
        }

        $lines = [];
        exec('pdfinfo '.escapeshellarg($pdfPath).' 2>/dev/null', $lines, $code);
        if ($code !== 0) {
            return 0;
        }

        foreach ($lines as $line) {
            if (preg_match('/^Pages:\s+(\d+)/', $line, $matches)) {
                return (int) $matches[1];
            }
        }

        return 0;
    }
}

<?php

namespace Gigabyte\ReportPdf\Rendering;

use Illuminate\Support\Facades\Log;

class PdfMerger
{
    /**
     * Merge multiple PDF files into a single output file using Ghostscript.
     *
     * @param  array<int, string>  $paths  Absolute paths in merge order
     */
    public function mergeMany(array $paths, string $outputPath): string
    {
        $dir = dirname($outputPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $existingPaths = array_values(array_filter($paths, fn (string $path) => file_exists($path)));

        if ($existingPaths === []) {
            touch($outputPath);

            return $outputPath;
        }

        if (count($existingPaths) === 1) {
            copy($existingPaths[0], $outputPath);

            return $outputPath;
        }

        $gsBase = 'gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite';
        $inputs = implode(' ', array_map('escapeshellarg', $existingPaths));
        $mergeCmd = "{$gsBase} -sOutputFile=".escapeshellarg($outputPath)." {$inputs} 2>&1";
        $mergeOutput = [];
        exec($mergeCmd, $mergeOutput, $mergeCode);

        if ($mergeCode !== 0) {
            Log::warning('PDF merge: Ghostscript mergeMany failed', [
                'code' => $mergeCode,
                'output' => implode("\n", $mergeOutput),
            ]);

            copy($existingPaths[0], $outputPath);
        }

        return $outputPath;
    }

    public function mergeCoverAndBody(string $coverPath, string $bodyPath, string $outputPath): string
    {
        $dir = dirname($outputPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $bodyTrimmedPath = $dir.'/body-trimmed-'.basename($bodyPath);

        if (! file_exists($coverPath) || ! file_exists($bodyPath)) {
            if (file_exists($bodyPath)) {
                rename($bodyPath, $outputPath);
            }
            @unlink($coverPath);

            return $outputPath;
        }

        $gsBase = 'gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite';

        $trimCmd = "{$gsBase} -dFirstPage=2 -sOutputFile=".escapeshellarg($bodyTrimmedPath).' '.escapeshellarg($bodyPath).' 2>&1';
        $trimOutput = [];
        exec($trimCmd, $trimOutput, $trimCode);
        if ($trimCode !== 0) {
            Log::warning('PDF merge: Ghostscript page extraction failed, using body as-is', [
                'code' => $trimCode,
                'output' => implode("\n", $trimOutput),
            ]);
            rename($bodyPath, $outputPath);
            @unlink($coverPath);

            return $outputPath;
        }

        $mergeOutput = [];
        $mergeCmd = "{$gsBase} -sOutputFile=".escapeshellarg($outputPath).' '.escapeshellarg($coverPath).' '.escapeshellarg($bodyTrimmedPath).' 2>&1';
        exec($mergeCmd, $mergeOutput, $mergeCode);
        if ($mergeCode !== 0) {
            Log::warning('PDF merge: Ghostscript merge failed, using body as-is', [
                'code' => $mergeCode,
                'output' => implode("\n", $mergeOutput),
            ]);
            rename($bodyPath, $outputPath);
        }

        @unlink($coverPath);
        @unlink($bodyPath);
        @unlink($bodyTrimmedPath);

        return $outputPath;
    }
}

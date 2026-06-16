<?php

namespace Gigabyte\ReportPdf\Rendering;

use Spatie\LaravelPdf\Facades\Pdf;

class BodyRenderer
{
    /**
     * @param  array{
     *     footerHtml?: string,
     *     headerHtml?: string,
     *     margins?: array{0: int, 1: int, 2: int, 3: int},
     *     windowSize?: array{0: int, 1: int},
     *     format?: string,
     * }  $config
     */
    public function render(string $view, array $data, string $outputPath, array $config = []): void
    {
        $footerHtml = $config['footerHtml'] ?? null;
        $headerHtml = $config['headerHtml'] ?? '<span></span>';
        $margins = $config['margins'] ?? [20, 15, 22, 15];
        $windowSize = $config['windowSize'] ?? [794, 1123];
        $format = $config['format'] ?? 'a4';

        $pdf = Pdf::view($view, $data)->format($format);

        if ($footerHtml !== null) {
            $pdf->withBrowsershot(function ($browsershot) use ($footerHtml, $headerHtml, $margins, $windowSize) {
                $browsershot
                    ->windowSize($windowSize[0], $windowSize[1])
                    ->showBrowserHeaderAndFooter()
                    ->headerHtml($headerHtml)
                    ->footerHtml($footerHtml)
                    ->margins($margins[0], $margins[1], $margins[2], $margins[3]);
            });
        } elseif (isset($config['windowSize'])) {
            $pdf->withBrowsershot(function ($browsershot) use ($windowSize) {
                $browsershot->windowSize($windowSize[0], $windowSize[1]);
            });
        }

        $pdf->save($outputPath);
    }
}

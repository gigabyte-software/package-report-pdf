<?php

namespace Gigabyte\ReportPdf\Rendering;

use Spatie\LaravelPdf\Facades\Pdf;

class CoverRenderer
{
    /**
     * @param  array{width?: int, height?: int}  $config
     */
    public function render(string $view, array $data, string $outputPath, array $config = []): void
    {
        $width = $config['width'] ?? 794;
        $height = $config['height'] ?? 1123;

        Pdf::view($view, $data)
            ->withBrowsershot(function ($browsershot) use ($width, $height) {
                $browsershot->windowSize($width, $height);
            })
            ->format('a4')
            ->save($outputPath);
    }
}

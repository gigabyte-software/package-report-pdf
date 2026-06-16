<?php

namespace Gigabyte\ReportPdf\Jobs;

use Gigabyte\ReportPdf\Rendering\PdfMerger;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Optional batch job stub for merging section PDFs into the report body.
 */
class StitchReportPdfJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<int, string>  $paths
     */
    public function __construct(
        public array $paths,
        public string $outputPath,
    ) {}

    public function handle(PdfMerger $pdfMerger): string
    {
        return $pdfMerger->mergeMany($this->paths, $this->outputPath);
    }
}

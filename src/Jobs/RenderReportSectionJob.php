<?php

namespace Gigabyte\ReportPdf\Jobs;

use Gigabyte\ReportPdf\Orchestration\ReportSection;
use Gigabyte\ReportPdf\Rendering\BodyRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Optional batch job stub for rendering a single report section PDF.
 */
class RenderReportSectionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $bodyConfig
     */
    public function __construct(
        public ReportSection $section,
        public array $bodyConfig,
        public string $outputPath,
    ) {}

    public function handle(BodyRenderer $bodyRenderer): void
    {
        $bodyRenderer->render(
            $this->section->view,
            $this->section->viewData,
            $this->outputPath,
            $this->bodyConfig,
        );
    }
}

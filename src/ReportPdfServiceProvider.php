<?php

namespace Gigabyte\ReportPdf;

use Gigabyte\ReportPdf\Orchestration\PdfPageCounter;
use Gigabyte\ReportPdf\Orchestration\SectionedReportGenerator;
use Gigabyte\ReportPdf\Orchestration\TocPageMapBuilder;
use Gigabyte\ReportPdf\Rendering\BodyRenderer;
use Gigabyte\ReportPdf\Rendering\CoverRenderer;
use Gigabyte\ReportPdf\Rendering\ImageStager;
use Gigabyte\ReportPdf\Rendering\PdfMerger;
use Gigabyte\ReportPdf\Rendering\TocExtractor;
use Gigabyte\ReportPdf\Services\ReportFileGenerator;
use Gigabyte\ReportPdf\Support\SignedDownloadHelper;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class ReportPdfServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/report-pdf.php', 'report-pdf');
        $this->mergeConfigFrom(__DIR__.'/../config/report-covers.php', 'report-pdf.report-covers');

        $this->app->bind(ImageStager::class);
        $this->app->singleton(CoverRenderer::class);
        $this->app->singleton(BodyRenderer::class);
        $this->app->singleton(PdfMerger::class);
        $this->app->singleton(TocExtractor::class);
        $this->app->singleton(PdfPageCounter::class);
        $this->app->singleton(TocPageMapBuilder::class);
        $this->app->singleton(SectionedReportGenerator::class);
        $this->app->singleton(ReportFileGenerator::class);
        $this->app->singleton(SignedDownloadHelper::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'report-pdf');

        $this->app->booted(function () {
            $map = config('report-pdf.morph_map', []);
            if ($map !== []) {
                Relation::morphMap($map);
            }
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/report-pdf.php' => config_path('report-pdf.php'),
                __DIR__.'/../config/report-covers.php' => config_path('report-covers.php'),
            ], 'report-pdf-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'report-pdf-migrations');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/report-pdf'),
            ], 'report-pdf-views');
        }
    }
}

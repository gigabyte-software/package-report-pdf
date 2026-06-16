<?php

namespace Gigabyte\ReportPdf\Orchestration;

use Gigabyte\ReportPdf\Contracts\EnrichesTocPageMap;
use Gigabyte\ReportPdf\Rendering\BodyRenderer;
use Gigabyte\ReportPdf\Rendering\CoverRenderer;
use Gigabyte\ReportPdf\Rendering\PdfMerger;
use Gigabyte\ReportPdf\Rendering\TocExtractor;
use Illuminate\Support\Facades\Concurrency;

class SectionedReportGenerator
{
    public function __construct(
        protected CoverRenderer $coverRenderer,
        protected BodyRenderer $bodyRenderer,
        protected PdfMerger $pdfMerger,
        protected TocExtractor $tocExtractor,
        protected PdfPageCounter $pageCounter,
        protected TocPageMapBuilder $tocPageMapBuilder,
    ) {}

    public function generate(SectionedReportPlan $plan): string
    {
        if (! is_dir($plan->workDir)) {
            mkdir($plan->workDir, 0775, true);
        }

        $coverPath = $plan->workDir.'/cover.pdf';
        $frontMatterPath = $plan->workDir.'/front-matter.pdf';
        $bodyPath = $plan->workDir.'/body.pdf';

        $this->coverRenderer->render(
            $plan->coverView,
            $plan->coverData,
            $coverPath,
        );

        $sections = collect($plan->sections)->sortBy(fn (ReportSection $section) => $section->sortOrder)->values();
        $sectionPaths = $this->renderSectionsInParallel($sections, $plan);

        $frontMatterData = $plan->frontMatterData;
        $this->bodyRenderer->render(
            $plan->frontMatterView,
            $frontMatterData,
            $frontMatterPath,
            $plan->bodyConfig,
        );

        $frontMatterPages = $this->pageCounter->count($frontMatterPath);

        $sectionMaps = [];
        $pageOffset = $frontMatterPages;

        foreach ($sections as $index => $section) {
            $path = $sectionPaths[$section->id] ?? null;
            if ($path === null || ! file_exists($path)) {
                continue;
            }

            $sectionMaps[] = [
                'map' => $this->tocExtractor->extract($path, $plan->tocMarkerRegex),
                'pageOffset' => $pageOffset,
            ];

            $pageOffset += $this->pageCounter->count($path);
        }

        $tocPageMap = $this->tocPageMapBuilder->merge($sectionMaps);
        $tocPageMap = $this->applyEnrichToc($plan, $tocPageMap);

        $frontMatterData['tocPageMap'] = $tocPageMap;
        $this->bodyRenderer->render(
            $plan->frontMatterView,
            $frontMatterData,
            $frontMatterPath,
            $plan->bodyConfig,
        );

        $mergePaths = [$frontMatterPath];
        foreach ($sections as $section) {
            if (isset($sectionPaths[$section->id])) {
                $mergePaths[] = $sectionPaths[$section->id];
            }
        }

        $this->pdfMerger->mergeMany($mergePaths, $bodyPath);
        $this->pdfMerger->mergeCoverAndBody($coverPath, $bodyPath, $plan->finalPath);

        $this->cleanupSectionPaths($sectionPaths);
        @unlink($frontMatterPath);

        return $plan->finalPath;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ReportSection>  $sections
     * @return array<string, string> section id => absolute pdf path
     */
    protected function renderSectionsInParallel($sections, SectionedReportPlan $plan): array
    {
        if ($sections->isEmpty()) {
            return [];
        }

        $tasks = [];
        $pathsById = [];

        foreach ($sections as $section) {
            $outputPath = $plan->workDir.'/section-'.$section->id.'.pdf';
            $pathsById[$section->id] = $outputPath;

            $tasks[] = fn () => $this->renderSection($section, $plan->bodyConfig, $outputPath);
        }

        Concurrency::driver('process')->run($tasks);

        return $pathsById;
    }

    /**
     * @param  array<string, mixed>  $bodyConfig
     */
    protected function renderSection(ReportSection $section, array $bodyConfig, string $outputPath): void
    {
        $this->bodyRenderer->render(
            $section->view,
            $section->viewData,
            $outputPath,
            $bodyConfig,
        );
    }

    /**
     * @param  array<string, int>  $tocPageMap
     * @return array<string, int>
     */
    protected function applyEnrichToc(SectionedReportPlan $plan, array $tocPageMap): array
    {
        $enricher = $plan->enrichToc;

        if ($enricher === null) {
            return $tocPageMap;
        }

        if ($enricher instanceof EnrichesTocPageMap) {
            return $enricher->enrich($tocPageMap);
        }

        if (is_callable($enricher)) {
            return $enricher($tocPageMap);
        }

        return $tocPageMap;
    }

    /**
     * @param  array<string, string>  $sectionPaths
     */
    protected function cleanupSectionPaths(array $sectionPaths): void
    {
        foreach ($sectionPaths as $path) {
            @unlink($path);
        }
    }
}

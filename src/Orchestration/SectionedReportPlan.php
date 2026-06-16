<?php

namespace Gigabyte\ReportPdf\Orchestration;

use Gigabyte\ReportPdf\Contracts\EnrichesTocPageMap;

final class SectionedReportPlan
{
    /**
     * @param  array<int, ReportSection>  $sections
     * @param  array<string, mixed>  $coverData
     * @param  array<string, mixed>  $frontMatterData
     * @param  array<string, mixed>  $bodyConfig
     * @param  callable(array<string, int>): array<string, int>|EnrichesTocPageMap|null  $enrichToc
     */
    public function __construct(
        public readonly string $coverView,
        public readonly array $coverData,
        public readonly array $sections,
        public readonly string $frontMatterView,
        public readonly array $frontMatterData,
        public readonly array $bodyConfig,
        public readonly string $workDir,
        public readonly string $finalPath,
        public readonly string $tocMarkerRegex = '/TOCMARK:([^\s]+)/',
        public readonly mixed $enrichToc = null,
    ) {}
}

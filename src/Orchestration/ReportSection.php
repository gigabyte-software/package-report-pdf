<?php

namespace Gigabyte\ReportPdf\Orchestration;

final class ReportSection
{
    /**
     * @param  array<string, mixed>  $viewData
     */
    public function __construct(
        public readonly string $id,
        public readonly string $view,
        public readonly array $viewData,
        public readonly int $sortOrder,
    ) {}
}

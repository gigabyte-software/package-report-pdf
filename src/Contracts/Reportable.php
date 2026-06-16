<?php

namespace Gigabyte\ReportPdf\Contracts;

use Gigabyte\ReportPdf\Enums\ReportFileStatus;

interface Reportable
{
    public function getReportFileStatusColumn(): string;

    public function getReportFileLatestVersionColumn(): string;

    public function getReportFileErrorMessageColumn(): string;

    public function getReportFileStatus(): ?ReportFileStatus;

    public function setReportFileStatus(ReportFileStatus $status): void;

    public function getKey();

    /**
     * Must match {@see \Illuminate\Database\Eloquent\Model::getMorphClass()} (no return type).
     */
    public function getMorphClass();
}

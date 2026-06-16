<?php

namespace Gigabyte\ReportPdf\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Gigabyte\ReportPdf\Contracts\Reportable;
use Gigabyte\ReportPdf\Enums\ReportFileStatus;
use Gigabyte\ReportPdf\Models\ReportFile;

trait HasReportFiles
{
    public function reportFiles(): MorphMany
    {
        return $this->morphMany(ReportFile::class, 'reportable')->orderBy('version');
    }

    public function latestReportFile(): MorphOne
    {
        return $this->morphOne(ReportFile::class, 'reportable')->orderByDesc('version');
    }

    public function getReportFileStatusColumn(): string
    {
        return 'report_file_status';
    }

    public function getReportFileLatestVersionColumn(): string
    {
        return 'report_file_latest_version';
    }

    public function getReportFileErrorMessageColumn(): string
    {
        return 'report_file_error_message';
    }

    public function getReportFileStatus(): ?ReportFileStatus
    {
        $value = $this->getAttribute($this->getReportFileStatusColumn());

        if ($value === null) {
            return null;
        }

        if ($value instanceof ReportFileStatus) {
            return $value;
        }

        return ReportFileStatus::tryFrom((string) $value);
    }

    public function setReportFileStatus(ReportFileStatus $status): void
    {
        $this->setAttribute($this->getReportFileStatusColumn(), $status->value);
    }

    public function getReportFileLatestVersion(): ?int
    {
        $value = $this->getAttribute($this->getReportFileLatestVersionColumn());

        return $value !== null ? (int) $value : null;
    }

    public function isReportFileInProgress(): bool
    {
        $status = $this->getReportFileStatus();

        return $status !== null && $status->isGenerating();
    }

    /**
     * @return $this&Reportable
     */
    protected function assertReportable(): Reportable
    {
        if (! $this instanceof Reportable) {
            throw new \LogicException(static::class.' must implement '.Reportable::class);
        }

        return $this;
    }
}

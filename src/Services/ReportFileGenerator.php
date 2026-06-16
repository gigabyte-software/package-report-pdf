<?php

namespace Gigabyte\ReportPdf\Services;

use Gigabyte\ReportPdf\Contracts\GeneratesReportFile;
use Gigabyte\ReportPdf\Contracts\Reportable;
use Gigabyte\ReportPdf\Enums\ReportFileStatus;
use Gigabyte\ReportPdf\Models\ReportFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportFileGenerator
{
    public function execute(Model $reportable, GeneratesReportFile $generator, ?int $generatedBy = null): void
    {
        if (! $reportable instanceof Reportable) {
            throw new \InvalidArgumentException('Reportable model must implement '.Reportable::class);
        }

        $claimed = $this->claimGeneration($reportable);

        if (! $claimed) {
            return;
        }

        try {
            DB::transaction(function () use ($reportable, $generator, $generatedBy) {
                $locked = $reportable->newQuery()->lockForUpdate()->findOrFail($reportable->getKey());

                if (! $locked instanceof Reportable) {
                    throw new \InvalidArgumentException('Locked model must implement '.Reportable::class);
                }

                $nextVersion = ($locked->getReportFileLatestVersion() ?? 0) + 1;
                $absolutePath = $generator->generate($locked, $nextVersion);
                $relativePath = $this->toRelativeStoragePath($absolutePath);

                $file = new ReportFile;
                $file->fill([
                    'reportable_type' => $locked->getMorphClass(),
                    'reportable_id' => $locked->getKey(),
                    'version' => $nextVersion,
                    'file_path' => $relativePath,
                    'generated_at' => now(),
                ]);
                $file->created_by = $generatedBy;
                $file->created_on = now();
                $file->save();

                $locked->setAttribute($locked->getReportFileLatestVersionColumn(), $nextVersion);
                $locked->setReportFileStatus(ReportFileStatus::Ready);
                $locked->save();
            });
        } catch (\Throwable $e) {
            Log::error('Report file generation failed', [
                'reportable_type' => $reportable->getMorphClass(),
                'reportable_id' => $reportable->getKey(),
                'error' => $e->getMessage(),
            ]);

            $reportable->setReportFileStatus(ReportFileStatus::Failed);
            $reportable->setAttribute($reportable->getReportFileErrorMessageColumn(), $e->getMessage());
            $reportable->save();

            throw $e;
        }
    }

    public function queue(Model $reportable, GeneratesReportFile $generator, callable $dispatchJob, ?int $generatedBy = null): void
    {
        if (! $reportable instanceof Reportable) {
            throw new \InvalidArgumentException('Reportable model must implement '.Reportable::class);
        }

        $statusColumn = $reportable->getReportFileStatusColumn();
        $errorColumn = $reportable->getReportFileErrorMessageColumn();

        $affected = $reportable->newQuery()
            ->where($reportable->getKeyName(), $reportable->getKey())
            ->where(function ($query) use ($statusColumn) {
                $query->whereNull($statusColumn)
                    ->orWhereNotIn($statusColumn, [
                        ReportFileStatus::Queued->value,
                        ReportFileStatus::Generating->value,
                    ]);
            })
            ->update([
                $statusColumn => ReportFileStatus::Queued->value,
                $errorColumn => null,
            ]);

        if ($affected === 0) {
            return;
        }

        $reportable->refresh();
        $dispatchJob();
    }

    /**
     * Atomically claim the "generating" slot using a DB-level compare-and-swap
     * to prevent concurrent workers from both entering generation.
     */
    protected function claimGeneration(Model&Reportable $reportable): bool
    {
        $statusColumn = $reportable->getReportFileStatusColumn();
        $errorColumn = $reportable->getReportFileErrorMessageColumn();

        $affected = $reportable->newQuery()
            ->where($reportable->getKeyName(), $reportable->getKey())
            ->where(function ($query) use ($statusColumn) {
                $query->whereNull($statusColumn)
                    ->orWhereNotIn($statusColumn, [ReportFileStatus::Generating->value]);
            })
            ->update([
                $statusColumn => ReportFileStatus::Generating->value,
                $errorColumn => null,
            ]);

        if ($affected === 0) {
            return false;
        }

        $reportable->refresh();

        return true;
    }

    protected function toRelativeStoragePath(string $absolutePath): string
    {
        $prefix = storage_path('app/');

        if (str_starts_with($absolutePath, $prefix)) {
            return substr($absolutePath, strlen($prefix));
        }

        return $absolutePath;
    }
}

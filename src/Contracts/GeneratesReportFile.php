<?php

namespace Gigabyte\ReportPdf\Contracts;

use Illuminate\Database\Eloquent\Model;

interface GeneratesReportFile
{
    /**
     * Generate the report file and return the absolute path on disk.
     */
    public function generate(Model $reportable, int $version): string;
}

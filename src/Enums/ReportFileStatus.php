<?php

namespace Gigabyte\ReportPdf\Enums;

enum ReportFileStatus: string
{
    case Queued = 'queued';
    case Generating = 'generating';
    case Ready = 'ready';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Queued => 'Queued',
            self::Generating => 'Generating',
            self::Ready => 'Ready',
            self::Failed => 'Failed',
        };
    }

    public function isInProgress(): bool
    {
        return $this === self::Queued || $this === self::Generating;
    }

    public function isGenerating(): bool
    {
        return $this === self::Generating;
    }
}

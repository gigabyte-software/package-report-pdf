<?php

namespace Gigabyte\ReportPdf\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ReportFile extends Model
{
    use HasUuids;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'libraries_report_files';

    protected $keyType = 'string';

    protected $fillable = [
        'reportable_type',
        'reportable_id',
        'version',
        'file_path',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'generated_at' => 'datetime',
            'created_on' => 'datetime',
            'updated_on' => 'datetime',
        ];
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }
}

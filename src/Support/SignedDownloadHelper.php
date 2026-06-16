<?php

namespace Gigabyte\ReportPdf\Support;

use Gigabyte\ReportPdf\Models\ReportFile;
use Illuminate\Support\Facades\URL;

class SignedDownloadHelper
{
    public function buildSignedUrl(ReportFile $file, string $routeName, int $ttlMinutes = 5): string
    {
        return URL::temporarySignedRoute(
            $routeName,
            now()->addMinutes($ttlMinutes),
            ['path' => $file->file_path],
        );
    }

    public function resolveFilePath(?string $relativePath, string $storageSubdir): string
    {
        if (! $relativePath) {
            abort(404);
        }

        $normalized = str_replace('\\', '/', $relativePath);
        $storageRoot = realpath(storage_path('app'));

        if ($storageRoot === false) {
            abort(404, 'Report file not found.');
        }

        if (str_starts_with($normalized, $storageSubdir.'/')) {
            $fullPath = storage_path('app/'.$normalized);
        } else {
            $fullPath = storage_path('app/'.$storageSubdir.'/'.basename($normalized));
        }

        $resolved = realpath($fullPath);
        $allowedDir = realpath($storageRoot.DIRECTORY_SEPARATOR.$storageSubdir);

        if ($resolved === false || $allowedDir === false || ! str_starts_with($resolved, $allowedDir.DIRECTORY_SEPARATOR)) {
            abort(404, 'Report file not found.');
        }

        return $resolved;
    }

    public function buildFilename(ReportFile $file, string $prefix, ?string $suffix = null): string
    {
        $version = $file->version;
        $generatedAt = $file->generated_at;
        $datePart = $generatedAt
            ? $generatedAt->format('Y-m-d')
            : date('Y-m-d');

        $segments = [$prefix];
        if ($version !== null) {
            $segments[] = 'v'.$version;
        }
        if ($suffix !== null && $suffix !== '') {
            $segments[] = $suffix;
        }
        $segments[] = $datePart;

        return implode('-', $segments).'.pdf';
    }
}

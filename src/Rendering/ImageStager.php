<?php

namespace Gigabyte\ReportPdf\Rendering;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Stage cloud storage files locally for Browsershot PDF rendering.
 */
class ImageStager
{
    private const IMAGES_DIR = 'images';

    private ?string $stagingDir = null;

    /** @var array<string, string> storage path => absolute local path */
    private array $staged = [];

    public function begin(): void
    {
        $this->stagingDir = storage_path('app/pdf-staging/'.uniqid('pdf_', true));
        $this->staged = [];
    }

    public function resolveUrl(string $path): ?string
    {
        $localPath = $this->localPath($path);

        if ($localPath === null) {
            return null;
        }

        if ($this->isLocalDisk()) {
            return $localPath;
        }

        return self::IMAGES_DIR.'/'.basename($localPath);
    }

    public function localPath(string $path): ?string
    {
        if (isset($this->staged[$path])) {
            return $this->staged[$path];
        }

        if ($this->isLocalDisk()) {
            $absolute = Storage::path($path);
            if (is_readable($absolute)) {
                $this->staged[$path] = $absolute;

                return $absolute;
            }
        }

        if (! Storage::exists($path)) {
            return null;
        }

        $this->ensureStagingDir();
        $this->ensureImagesDir();

        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';
        $localPath = $this->stagingDir.'/'.self::IMAGES_DIR.'/'.hash('sha256', $path).'.'.$extension;

        if (! is_file($localPath)) {
            $stream = Storage::readStream($path);
            if ($stream === null) {
                return null;
            }

            $dest = fopen($localPath, 'w');
            if ($dest === false) {
                if (is_resource($stream)) {
                    fclose($stream);
                }

                return null;
            }

            stream_copy_to_stream($stream, $dest);
            fclose($stream);
            fclose($dest);
        }

        $this->staged[$path] = $localPath;

        return $localPath;
    }

    public function needsHtmlFromFilePath(): bool
    {
        return $this->stagingDir !== null
            && is_dir($this->stagingDir.'/'.self::IMAGES_DIR);
    }

    public function writeHtml(string $html): string
    {
        $this->ensureStagingDir();

        $path = $this->stagingDir.'/index.html';
        file_put_contents($path, $html);

        return $path;
    }

    public function cleanup(): void
    {
        if ($this->stagingDir !== null && is_dir($this->stagingDir)) {
            File::deleteDirectory($this->stagingDir);
        }

        $this->stagingDir = null;
        $this->staged = [];
    }

    private function ensureStagingDir(): void
    {
        if ($this->stagingDir === null) {
            $this->begin();
        }

        if (! is_dir($this->stagingDir)) {
            mkdir($this->stagingDir, 0755, true);
        }
    }

    private function ensureImagesDir(): void
    {
        $imagesDir = $this->stagingDir.'/'.self::IMAGES_DIR;
        if (! is_dir($imagesDir)) {
            mkdir($imagesDir, 0755, true);
        }
    }

    private function isLocalDisk(): bool
    {
        return config('filesystems.disks.'.config('filesystems.default').'.driver') === 'local';
    }
}

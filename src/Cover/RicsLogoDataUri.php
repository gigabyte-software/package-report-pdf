<?php

namespace Gigabyte\ReportPdf\Cover;

final class RicsLogoDataUri
{
    private static ?string $regulated = null;

    public static function regulated(): string
    {
        return self::$regulated ??= self::encode('rics-logo.png', 'image/png');
    }

    private static function encode(string $filename, string $mime): string
    {
        $path = self::resolveAssetPath($filename);
        $contents = file_get_contents($path);

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }

    private static function resolveAssetPath(string $filename): string
    {
        $assetsPath = rtrim((string) config('report-pdf.assets_path'), '/');
        $packagePath = $assetsPath.'/'.$filename;

        if (is_readable($packagePath)) {
            return $packagePath;
        }

        return public_path('images/'.$filename);
    }
}

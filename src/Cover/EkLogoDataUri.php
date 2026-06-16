<?php

namespace Gigabyte\ReportPdf\Cover;

final class EkLogoDataUri
{
    private static ?string $colour = null;

    private static ?string $white = null;

    private static ?string $default = null;

    public static function colour(): string
    {
        return self::$colour ??= self::encode('ek-logo.svg');
    }

    public static function white(): string
    {
        return self::$white ??= self::encode('ek-logo-white.svg');
    }

    /** Square Earl Kendrick brand mark for client-facing report covers. */
    public static function default(): string
    {
        return self::$default ??= self::encode('ek-logo-default.svg');
    }

    private static function encode(string $filename): string
    {
        $path = self::resolveAssetPath($filename);
        $contents = file_get_contents($path);

        return 'data:image/svg+xml;base64,'.base64_encode($contents);
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

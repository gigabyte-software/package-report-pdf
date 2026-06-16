<?php

namespace Gigabyte\ReportPdf\Cover;

final class ReportCoverData
{
    /**
     * @param  array<int, array{label?: string, value: string}>  $metaLines
     * @param  array{full_name: string, qualifications?: string|null, role?: string|null, email?: string|null, phone?: string|null}|null  $author
     */
    public function __construct(
        public readonly string $themeKey,
        public readonly string $title,
        public readonly string $subtitle,
        public readonly array $metaLines,
        public readonly ?string $clientLogoDataUri = null,
        public readonly ?string $heroPhotoDataUri = null,
        public readonly ?array $author = null,
        public readonly ?string $version = null,
        public readonly bool $includeEkBranding = true,
        public readonly ?string $ekLogoDataUri = null,
    ) {}

    /**
     * @param  array<int, array{label?: string, value: string}>  $metaLines
     * @param  array{full_name: string, qualifications?: string|null, role?: string|null, email?: string|null, phone?: string|null}|null  $author
     */
    public static function make(
        string $themeKey,
        string $title,
        string $subtitle,
        array $metaLines,
        ?string $clientLogoDataUri = null,
        ?string $heroPhotoDataUri = null,
        ?array $author = null,
        ?string $version = null,
        bool $includeEkBranding = true,
        ?string $ekLogoDataUri = null,
    ): self {
        return new self(
            themeKey: $themeKey,
            title: $title,
            subtitle: $subtitle,
            metaLines: $metaLines,
            clientLogoDataUri: $clientLogoDataUri,
            heroPhotoDataUri: $heroPhotoDataUri,
            author: $author,
            version: $version,
            includeEkBranding: $includeEkBranding,
            ekLogoDataUri: $ekLogoDataUri,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $theme = config(
            'report-pdf.report-covers.themes.'.$this->themeKey,
            config('report-pdf.report-covers.themes.pmp'),
        );

        return [
            'themeKey' => $this->themeKey,
            'themeLabel' => $theme['label'],
            'themePrimary' => $theme['primary'],
            'themeDark' => $theme['dark'],
            'themeAccent' => $theme['accent'],
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'metaLines' => $this->metaLines,
            'clientLogoDataUri' => $this->clientLogoDataUri,
            'heroPhotoDataUri' => $this->heroPhotoDataUri,
            'author' => $this->author,
            'version' => $this->version,
            'includeEkBranding' => $this->includeEkBranding,
            'ekLogoDataUri' => $this->ekLogoDataUri ?? EkLogoDataUri::default(),
            'ricsRegulatedLogoDataUri' => RicsLogoDataUri::regulated(),
            'footerTagline' => config('report-pdf.report-covers.footer.tagline'),
            'footerUrl' => config('report-pdf.report-covers.footer.url'),
            'spectrum' => config('report-pdf.report-covers.spectrum'),
        ];
    }
}

# gigabyte/laravel-report-pdf

Sectioned PDF report generation for Laravel: cover sheets, TOC extraction, Ghostscript merging, and parallel section rendering.

## Installation

```bash
composer require gigabyte/laravel-report-pdf
```

## Features

- Cover sheet rendering with bundled Earl Kendrick / RICS assets
- Sectioned report orchestration via `SectionedReportGenerator`
- TOC marker extraction (`TocExtractor`) and page map building (`TocPageMapBuilder`)
- Ghostscript PDF merging (`PdfMerger::mergeMany`, `mergeCoverAndBody`)
- Report file versioning (`ReportFileGenerator`, `libraries_report_files` table)
- Named document streams via `ReportFileGenerator::storeDocument()`. A null `document_key` remains the single survey PDF from `execute()`

## Configuration

Publish config and views:

```bash
php artisan vendor:publish --tag=report-pdf-config
php artisan vendor:publish --tag=report-pdf-views
```

## Testing

```bash
composer install
./vendor/bin/pest
```

<?php

namespace Gigabyte\ReportPdf\Tests;

use Gigabyte\ReportPdf\ReportPdfServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ReportPdfServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('report-pdf.assets_path', dirname(__DIR__).'/resources/assets');
    }
}

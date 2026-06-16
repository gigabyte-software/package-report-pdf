<?php

namespace Gigabyte\ReportPdf\Contracts;

interface EnrichesTocPageMap
{
    /**
     * @param  array<string, int>  $tocPageMap
     * @return array<string, int>
     */
    public function enrich(array $tocPageMap): array;
}

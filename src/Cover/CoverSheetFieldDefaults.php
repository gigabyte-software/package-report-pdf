<?php

namespace Gigabyte\ReportPdf\Cover;

final class CoverSheetFieldDefaults
{
    public function __construct(
        public readonly ?string $propertyKnownAs = null,
        public readonly ?string $preparedFor = null,
        public readonly ?string $onBehalfOf = null,
        public readonly ?string $propertyManager = null,
        public readonly ?string $jobReference = null,
    ) {}
}

<?php

namespace Gigabyte\ReportPdf\Cover;

use Carbon\CarbonInterface;

final class CoverPartyMetaLines
{
    /**
     * @return array<int, array{label?: string, value: string}>
     */
    public static function build(
        ?string $coverPropertyKnownAs,
        ?string $coverPreparedFor,
        ?string $coverOnBehalfOf,
        ?string $coverPropertyManager,
        ?string $coverJobReference,
        CoverSheetFieldDefaults $defaults,
        ?CarbonInterface $reportDate,
    ): array {
        $propertyKnownAs = self::value($coverPropertyKnownAs, $defaults->propertyKnownAs);
        $preparedFor = self::value($coverPreparedFor, $defaults->preparedFor);
        $onBehalfOf = self::value($coverOnBehalfOf, $defaults->onBehalfOf);
        $propertyManager = self::value($coverPropertyManager, $defaults->propertyManager);
        $jobReference = self::value($coverJobReference, $defaults->jobReference);

        $lines = [];

        if ($propertyKnownAs !== null) {
            $lines[] = ['label' => 'For the Property Known As', 'value' => $propertyKnownAs];
        }

        if ($preparedFor !== null) {
            $lines[] = ['label' => 'For', 'value' => $preparedFor];
        }

        if ($onBehalfOf !== null) {
            $lines[] = ['label' => 'On Behalf of', 'value' => $onBehalfOf];
        }

        if ($propertyManager !== null) {
            $lines[] = ['label' => 'Property Manager', 'value' => $propertyManager];
        }

        $lines[] = [
            'label' => 'Date',
            'value' => $reportDate
                ? $reportDate->format('j F Y')
                : now()->format('j F Y'),
        ];

        if ($jobReference !== null) {
            $lines[] = ['label' => 'Reference', 'value' => $jobReference];
        }

        return $lines;
    }

    private static function value(?string $stored, ?string $default): ?string
    {
        $value = trim($stored ?? '');

        if ($value !== '') {
            return $value;
        }

        $fallback = trim($default ?? '');

        return $fallback !== '' ? $fallback : null;
    }
}

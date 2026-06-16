<?php

namespace Gigabyte\ReportPdf\Orchestration;

class TocPageMapBuilder
{
    /**
     * Merge per-section TOC maps into a single map with absolute page numbers.
     *
     * @param  array<int, array{map: array<string, int>, pageOffset: int}>  $sectionMaps
     * @return array<string, int>
     */
    public function merge(array $sectionMaps): array
    {
        $merged = [];

        foreach ($sectionMaps as $entry) {
            $offset = $entry['pageOffset'];

            foreach ($entry['map'] as $id => $page) {
                if (! isset($merged[$id])) {
                    $merged[$id] = $offset + $page;
                }
            }
        }

        return $merged;
    }
}

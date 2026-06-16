<?php

namespace Gigabyte\ReportPdf\Support;

class DocumentFooterBuilder
{
    /**
     * Build the self-contained HTML used by Browsershot's displayHeaderFooter
     * for the page footer. Chrome auto-populates elements with the special
     * classes "pageNumber" and "totalPages".
     */
    public function build(string $url, string $addressLine, string $reportLabel): string
    {
        $url = e($url);
        $addressLine = e($addressLine);
        $reportLabel = e($reportLabel);

        return <<<HTML
        <div style="width:100%;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:7pt;color:#333;padding:0 15mm;line-height:1.4;">
            <div style="border-top:0.4pt solid #333;margin-bottom:1.5mm;"></div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;">
                <div style="text-align:left;">{$url}</div>
                <div style="text-align:center;">
                    <div>{$addressLine}</div>
                    <div>{$reportLabel}</div>
                </div>
                <div style="text-align:right;white-space:nowrap;"><span class="pageNumber"></span>/<span class="totalPages"></span></div>
            </div>
        </div>
        HTML;
    }
}

<?php

use Gigabyte\ReportPdf\Rendering\BrowsershotTimeouts;

/**
 * Records the timeout/setOption calls a Browsershot instance would receive.
 */
function fakeBrowsershot(): object
{
    return new class
    {
        /** @var array<string, mixed> */
        public array $calls = [];

        public function timeout(int $seconds): static
        {
            $this->calls['timeout'] = $seconds;

            return $this;
        }

        public function setOption(string $key, $value): static
        {
            $this->calls[$key] = $value;

            return $this;
        }
    };
}

it('applies configured process and protocol timeouts', function () {
    config()->set('report-pdf.browsershot.timeout', 300);
    config()->set('report-pdf.browsershot.protocol_timeout', 240);

    $browsershot = fakeBrowsershot();

    BrowsershotTimeouts::apply($browsershot);

    expect($browsershot->calls['timeout'])->toBe(300)
        ->and($browsershot->calls['protocolTimeout'])->toBe(240_000);
});

it('converts the protocol timeout from seconds to milliseconds', function () {
    config()->set('report-pdf.browsershot.protocol_timeout', 120);

    $browsershot = fakeBrowsershot();

    BrowsershotTimeouts::apply($browsershot);

    expect($browsershot->calls['protocolTimeout'])->toBe(120_000);
});

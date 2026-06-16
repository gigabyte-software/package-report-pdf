{{-- Shared A4 cover styles. Expects CSS variables: --theme-primary, --theme-dark, --theme-accent --}}
<style>
    .report-cover {
        position: relative;
        width: 100vw;
        height: 100vh;
        min-width: 100vw;
        min-height: 100vh;
        overflow: hidden;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        page-break-after: always;
        box-sizing: border-box;
        background: #eef0f4;
    }

    .report-cover *,
    .report-cover *::before,
    .report-cover *::after {
        box-sizing: border-box;
    }

    .report-cover__content {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: 22mm 20mm 18mm 20mm;
    }

    .report-cover__logo {
        width: 38mm;
        height: auto;
        margin-bottom: 12mm;
    }

    .report-cover__client-logo {
        max-width: 60mm;
        max-height: 22mm;
        object-fit: contain;
        margin-bottom: 8mm;
    }

    .report-cover__body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .report-cover__title {
        font-size: 26pt;
        font-weight: 700;
        line-height: 1.15;
        color: var(--theme-dark);
        margin: 0 0 5mm;
        max-width: 130mm;
    }

    .report-cover__subtitle {
        font-size: 15pt;
        font-weight: 400;
        line-height: 1.35;
        color: #4a5568;
        margin: 0 0 12mm;
        max-width: 120mm;
    }

    .report-cover__meta {
        font-size: 9.5pt;
        line-height: 1.35;
        color: #4a5568;
    }

    .report-cover__meta-line {
        margin-bottom: 3mm;
    }

    .report-cover__meta-line:last-child {
        margin-bottom: 0;
    }

    .report-cover__meta-label {
        display: block;
        font-size: 8.5pt;
        font-weight: 600;
        letter-spacing: 0.02em;
        color: #1a202c;
        margin-bottom: 0.5mm;
    }

    .report-cover__meta-value {
        display: block;
        color: #2d3748;
    }

    .report-cover__author {
        margin-top: 10mm;
        font-size: 9.5pt;
        line-height: 1.6;
        color: #4a5568;
    }

    .report-cover__author-name {
        font-size: 10.5pt;
        font-weight: 600;
        color: #2d3748;
    }

    .report-cover__version {
        margin-top: 8mm;
        font-size: 9pt;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--theme-primary);
    }

    .report-cover__footer-scrim {
        position: absolute;
        z-index: 3;
        left: 0;
        right: 0;
        bottom: 0;
        height: 38mm;
        background: linear-gradient(to top, rgba(26, 32, 44, 0.88) 0%, rgba(26, 32, 44, 0.42) 55%, transparent 100%);
        pointer-events: none;
    }

    .report-cover__footer {
        position: absolute;
        z-index: 4;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        padding: 8mm 18mm 12mm;
    }

    .report-cover__footer-left {
        display: flex;
        flex-direction: column;
        gap: 1mm;
    }

    .report-cover__footer-tagline {
        font-size: 10.5pt;
        font-weight: 400;
        color: #ffffff;
        letter-spacing: 0.01em;
        line-height: 1.2;
    }

    .report-cover__footer-url {
        font-size: 12pt;
        font-weight: 600;
        color: var(--theme-accent);
        line-height: 1.2;
    }

    .report-cover__footer-right {
        display: flex;
        align-items: flex-end;
        flex-shrink: 0;
    }

    .report-cover__rics-logo {
        height: 15mm;
        width: auto;
        max-width: 60mm;
        display: block;
    }

    /* --- Diagonal spectrum layout --- */
    .report-cover--diagonal .report-cover__hero {
        position: absolute;
        inset: 0;
        z-index: 0;
        overflow: hidden;
    }

    .report-cover--diagonal .report-cover__hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        filter: grayscale(100%);
        opacity: 0.32;
    }

    .report-cover--diagonal .report-cover__diagonal {
        position: absolute;
        z-index: 1;
        bottom: -8mm;
        right: -26mm;
        width: 128mm;
        height: 172mm;
        background: var(--theme-primary);
        transform: skewY(-14deg);
        transform-origin: bottom right;
    }

    .report-cover--diagonal .report-cover__spectrum-edge {
        position: absolute;
        z-index: 2;
        bottom: 62mm;
        right: 7mm;
        width: 5.5mm;
        height: 118mm;
        transform: skewY(-14deg);
        transform-origin: bottom right;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .report-cover--diagonal .report-cover__spectrum-edge span {
        flex: 1;
        width: 100%;
    }

    .report-cover--diagonal .report-cover__content {
        align-items: flex-start;
        text-align: left;
        max-width: 132mm;
    }

    .report-cover--diagonal .report-cover__title,
    .report-cover--diagonal .report-cover__subtitle {
        color: var(--theme-dark);
        max-width: 120mm;
    }

    .report-cover--diagonal .report-cover__body {
        max-width: 118mm;
    }
</style>

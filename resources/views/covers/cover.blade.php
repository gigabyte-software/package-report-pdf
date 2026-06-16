@include('report-pdf::covers.partials.base-styles')

<div
    class="report-cover report-cover--diagonal"
    style="--theme-primary: {{ $themePrimary }}; --theme-dark: {{ $themeDark }}; --theme-accent: {{ $themeAccent }};"
>
    @if($heroPhotoDataUri ?? null)
        <div class="report-cover__hero" aria-hidden="true">
            <img src="{{ $heroPhotoDataUri }}" alt="">
        </div>
    @endif
    <div class="report-cover__diagonal" aria-hidden="true"></div>
    @if($includeEkBranding ?? true)
        <div class="report-cover__spectrum-edge" aria-hidden="true">
            @foreach($spectrum as $colour)
                <span style="background: {{ $colour }};"></span>
            @endforeach
        </div>
    @endif
    <div class="report-cover__content">
        @include('report-pdf::covers.partials.content')
    </div>
    @if($includeEkBranding ?? true)
        <div class="report-cover__footer-scrim" aria-hidden="true"></div>
        @include('report-pdf::covers.partials.footer')
    @endif
</div>

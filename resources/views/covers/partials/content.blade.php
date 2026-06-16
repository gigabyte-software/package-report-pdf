@if($clientLogoDataUri ?? null)
    <img src="{{ $clientLogoDataUri }}" alt="Client logo" class="report-cover__client-logo">
@endif

@if($includeEkBranding ?? true)
    <img src="{{ $ekLogoDataUri }}" alt="Earl Kendrick" class="report-cover__logo">
@endif

<div class="report-cover__body">
    <h1 class="report-cover__title">{{ $title }}</h1>
    <p class="report-cover__subtitle">{{ $subtitle }}</p>

    <div class="report-cover__meta">
        @foreach($metaLines as $line)
            <div class="report-cover__meta-line">
                @if(! empty($line['label']))
                    <span class="report-cover__meta-label">{{ $line['label'] }}</span>
                    <span class="report-cover__meta-value">{{ $line['value'] }}</span>
                @else
                    <span class="report-cover__meta-value">{{ $line['value'] }}</span>
                @endif
            </div>
        @endforeach
    </div>

    @if(! empty($author))
        <div class="report-cover__author">
            <div class="report-cover__author-name">{{ $author['full_name'] }}</div>
            @if(! empty($author['qualifications']))
                <div>{{ $author['qualifications'] }}</div>
            @endif
            @if(! empty($author['role']))
                <div>{{ $author['role'] }}</div>
            @endif
            @if(! empty($author['email']))
                <div>{{ $author['email'] }}</div>
            @endif
            @if(! empty($author['phone']))
                <div>{{ $author['phone'] }}</div>
            @endif
        </div>
    @endif

    @if(! empty($version))
        <div class="report-cover__version">Version {{ $version }}</div>
    @endif
</div>

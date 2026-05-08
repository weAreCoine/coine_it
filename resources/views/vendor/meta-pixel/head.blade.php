@if($metaPixel->isEnabled())
    {{--
        Meta Pixel — base script + init only.
        PageView is fired from resources/js/hooks/useMetaPixel.ts with the same
        event id used by the server-side Conversions API call, so that Meta can
        de-duplicate the two firings. The library default `fbq('track','PageView')`
        is intentionally removed because it would fire without an event id.

        For anonymous visitors we still pass `external_id` taken from the
        first-party `coine_uid` cookie set by the EnsureExternalId middleware.
        The same id is also forwarded to Conversions API by
        MetaPixelUserDataFactory, so Meta can match browser and server events
        for the same visitor across sessions.
    --}}
    @php
        $coineUid = request()->cookie('coine_uid');
        $coineUid = is_string($coineUid) && preg_match('/^[0-9a-f-]{36}$/i', $coineUid) ? $coineUid : null;
    @endphp
    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
    @if($user = $metaPixel->getUser())
        @if($userIdAsString)
            fbq('init', '{{ $metaPixel->pixelId() }}', {em: '{{ $user['em'] }}', external_id: '{{ $user['external_id'] }}'});
        @else
            fbq('init', '{{ $metaPixel->pixelId() }}', {em: '{{ $user['em'] }}', external_id: {{ $user['external_id'] }}});
        @endif
    @elseif($coineUid)
        fbq('init', '{{ $metaPixel->pixelId() }}', {external_id: '{{ $coineUid }}'});
    @else
        fbq('init', '{{ $metaPixel->pixelId() }}');
    @endif
    </script>
    <!-- End Meta Pixel Code -->
@endif

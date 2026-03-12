@php
    use Illuminate\Support\Facades\Route;
    $currentRouteName = Route::currentRouteName();
    $currentAction = request()->route()->getActionMethod();
    $title = "CrowdFunding-CZM-bd";
    $description = "CrowdFunding - Center for Zakat Management";
@endphp
    <!DOCTYPE html>
<html lang="eng">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">

    <title>Center for Zakat Management</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ asset('images/czm_logo_color.png') }}">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAes-KcNb3nboVMGCpW2uYg-LLh94wLZKo"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <!-- Meta Pixel Code -->
    <script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '2021617665338939');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=2021617665338939&ev=PageView&noscript=1"
        /></noscript>
    <!-- End Meta Pixel Code -->
</head>
<body class="page-wrapper">
<x-header/>
<x-navbar/>
<div id="flash-messages"></div>
<main class="max-vh-100">
    {{ $slot }}
</main>
<x-footer :footerNews="$footerNews"></x-footer>
<x-flash-script/>

@unless($currentRouteName == 'home' && $currentAction == 'home')
    <x-sidebar/>
@endunless

<a href="#" id="scrollToTop">↑</a>
</body>
</html>

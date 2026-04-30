<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Monitor de Preços')</title>
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/images/favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ Vite::asset('resources/images/favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ Vite::asset('resources/images/favicon/apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ Vite::asset('resources/images/favicon/site.webmanifest') }}" />
    <meta name="description" content="@yield('description', 'Compare preços de produtos de lojas virtuais de todo o Brasil e encontre as melhores ofertas.')">
    @stack('meta')
    @include('partials.tracking.meta-pixel')
    @include('partials.tracking.ga4')
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 min-h-screen">
    @yield('content')
    @stack('scripts')
</body>
</html>

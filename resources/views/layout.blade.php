@php
    $siteName = config('app.name', 'DestinyCommand');
    $pageTitle = trim($__env->yieldContent('title', $siteName));
    $metaDescription = trim($__env->yieldContent('meta_description', 'DestinyCommand helps Destiny 2 players and stream communities run bot commands, check stats, and set up supported chat integrations.'));
    $canonicalUrl = trim($__env->yieldContent('canonical', url()->current()));
    $metaRobots = trim($__env->yieldContent('meta_robots', 'index, follow'));
    $metaImage = trim($__env->yieldContent('meta_image'));
    $ogType = trim($__env->yieldContent('og_type', 'website'));
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="{{ $metaRobots }}">
    <meta name="author" content="DestinyCommand">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#f8fafc">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:site_name" content="{{ $siteName }}">

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">

    @if ($metaImage !== '')
        <meta property="og:image" content="{{ $metaImage }}">
        <meta name="twitter:image" content="{{ $metaImage }}">
    @endif

    @stack('meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-stone-100 font-sans text-zinc-800 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    @include('partials.header')
    @include('partials.body')
    @include('partials.footer')
    @stack('scripts')
</body>
</html>

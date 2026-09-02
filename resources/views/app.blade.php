<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class([
    'dark' => ($appearance ?? 'system') == 'dark',
])>

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Detect system dark mode --}}
    <script>
        (function() {
            const appearance =
                '{{ $appearance ?? 'system' }}';

            if (appearance === 'system') {
                const prefersDark =
                    window.matchMedia(
                        '(prefers-color-scheme: dark)',
                    ).matches;

                if (prefersDark) {
                    document.documentElement
                        .classList
                        .add('dark');
                }
            }
        })();
    </script>

    <style>
        html {
            background-color: oklch(1 0 0);
        }

        html.dark {
            background-color: oklch(0.145 0 0);
        }
    </style>

    {{-- SiOne Favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/sione-logo-transparant.png') }}?v=3">

    <link rel="shortcut icon" type="image/png" href="{{ asset('images/sione-logo-transparant.png') }}?v=3">

    <link rel="apple-touch-icon" href="{{ asset('images/sione-logo-transparant.png') }}?v=3">

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])

    <x-inertia::head>
        <title>
            {{ config('app.name', 'SiOne') }}
        </title>
    </x-inertia::head>
</head>

<body class="font-sans antialiased">
    <x-inertia::app />
</body>

</html>

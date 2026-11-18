<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->user()?->dark_mode ? 'dark' : '' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>try { const theme = localStorage.getItem('nutrition-theme'); if (theme) document.documentElement.classList.toggle('dark', theme === 'dark'); } catch (e) {}</script>

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body data-reminders='@json(auth()->user()?->healthProfile?->reminders ?? [])' class="font-sans antialiased text-slate-900 dark:text-slate-100">
        <div class="flex min-h-screen flex-col bg-emerald-50/40 dark:bg-zinc-950">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="border-b border-emerald-100 bg-white text-zinc-950 dark:border-emerald-950 dark:bg-zinc-900 dark:text-white">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
            @include('layouts.footer')
        </div>
        <div class="fixed right-4 top-20 z-50 w-[min(24rem,calc(100vw-2rem))] space-y-3" aria-live="polite">
            @if(session('status'))<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="toast toast-success"><span>{{ session('status') }}</span><button type="button" @click="show=false" aria-label="{{ __('Đóng thông báo') }}">×</button></div>@endif
            @if($errors->any())<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" x-transition class="toast toast-error"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul><button type="button" @click="show=false" aria-label="{{ __('Đóng thông báo') }}">×</button></div>@endif
        </div>
        <div id="global-loading" class="loading-overlay" hidden aria-live="assertive" aria-label="{{ __('Đang xử lý') }}">
            <div class="loading-panel"><span class="loading-spinner" aria-hidden="true"></span><span>{{ __('Đang xử lý...') }}</span></div>
        </div>
    </body>
</html>

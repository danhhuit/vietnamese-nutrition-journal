<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Bảo trì hệ thống</title>@vite(['resources/css/app.css'])</head>
<body class="grid min-h-screen place-items-center bg-[#2A8627] px-6 text-white"><main class="max-w-xl text-center"><p class="text-sm font-semibold uppercase tracking-[0.25em]">503</p><h1 class="mt-4 text-4xl font-bold">Hệ thống đang bảo trì</h1><p class="mt-4 text-lg text-green-50">{{ $message }}</p><a class="mt-8 inline-flex rounded-lg bg-white px-5 py-3 font-semibold text-[#1f651d]" href="{{ route('login') }}">Đăng nhập quản trị</a></main></body>
</html>

<x-app-layout>
    <x-slot name="header"><h1 class="text-2xl font-bold">Quản trị hệ thống</h1></x-slot>
    <x-admin.navigation />
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="card"><small>Người dùng</small><div class="text-3xl font-bold">{{ $stats['users'] }}</div></div>
        <div class="card"><small>Lượt tra cứu</small><div class="text-3xl font-bold">{{ $stats['searches'] }}</div></div>
    </div>
    <div class="card mt-6"><h2 class="text-xl font-bold">Quản lý tài khoản và cấu hình</h2><p class="mt-2 text-zinc-600 dark:text-zinc-300">Mở bảng chức năng phía trên để quản lý người dùng, dữ liệu, sao lưu và nhật ký thao tác.</p></div>
</x-app-layout>

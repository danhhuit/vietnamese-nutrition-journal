<x-app-layout>
    <x-slot name="header"><h1 class="text-2xl font-bold">Vận hành hệ thống</h1></x-slot>
    <x-admin.navigation />
    <div class="grid gap-6 lg:grid-cols-2">
        <form method="POST" action="{{ route('admin.system.maintenance') }}" class="card">@csrf @method('PUT')
            <h2 class="text-xl font-bold">Chế độ bảo trì</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Khi bật, người dùng nhận trang 503; quản trị viên vẫn đăng nhập và vận hành được.</p>
            <label class="mt-5 flex items-center gap-3"><input type="checkbox" name="enabled" value="1" @checked($maintenanceEnabled)> Bật chế độ bảo trì</label>
            <label for="maintenance-message" class="label mt-4">Thông báo cho người dùng</label>
            <textarea id="maintenance-message" name="message" class="input" rows="4" required>{{ $maintenanceMessage }}</textarea>
            <button class="btn mt-4">Lưu trạng thái</button>
        </form>
        <section class="card">
            <h2 class="text-xl font-bold">Sao lưu dữ liệu</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Tạo bản sao JSON của dữ liệu nghiệp vụ và tài khoản.</p>
            <form method="POST" action="{{ route('admin.system.backup') }}" class="mt-4">@csrf<button class="btn">Tạo bản sao lưu</button></form>
            <div class="mt-5 space-y-2">@forelse($backups as $backup)<a class="flex justify-between rounded-lg border border-zinc-200 p-3 text-sm dark:border-zinc-700" href="{{ route('admin.system.backups.download', $backup['name']) }}"><span>{{ $backup['name'] }}</span><span>{{ number_format($backup['size'] / 1024, 1) }} KB</span></a>@empty<p class="text-sm text-zinc-500">Chưa có bản sao lưu.</p>@endforelse</div>
        </section>
    </div>
    <form method="POST" action="{{ route('admin.system.restore') }}" enctype="multipart/form-data" class="card mt-6">@csrf
        <h2 class="text-xl font-bold">Phục hồi dữ liệu</h2>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Dữ liệu hiện tại sẽ được thay bằng nội dung trong tệp sao lưu hợp lệ.</p>
        <div class="mt-4 grid gap-4 md:grid-cols-2"><div><label for="backup-file" class="label">Tệp JSON</label><input id="backup-file" type="file" name="backup" accept="application/json,.json" class="input" required></div><div><label for="restore-confirmation" class="label">Nhập RESTORE để xác nhận</label><input id="restore-confirmation" name="confirmation" class="input" required></div></div>
        <button class="btn-danger mt-4">Phục hồi bản sao</button>
    </form>
</x-app-layout>

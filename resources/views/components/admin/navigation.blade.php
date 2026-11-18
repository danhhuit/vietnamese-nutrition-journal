<aside data-admin-navigation x-data="{ open: localStorage.getItem('admin-navigation-open') !== 'false' }" class="card mb-6 p-0">
    <button type="button" class="flex w-full items-center justify-between px-5 py-4 text-left font-semibold" @click="open = !open; localStorage.setItem('admin-navigation-open', open)" :aria-expanded="open.toString()">
        <span>Chức năng quản trị</span>
        <span class="relative h-5 w-5" aria-hidden="true">
            <svg x-show="!open" class="absolute h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 7 5 5 5-5"/></svg>
            <svg x-show="open" x-cloak class="absolute h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 5-5 5 5"/></svg>
        </span>
    </button>
    <nav x-show="open" x-cloak x-transition.opacity.duration.200ms class="border-t border-emerald-100 px-5 py-4 dark:border-emerald-900" aria-label="Quản trị">
        <div class="flex flex-wrap gap-2">
            <a class="admin-nav-link" href="{{ route('admin.dashboard') }}">Tổng quan</a>
            @if(auth()->user()->canAccess('manage_users'))<a class="admin-nav-link" href="{{ route('admin.users.index') }}">Người dùng</a><a class="admin-nav-link" href="{{ route('admin.permission-groups.index') }}">Nhóm quyền</a>@endif
            @if(auth()->user()->canAccess('manage_system'))<a class="admin-nav-link" href="{{ route('admin.settings.index') }}">Công thức</a><a class="admin-nav-link" href="{{ route('admin.system.index') }}">Bảo trì và sao lưu</a>@endif
        </div>
        @if(auth()->user()->canAccess('view_activity'))<div class="mt-4 border-t border-zinc-200 pt-4 dark:border-zinc-700"><a class="admin-nav-link" href="{{ route('admin.activity.index') }}">Xem nhật ký thao tác</a></div>@endif
    </nav>
</aside>

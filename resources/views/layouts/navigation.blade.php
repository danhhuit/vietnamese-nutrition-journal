<nav x-data="{ open: false }" class="sticky top-0 z-30 border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="text-lg font-bold tracking-tight">{{ __('Dinh dưỡng Việt') }}</a>
        <div class="hidden items-center gap-5 text-sm md:flex">
            @unless(auth()->user()?->isAdmin())
                <a href="{{ route('foods.index') }}">{{ __('Thực phẩm') }}</a>
                <a href="{{ route('dishes.index') }}">{{ __('Món Việt') }}</a>
                <a href="{{ route('restaurants.index') }}">{{ __('Quán ăn') }}</a>
                <a href="{{ route('calculator') }}">{{ __('Công cụ') }}</a>
                @auth<a href="{{ route('journal.index') }}">{{ __('Nhật ký') }}</a><a href="{{ route('reports.index') }}">{{ __('Thống kê') }}</a>@endauth
            @else
                <a href="{{ route('admin.dashboard') }}">Tổng quan</a>
                <a href="{{ route('admin.users.index') }}">Người dùng</a>
                <a href="{{ route('admin.settings.index') }}">Hệ thống</a>
                <a href="{{ route('admin.activity.index') }}">Nhật ký thao tác</a>
            @endunless
            @if(auth()->user()?->permission_group_id)<a href="{{ route('admin.dashboard') }}">Quản trị</a>@endif
            <button type="button" onclick="const dark = !document.documentElement.classList.contains('dark'); document.documentElement.classList.toggle('dark', dark); localStorage.setItem('nutrition-theme', dark ? 'dark' : 'light')" aria-label="Đổi giao diện sáng tối">Sáng/Tối</button>
            <form method="POST" action="{{ route('locale.update') }}">@csrf<select name="locale" aria-label="Ngôn ngữ" onchange="this.form.submit()" class="rounded-md border-zinc-300 bg-white py-1 text-sm dark:border-zinc-700 dark:bg-zinc-900"><option value="vi" @selected(app()->getLocale()==='vi')>VI</option><option value="en" @selected(app()->getLocale()==='en')>EN</option></select></form>
            @auth
                <a href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button>{{ __('Đăng xuất') }}</button></form>
            @else
                <a href="{{ route('login') }}">{{ __('Đăng nhập') }}</a><a class="btn" href="{{ route('register') }}">{{ __('Đăng ký') }}</a>
            @endauth
        </div>
        <button type="button" @click="open = !open" class="btn-secondary md:hidden" aria-label="Mở menu" :aria-expanded="open.toString()">Menu</button>
    </div>
    <div x-show="open" x-cloak class="space-y-3 border-t border-zinc-200 px-4 py-4 text-sm md:hidden">
        <button type="button" onclick="const dark = !document.documentElement.classList.contains('dark'); document.documentElement.classList.toggle('dark', dark); localStorage.setItem('nutrition-theme', dark ? 'dark' : 'light')">Sáng/Tối</button><form method="POST" action="{{ route('locale.update') }}">@csrf<select name="locale" aria-label="Ngôn ngữ" onchange="this.form.submit()" class="input max-w-24"><option value="vi" @selected(app()->getLocale()==='vi')>VI</option><option value="en" @selected(app()->getLocale()==='en')>EN</option></select></form>
        @unless(auth()->user()?->isAdmin())
            @foreach([route('home')=>__('Trang chủ'),route('foods.index')=>__('Thực phẩm'),route('dishes.index')=>__('Món Việt'),route('restaurants.index')=>__('Quán ăn'),route('calculator')=>__('Công cụ')] as $url=>$label)<a class="block" href="{{ $url }}">{{ $label }}</a>@endforeach
            @auth<a class="block" href="{{ route('journal.index') }}">{{ __('Nhật ký') }}</a><a class="block" href="{{ route('reports.index') }}">{{ __('Thống kê') }}</a>@endauth
        @else
            @foreach([route('admin.dashboard')=>'Tổng quan',route('admin.users.index')=>'Người dùng',route('admin.settings.index')=>'Hệ thống',route('admin.activity.index')=>'Nhật ký thao tác'] as $url=>$label)<a class="block" href="{{ $url }}">{{ $label }}</a>@endforeach
        @endunless
        @auth<a class="block" href="{{ route('profile.edit') }}">{{ __('Cá nhân') }}</a><form method="POST" action="{{ route('logout') }}">@csrf<button>{{ __('Đăng xuất') }}</button></form>@endauth
    </div>
</nav>

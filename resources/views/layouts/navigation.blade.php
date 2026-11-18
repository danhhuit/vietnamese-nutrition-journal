<nav x-data="{ open: false }" class="site-nav sticky top-0 z-40">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="text-lg font-extrabold tracking-tight text-white hover:no-underline">{{ __('Dinh dưỡng Việt') }}</a>
        <div class="hidden items-center gap-5 text-sm font-medium text-white md:flex">
            @unless(auth()->user()?->isAdmin())
                <a href="{{ route('foods.index') }}">{{ __('Thực phẩm') }}</a>
                <a href="{{ route('dishes.index') }}">{{ __('Món Việt') }}</a>
                <a href="{{ route('restaurants.index') }}">{{ __('Quán ăn') }}</a>
                <a href="{{ route('calculator') }}">{{ __('Công cụ') }}</a>
                @auth<a href="{{ route('journal.index') }}">{{ __('Nhật ký') }}</a><a href="{{ route('reports.index') }}">{{ __('Thống kê') }}</a>@endauth
            @else
                <a href="{{ route('admin.dashboard') }}">{{ __('Tổng quan') }}</a>
                <a href="{{ route('admin.users.index') }}">{{ __('Người dùng') }}</a>
                <a href="{{ route('admin.system.index') }}">{{ __('Hệ thống') }}</a>
            @endunless
            @if(auth()->user()?->permission_group_id)<a href="{{ route('admin.dashboard') }}">{{ __('Quản trị') }}</a>@endif
            <button type="button" class="theme-toggle" onclick="window.toggleNutritionTheme()" aria-label="{{ __('Đổi giao diện sáng tối') }}">
                <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
                <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.5 14.4A8.5 8.5 0 0 1 9.6 3.5 8.5 8.5 0 1 0 20.5 14.4Z"/></svg>
            </button>
            <form method="POST" action="{{ route('locale.update') }}">@csrf<select name="locale" aria-label="{{ __('Ngôn ngữ') }}" onchange="this.form.submit()" class="locale-select"><option value="vi" @selected(app()->getLocale()==='vi')>VI</option><option value="en" @selected(app()->getLocale()==='en')>EN</option></select></form>
            @auth
                <a href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button>{{ __('Đăng xuất') }}</button></form>
            @else
                <a href="{{ route('login') }}">{{ __('Đăng nhập') }}</a><a class="nav-cta" href="{{ route('register') }}">{{ __('Đăng ký') }}</a>
            @endauth
        </div>
        <button type="button" @click="open = !open" class="nav-cta md:hidden" aria-label="{{ __('Mở menu') }}" :aria-expanded="open.toString()">{{ __('Menu') }}</button>
    </div>
    <div x-show="open" x-cloak x-transition class="space-y-3 border-t border-white/20 px-4 py-4 text-sm text-white md:hidden">
        <div class="flex items-center gap-4"><button type="button" class="theme-toggle" onclick="window.toggleNutritionTheme()" aria-label="{{ __('Đổi giao diện sáng tối') }}"><svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2"/></svg><svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.5 14.4A8.5 8.5 0 0 1 9.6 3.5 8.5 8.5 0 1 0 20.5 14.4Z"/></svg></button><form method="POST" action="{{ route('locale.update') }}">@csrf<select name="locale" aria-label="{{ __('Ngôn ngữ') }}" onchange="this.form.submit()" class="locale-select"><option value="vi" @selected(app()->getLocale()==='vi')>VI</option><option value="en" @selected(app()->getLocale()==='en')>EN</option></select></form></div>
        @unless(auth()->user()?->isAdmin())
            @foreach([route('home')=>__('Trang chủ'),route('foods.index')=>__('Thực phẩm'),route('dishes.index')=>__('Món Việt'),route('restaurants.index')=>__('Quán ăn'),route('calculator')=>__('Công cụ')] as $url=>$label)<a class="block" href="{{ $url }}">{{ $label }}</a>@endforeach
            @auth<a class="block" href="{{ route('journal.index') }}">{{ __('Nhật ký') }}</a><a class="block" href="{{ route('reports.index') }}">{{ __('Thống kê') }}</a>@endauth
        @else
            @foreach([route('admin.dashboard')=>__('Tổng quan'),route('admin.users.index')=>__('Người dùng'),route('admin.system.index')=>__('Hệ thống')] as $url=>$label)<a class="block" href="{{ $url }}">{{ $label }}</a>@endforeach
        @endunless
        @auth<a class="block" href="{{ route('profile.edit') }}">{{ __('Cá nhân') }}</a><form method="POST" action="{{ route('logout') }}">@csrf<button>{{ __('Đăng xuất') }}</button></form>@endauth
    </div>
</nav>

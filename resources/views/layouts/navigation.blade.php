<nav x-data="{ open: false }" class="sticky top-0 z-30 border-b border-emerald-800 bg-emerald-700 text-white shadow-sm">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="text-lg font-black">🍃 {{ __('Dinh dưỡng Việt') }}</a>
        <div class="hidden items-center gap-4 text-sm font-semibold md:flex">
            <a href="{{ route('dishes.index') }}" class="hover:text-amber-200">{{ __('Món Việt') }}</a>
            <a href="{{ route('foods.index') }}" class="hover:text-amber-200">{{ __('Thực phẩm') }}</a>
            <a href="{{ route('restaurants.index') }}" class="hover:text-amber-200">{{ __('Quán ăn') }}</a>
            <a href="{{ route('calculator') }}" class="hover:text-amber-200">{{ __('Công cụ') }}</a>
            @auth
                <a href="{{ route('journal.index') }}" class="hover:text-amber-200">{{ __('Nhật ký') }}</a>
                <a href="{{ route('reports.index') }}" class="hover:text-amber-200">{{ __('Thống kê') }}</a>
                @if(auth()->user()->isAdmin())<a href="{{ route('admin.dashboard') }}" class="rounded-lg bg-amber-400 px-3 py-1.5 text-emerald-950">Admin</a>@endif
                <a href="{{ route('profile.edit') }}" class="hover:text-amber-200">{{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="hover:text-amber-200">{{ __('Đăng xuất') }}</button></form>
            @else
                <a href="{{ route('login') }}">{{ __('Đăng nhập') }}</a><a href="{{ route('register') }}" class="rounded-lg bg-amber-400 px-3 py-1.5 text-emerald-950">{{ __('Đăng ký') }}</a>
            @endauth
        </div>
        <button @click="open=!open" class="rounded-lg p-2 md:hidden" aria-label="Mở điều hướng">☰</button>
    </div>
    <div x-show="open" x-cloak class="space-y-2 border-t border-emerald-600 px-4 py-4 md:hidden">
        @foreach([route('home')=>__('Trang chủ'),route('dishes.index')=>__('Món Việt'),route('restaurants.index')=>__('Quán ăn'),route('calculator')=>__('Công cụ')] as $url=>$label)<a class="block" href="{{ $url }}">{{ $label }}</a>@endforeach
        @auth<a class="block" href="{{ route('journal.index') }}">{{ __('Nhật ký') }}</a><a class="block" href="{{ route('reports.index') }}">{{ __('Thống kê') }}</a><a class="block" href="{{ route('profile.edit') }}">{{ __('Cá nhân') }}</a>@endauth
    </div>
</nav>

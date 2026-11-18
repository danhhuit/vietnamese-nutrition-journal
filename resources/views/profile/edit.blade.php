<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
    <div class="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8"><h2 class="text-lg font-medium">Dinh dưỡng & tiện ích</h2><div class="mt-4 flex flex-wrap gap-3"><a class="btn" href="{{ route('health-profile.edit') }}">Hồ sơ thể trạng</a><a class="btn-secondary" href="{{ route('favorites.index') }}">Yêu thích</a></div></div>
        <form method="POST" action="{{ route('preferences.update') }}" class="bg-white p-4 shadow dark:bg-slate-800 sm:rounded-lg sm:p-8">@csrf @method('PATCH')<h2 class="text-lg font-medium">{{ __('Ngôn ngữ & giao diện') }}</h2><div class="mt-4 flex flex-wrap items-center gap-4"><select name="locale" class="input max-w-xs"><option value="vi" @selected(auth()->user()->locale==='vi')>Tiếng Việt</option><option value="en" @selected(auth()->user()->locale==='en')>English</option></select><label><input type="checkbox" name="dark_mode" value="1" @checked(auth()->user()->dark_mode)> {{ __('Chế độ tối') }}</label><button class="btn">{{ __('Lưu tùy chọn') }}</button></div></form>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold">Tìm quán trực tiếp trên Google Maps</h1>
        <p class="mt-1 text-sm">Kết quả được truy vấn trực tiếp, không sao chép hàng loạt vào cơ sở dữ liệu của ứng dụng.</p>
    </x-slot>

    <form class="card mb-6 flex flex-col gap-3 sm:flex-row" method="GET" action="{{ route('restaurants.google') }}">
        <label class="sr-only" for="google-place-query">Tên món hoặc quán</label>
        <input id="google-place-query" name="q" class="input flex-1" value="{{ request('q', $query) }}" maxlength="100" placeholder="Ví dụ: phở, cơm tấm, bún bò">
        <button class="btn" type="submit">Tìm trên Google Maps</button>
    </form>

    @unless($configured)
        <div class="card border-amber-300 bg-amber-50 text-amber-950">
            Quản trị viên cần đặt <code>GOOGLE_PLACES_API_KEY</code> trong <code>.env</code> để bật tìm kiếm trực tiếp.
        </div>
    @endunless

    @if($error)
        <div class="card border-red-300 bg-red-50 text-red-900" role="alert">{{ $error }}</div>
    @endif

    @if($configured && !$error)
        <div class="mb-4 flex items-center justify-between gap-3">
            <p class="font-semibold">{{ count($places) }} kết quả trong trang này</p>
            <span class="text-sm text-zinc-500">Powered by Google</span>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            @forelse($places as $place)
                <article class="card">
                    <h2 class="font-bold">{{ $place['name'] }}</h2>
                    <p class="mt-2 text-sm">{{ $place['address'] }}</p>
                    @if($place['maps_url'])
                        <a class="btn-secondary mt-4" href="{{ $place['maps_url'] }}" target="_blank" rel="noopener noreferrer">Mở trong Google Maps</a>
                    @endif
                </article>
            @empty
                <p class="card md:col-span-2">Không tìm thấy kết quả phù hợp.</p>
            @endforelse
        </div>

        @if($nextPageToken)
            <form class="mt-6 text-center" method="GET" action="{{ route('restaurants.google') }}">
                <input type="hidden" name="q" value="{{ $query }}">
                <input type="hidden" name="page_token" value="{{ $nextPageToken }}">
                <button class="btn-secondary" type="submit">Xem thêm kết quả</button>
            </form>
        @endif
    @endif

    <p class="mt-8 text-xs text-zinc-500">Thông tin quán có thể thay đổi. Hãy kiểm tra lại địa chỉ, giờ mở cửa và liên hệ trên Google Maps trước khi đến.</p>
</x-app-layout>

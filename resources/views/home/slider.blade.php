@php($slides = [
    ['Tra cứu giá trị dinh dưỡng', 'Xem năng lượng và các thành phần trong 100g thực phẩm.', route('foods.index'), 'Khám phá thực phẩm'],
    ['Món Việt mỗi ngày', 'So sánh khẩu phần và chọn món phù hợp với mục tiêu.', route('dishes.index'), 'Xem món ăn'],
    ['Tìm quán ăn', 'Đối chiếu địa chỉ, thực đơn và vị trí trước khi đến.', route('restaurants.index'), 'Xem quán ăn'],
    ['Ghi nhật ký ăn uống', 'Theo dõi bữa sáng, trưa, tối và bữa phụ trong một nơi.', auth()->check() ? route('journal.index') : route('register'), 'Bắt đầu ghi chép'],
    ['Hiểu nhu cầu của bạn', 'Tính BMI, BMR và mức năng lượng tham khảo.', route('calculator'), 'Mở công cụ'],
])
<section x-data="{ current: 0, timer: null }" x-init="timer = setInterval(() => current = (current + 1) % 5, 7000)" class="card relative overflow-hidden p-8 sm:p-14" aria-label="Giới thiệu tính năng">
    @foreach($slides as $index => [$title, $description, $url, $action])
        <div x-show="current === {{ $index }}" x-cloak class="max-w-2xl" aria-live="polite">
            <p class="text-sm font-semibold uppercase tracking-widest text-zinc-500">{{ sprintf('%02d / 05', $index + 1) }}</p>
            <h2 class="mt-6 text-4xl font-bold tracking-tight sm:text-6xl">{{ $title }}</h2>
            <p class="mt-5 max-w-xl text-lg leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $description }}</p>
            <a class="btn mt-8" href="{{ $url }}">{{ $action }}</a>
        </div>
    @endforeach
    <div class="mt-12 flex items-center gap-3" role="tablist" aria-label="Chọn trang giới thiệu">
        @foreach($slides as $index => $slide)
            <button type="button" role="tab" @click="current = {{ $index }}; clearInterval(timer)" :aria-selected="(current === {{ $index }}).toString()" class="h-2 w-8 rounded-full bg-zinc-300 dark:bg-zinc-700" :class="current === {{ $index }} ? 'bg-zinc-950 dark:bg-white' : ''" aria-label="Trang {{ $index + 1 }}"></button>
        @endforeach
    </div>
</section>

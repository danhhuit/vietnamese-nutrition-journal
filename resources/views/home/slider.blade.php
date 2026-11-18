@php($slides = [
    [__('Tra cứu giá trị dinh dưỡng'), __('Xem năng lượng và các thành phần trong 100g thực phẩm.'), route('foods.index'), __('Khám phá thực phẩm'), '/images/slides/nutrition.svg'],
    [__('Món Việt mỗi ngày'), __('So sánh khẩu phần và chọn món phù hợp với mục tiêu.'), route('dishes.index'), __('Xem món ăn'), '/images/slides/vietnamese-food.svg'],
    [__('Tìm quán ăn'), __('Đối chiếu địa chỉ, thực đơn và vị trí trước khi đến.'), route('restaurants.index'), __('Xem quán ăn'), '/images/slides/restaurant.svg'],
    [__('Ghi nhật ký ăn uống'), __('Theo dõi bữa sáng, trưa, tối và bữa phụ trong một nơi.'), auth()->check() ? route('journal.index') : route('register'), __('Bắt đầu ghi chép'), '/images/slides/journal.svg'],
    [__('Hiểu nhu cầu của bạn'), __('Tính BMI, BMR và mức năng lượng tham khảo.'), route('calculator'), __('Mở công cụ'), '/images/slides/calculator.svg'],
])
<section x-data="{ current: 0, timer: null, start() { this.stop(); this.timer = setInterval(() => this.current = (this.current + 1) % 5, 5000) }, stop() { if (this.timer) clearInterval(this.timer) } }" x-init="start()" @mouseenter="stop()" @mouseleave="start()" @focusin="stop()" @focusout="start()" class="relative overflow-hidden rounded-3xl bg-[#2A8627] text-white shadow-xl shadow-emerald-950/10" aria-label="{{ __('Giới thiệu tính năng') }}">
    @foreach($slides as $index => [$title, $description, $url, $action, $image])
        <div x-show="current === {{ $index }}" x-cloak x-transition.opacity.duration.500ms class="grid min-h-[30rem] items-center gap-8 px-8 py-12 sm:px-14 lg:grid-cols-2" aria-live="polite">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-emerald-100">{{ sprintf('%02d / 05', $index + 1) }}</p>
                <h2 class="mt-5 text-4xl font-extrabold tracking-tight sm:text-6xl">{{ $title }}</h2>
                <p class="mt-5 max-w-xl text-lg leading-relaxed text-emerald-50">{{ $description }}</p>
                <a class="mt-8 inline-flex rounded-lg bg-white px-5 py-3 font-bold text-emerald-900 transition hover:bg-emerald-50 hover:no-underline" href="{{ $url }}">{{ $action }}</a>
            </div>
            <img class="mx-auto h-72 w-full max-w-xl rounded-2xl object-cover shadow-2xl shadow-emerald-950/20 sm:h-80" src="{{ $image }}" alt="{{ $title }}" width="640" height="400">
        </div>
    @endforeach
    <div class="absolute bottom-5 left-8 flex items-center gap-3 sm:left-14" role="tablist" aria-label="{{ __('Chọn trang giới thiệu') }}">
        @foreach($slides as $index => $slide)
            <button type="button" role="tab" @click="current = {{ $index }}; start()" :aria-selected="(current === {{ $index }}).toString()" class="h-2 w-8 rounded-full bg-white/35 transition-all" :class="current === {{ $index }} ? 'bg-white w-12' : ''" aria-label="{{ __('Trang') }} {{ $index + 1 }}"></button>
        @endforeach
    </div>
</section>

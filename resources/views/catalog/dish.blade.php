<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-black">{{ $dish->name }}</h1>
        <p>{{ $dish->description }}</p>
    </x-slot>

    @if($hasAllergy)
        <div class="mb-5 rounded-xl border border-zinc-500 bg-zinc-100 p-4 font-bold dark:bg-zinc-800">Món này chứa thành phần trùng với dị ứng trong hồ sơ của bạn.</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-2">
            <form x-data="{ grams: {{ (float) $grams }} }" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="label">Khẩu phần (gram)</label>
                    <input x-model.number="grams" name="grams" type="number" min="1" class="input">
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($units as $unit)
                        <button type="button" @click="grams={{ (float) $unit->grams }}" class="badge">{{ $unit->name_vi }}</button>
                    @endforeach
                </div>
                <button class="btn">Quy đổi</button>
            </form>

            <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach(['calories'=>'Kcal','protein_g'=>'Protein','fat_g'=>'Fat','carb_g'=>'Carb','fiber_g'=>'Chất xơ','sodium_mg'=>'Natri','sugar_g'=>'Đường'] as $key=>$label)
                    <div class="rounded-xl bg-zinc-100 p-3 text-zinc-950 dark:bg-zinc-800 dark:text-zinc-100">
                        <small>{{ $label }}</small>
                        <div class="text-2xl font-black">{{ $nutrition[$key] }}{{ $key === 'calories' ? '' : ($key === 'sodium_mg' ? 'mg' : 'g') }}</div>
                    </div>
                @endforeach
            </div>

            <h2 class="mt-7 text-xl font-bold">Nguyên liệu tham khảo</h2>
            <ul class="mt-2 list-disc pl-5">
                @forelse($dish->ingredients as $ingredient)
                    <li>{{ $ingredient->name }} — {{ $ingredient->pivot->grams }}g</li>
                @empty<li>Chưa đối chiếu công thức nguyên liệu cho món này.</li>@endforelse
            </ul>
            <div class="mt-4 flex gap-2">
                @if($dish->is_low_calorie)<span class="badge">Ít calo</span>@endif
                @if($dish->is_low_sodium)<span class="badge">Ít muối</span>@endif
                @if($dish->is_high_protein)<span class="badge">Giàu đạm</span>@endif
                @if($dish->is_vegetarian)<span class="badge">Món chay</span>@endif
            </div>
        </section>

        <aside class="space-y-4">
            <div class="card">
                <h2 class="font-bold">Có ở quán nào?</h2>
                @forelse($dish->restaurants as $restaurant)
                    <a class="mt-3 block rounded-xl bg-slate-50 p-3" href="{{ route('restaurants.show', $restaurant) }}">
                        <strong>{{ $restaurant->name }}</strong>
                        <div>{{ $restaurant->pivot->price > 0 ? number_format($restaurant->pivot->price).'đ' : 'Chưa rõ giá' }} · {{ $restaurant->area }}</div>
                    </a>
                @empty
                    <p class="mt-2 text-slate-500">Chưa có quán bán món này.</p>
                @endforelse
            </div>

            @auth
                <div class="card">
                    <form method="POST" action="{{ route('journal.store') }}">
                        @csrf
                        <input type="hidden" name="dish_id" value="{{ $dish->id }}">
                        <input type="hidden" name="logged_on" value="{{ now()->toDateString() }}">
                        <label class="label">Bữa ăn</label>
                        <select name="meal_type" class="input"><option value="breakfast">Sáng</option><option value="lunch">Trưa</option><option value="dinner">Tối</option><option value="snack">Phụ</option></select>
                        <label class="label mt-3">Số khẩu phần</label>
                        <input name="quantity" value="1" type="number" step="0.1" class="input">
                        <button class="btn mt-4 w-full">Đã ăn món này</button>
                    </form>
                    <form class="mt-2" method="POST" action="{{ route('favorites.toggle', ['dish', $dish->id]) }}">
                        @csrf
                        <button class="btn-secondary w-full">Yêu thích</button>
                    </form>
                </div>
            @endauth
        </aside>
    </div>
</x-app-layout>

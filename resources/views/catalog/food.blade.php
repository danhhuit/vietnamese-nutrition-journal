<x-app-layout>
    <x-slot name="header"><h1 class="text-3xl font-bold">{{ $food->name }}</h1><p>{{ $food->foodGroup->name_vi }} · {{ $food->source }}</p></x-slot>
    <div x-data="{ grams: 100 }" class="grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-2"><h2 class="text-xl font-bold">Giá trị theo khẩu phần</h2><div class="table-wrap mt-4"><table class="table"><thead><tr><th>Thành phần</th><th>Hàm lượng</th></tr></thead><tbody>
            @foreach(['calories'=>'Năng lượng (Kcal)','protein_g'=>'Protein (g)','fat_g'=>'Chất béo (g)','carb_g'=>'Carbohydrate (g)','fiber_g'=>'Chất xơ (g)','calcium_mg'=>'Canxi (mg)','iron_mg'=>'Sắt (mg)','sodium_mg'=>'Natri (mg)','potassium_mg'=>'Kali (mg)','vitamin_a_mcg'=>'Vitamin A (mcg)','vitamin_c_mg'=>'Vitamin C (mg)'] as $key=>$label)
                <tr><td>{{ $label }}</td><td>@if((str_starts_with($food->source ?? '', 'Ước tính') && ! in_array($key, ['calories','protein_g','fat_g','carb_g','sodium_mg'])) || ((float) $food->$key === 0.0 && in_array($key, ['calcium_mg','iron_mg','sodium_mg','potassium_mg','vitamin_a_mcg','vitamin_c_mg'])))Chưa có dữ liệu @else <span x-text="({{ (float) $food->$key }} * grams / 100).toFixed(1)"></span> @endif</td></tr>
            @endforeach
        </tbody></table></div></section>
        <aside class="card"><label for="serving-grams" class="label">Khẩu phần (gram)</label><input id="serving-grams" x-model.number="grams" type="number" min="1" class="input"><div class="mt-3 flex flex-wrap gap-2">@foreach($units as $unit)<button type="button" @click="grams={{ (float) $unit->grams }}" class="badge">{{ $unit->name_vi }}</button>@endforeach</div>@auth<form class="mt-4" method="POST" action="{{ route('favorites.toggle',['food',$food->id]) }}">@csrf<button class="btn-secondary w-full">Yêu thích</button></form>@endauth</aside>
    </div>
</x-app-layout>

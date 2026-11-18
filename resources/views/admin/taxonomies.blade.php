<x-app-layout>
    <x-slot name="header"><h1 class="text-2xl font-black">Nhóm, khẩu phần & dị ứng</h1></x-slot>
    <x-admin.navigation />

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card">
            <h2 class="font-bold">Nhóm thực phẩm</h2>
            <form method="POST" action="{{ route('admin.taxonomies.save', 'group') }}" class="mt-3 space-y-2">
                @csrf
                <input name="name_vi" class="input" placeholder="Tên Việt" required>
                <input name="name_en" class="input" placeholder="Tên Anh">
                <button class="btn">Thêm</button>
            </form>
            @foreach($groups as $group)
                <div class="mt-4 border-t pt-3">
                    <form method="POST" action="{{ route('admin.taxonomies.save', ['group', $group->id]) }}" class="space-y-2">
                        @csrf
                        <input name="name_vi" class="input" value="{{ $group->name_vi }}" required>
                        <input name="name_en" class="input" value="{{ $group->name_en }}">
                        <button class="text-sm font-semibold text-emerald-700">Lưu sửa</button>
                    </form>
                    <form method="POST" action="{{ route('admin.taxonomies.destroy', ['group', $group->id]) }}">@csrf @method('DELETE')<button class="text-sm text-red-600">Xóa</button></form>
                </div>
            @endforeach
        </section>

        <section class="card">
            <h2 class="font-bold">Đơn vị khẩu phần</h2>
            <form method="POST" action="{{ route('admin.taxonomies.save', 'unit') }}" class="mt-3 space-y-2">
                @csrf
                <input name="name_vi" class="input" placeholder="Tô, chén..." required>
                <input name="name_en" class="input" placeholder="Bowl, spoon...">
                <input name="grams" type="number" step="0.1" class="input" placeholder="Gram" required>
                <select name="applies_to" class="input"><option value="both">Tất cả</option><option value="food">Thực phẩm</option><option value="dish">Món</option></select>
                <button class="btn">Thêm</button>
            </form>
            @foreach($units as $unit)
                <div class="mt-4 border-t pt-3">
                    <form method="POST" action="{{ route('admin.taxonomies.save', ['unit', $unit->id]) }}" class="space-y-2">
                        @csrf
                        <input name="name_vi" class="input" value="{{ $unit->name_vi }}" required>
                        <input name="name_en" class="input" value="{{ $unit->name_en }}">
                        <input name="grams" type="number" step="0.1" class="input" value="{{ $unit->grams }}" required>
                        <select name="applies_to" class="input">@foreach(['both'=>'Tất cả','food'=>'Thực phẩm','dish'=>'Món'] as $key=>$label)<option value="{{ $key }}" @selected($unit->applies_to === $key)>{{ $label }}</option>@endforeach</select>
                        <button class="text-sm font-semibold text-emerald-700">Lưu sửa</button>
                    </form>
                    <form method="POST" action="{{ route('admin.taxonomies.destroy', ['unit', $unit->id]) }}">@csrf @method('DELETE')<button class="text-sm text-red-600">Xóa</button></form>
                </div>
            @endforeach
        </section>

        <section class="card">
            <h2 class="font-bold">Nhãn dị ứng</h2>
            <form method="POST" action="{{ route('admin.taxonomies.save', 'allergy') }}" class="mt-3 flex gap-2">
                @csrf
                <input name="name" class="input" placeholder="Hải sản..." required>
                <button class="btn">Thêm</button>
            </form>
            @foreach($allergies as $allergy)
                <div class="mt-4 border-t pt-3">
                    <form method="POST" action="{{ route('admin.taxonomies.save', ['allergy', $allergy->id]) }}" class="flex gap-2">
                        @csrf
                        <input name="name" class="input" value="{{ $allergy->name }}" required>
                        <button class="text-sm font-semibold text-emerald-700">Lưu</button>
                    </form>
                    <form method="POST" action="{{ route('admin.taxonomies.destroy', ['allergy', $allergy->id]) }}">@csrf @method('DELETE')<button class="text-sm text-red-600">Xóa</button></form>
                </div>
            @endforeach
        </section>
    </div>
</x-app-layout>

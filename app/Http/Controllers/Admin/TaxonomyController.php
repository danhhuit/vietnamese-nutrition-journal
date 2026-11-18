<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllergyTag;
use App\Models\FoodGroup;
use App\Models\ServingUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class TaxonomyController extends Controller
{
    public function index(): View
    {
        return view('admin.taxonomies', ['groups' => FoodGroup::orderBy('name_vi')->get(), 'units' => ServingUnit::orderBy('name_vi')->get(), 'allergies' => AllergyTag::orderBy('name')->get()]);
    }

    public function save(Request $request, string $type, ?int $id = null): RedirectResponse
    {
        $map = ['group' => FoodGroup::class, 'unit' => ServingUnit::class, 'allergy' => AllergyTag::class];
        abort_unless(isset($map[$type]), 404);
        $class = $map[$type];
        $model = $id ? $class::findOrFail($id) : new $class;
        if ($type === 'group') {
            $data = $request->validate(['name_vi' => ['required', 'string', 'max:255'], 'name_en' => ['nullable', 'string', 'max:255']]) + ['slug' => Str::slug($request->string('name_vi'))];
        } elseif ($type === 'unit') {
            $data = $request->validate(['name_vi' => ['required', 'string', 'max:100'], 'name_en' => ['nullable', 'string', 'max:100'], 'grams' => ['required', 'numeric', 'between:0.1,5000'], 'applies_to' => ['required', 'in:food,dish,both']]);
        } else {
            $data = $request->validate(['name' => ['required', 'string', 'max:100']]) + ['slug' => Str::slug($request->string('name'))];
        }
        $model->fill($data)->save();

        return back()->with('status', 'Đã lưu danh mục.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $map = ['group' => FoodGroup::class, 'unit' => ServingUnit::class, 'allergy' => AllergyTag::class];
        abort_unless(isset($map[$type]), 404);
        $map[$type]::findOrFail($id)->delete();

        return back()->with('status', 'Đã xóa danh mục.');
    }
}

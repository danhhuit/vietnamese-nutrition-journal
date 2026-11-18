<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.settings', ['settings' => Setting::where('type', 'number')->orderBy('key')->get()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(['values' => ['required', 'array'], 'values.*' => ['required', 'numeric', 'between:-5000,100000']]);
        foreach ($data['values'] as $id => $value) {
            Setting::whereKey($id)->update(['value' => (string) $value]);
        }

        return back()->with('status', 'Đã cập nhật công thức và ngưỡng.');
    }
}

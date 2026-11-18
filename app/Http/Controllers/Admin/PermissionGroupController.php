<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermissionGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PermissionGroupController extends Controller
{
    public function index(): View
    {
        return view('admin.permissions', ['groups' => PermissionGroup::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:permission_groups,name'], 'permissions' => ['nullable', 'array'], 'permissions.*' => ['in:manage_users,manage_system,view_activity']]);
        PermissionGroup::create(['name' => $data['name'], 'permissions' => $data['permissions'] ?? []]);

        return back()->with('status', 'Đã tạo nhóm quyền.');
    }

    public function destroy(Request $request, PermissionGroup $group): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $group->delete();

        return back()->with('status', 'Đã xóa nhóm quyền.');
    }
}

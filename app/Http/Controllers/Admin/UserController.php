<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class UserController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.users', ['users' => User::with('permissionGroup')->when($request->filled('q'), fn ($q) => $q->where(fn ($x) => $x->where('name', 'like', '%'.$request->string('q').'%')->orWhere('email', 'like', '%'.$request->string('q').'%')))->latest()->paginate(20), 'groups' => PermissionGroup::orderBy('name')->get()]);
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 422, 'Không thể khóa tài khoản quản trị.');
        $user->forceFill(['status' => $user->status === 'active' ? 'locked' : 'active'])->save();

        return back()->with('status', 'Đã cập nhật trạng thái người dùng.');
    }

    public function updateGroup(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_if($request->user()->is($user), 422);
        $data = $request->validate(['permission_group_id' => ['nullable', 'exists:permission_groups,id']]);
        $user->forceFill(['permission_group_id' => $data['permission_group_id'] ?? null])->save();

        return back()->with('status', 'Đã cập nhật nhóm quyền.');
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate(['password' => ['required', 'string', 'min:12', 'confirmed']]);
        $user->update(['password' => $data['password']]);

        return back()->with('status', 'Đã đổi mật khẩu người dùng.');
    }
}

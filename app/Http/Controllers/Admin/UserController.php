<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class UserController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.users', ['users' => User::when($request->filled('q'), fn ($q) => $q->where(fn ($x) => $x->where('name', 'like', '%'.$request->string('q').'%')->orWhere('email', 'like', '%'.$request->string('q').'%')))->latest()->paginate(20)]);
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->is($user), 422, 'Không thể tự khóa tài khoản quản trị.');
        $user->forceFill(['status' => $user->status === 'active' ? 'locked' : 'active'])->save();

        return back()->with('status', 'Đã cập nhật trạng thái người dùng.');
    }
}

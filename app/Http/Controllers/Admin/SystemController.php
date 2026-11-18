<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class SystemController extends Controller
{
    public function index(DatabaseBackupService $backups): View
    {
        return view('admin.system', [
            'maintenanceEnabled' => Setting::boolean('maintenance_enabled'),
            'maintenanceMessage' => Setting::text('maintenance_message', 'Hệ thống đang bảo trì. Vui lòng quay lại sau.'),
            'backups' => $backups->files(),
        ]);
    }

    public function maintenance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'message' => ['required', 'string', 'max:500'],
        ]);
        Setting::updateOrCreate(['key' => 'maintenance_enabled'], ['label' => 'Chế độ bảo trì', 'type' => 'boolean', 'value' => $request->boolean('enabled') ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'maintenance_message'], ['label' => 'Thông báo bảo trì', 'type' => 'text', 'value' => $data['message']]);

        return back()->with('status', $request->boolean('enabled') ? 'Đã bật chế độ bảo trì.' : 'Đã mở lại ứng dụng.');
    }

    public function backup(DatabaseBackupService $backups): RedirectResponse
    {
        $backups->create();

        return back()->with('status', 'Đã tạo bản sao lưu dữ liệu.');
    }

    public function download(string $file): BinaryFileResponse
    {
        abort_unless((bool) preg_match('/\Anutrition-backup-\d{8}-\d{6}\.json\z/', $file), 404);

        return response()->download(storage_path('app/private/backups/'.$file));
    }

    public function restore(Request $request, DatabaseBackupService $backups): RedirectResponse
    {
        $data = $request->validate([
            'backup' => ['required', 'file', 'mimes:json,txt', 'max:20480'],
            'confirmation' => ['required', Rule::in(['RESTORE'])],
        ]);
        $backups->restore($data['backup']);

        return redirect()->route('admin.system.index')->with('status', 'Đã phục hồi dữ liệu từ bản sao lưu.');
    }
}

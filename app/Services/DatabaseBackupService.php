<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final class DatabaseBackupService
{
    /** @var list<string> */
    private const TABLES = [
        'permission_groups', 'users', 'allergy_tags', 'food_groups', 'serving_units', 'foods', 'dishes',
        'restaurants', 'settings', 'health_profiles', 'allergy_tag_health_profile', 'allergy_tag_dish',
        'dish_food', 'dish_restaurant', 'daily_logs', 'food_log_entries', 'water_logs', 'weight_entries',
        'favorites', 'search_logs', 'activity_logs',
    ];

    public function create(): string
    {
        $tables = [];
        foreach (self::TABLES as $table) {
            $tables[$table] = Schema::hasTable($table) ? DB::table($table)->get()->map(fn (object $row): array => (array) $row)->all() : [];
        }

        $payload = json_encode([
            'format' => 'nutrition-diary-backup',
            'version' => 1,
            'created_at' => now()->toIso8601String(),
            'tables' => $tables,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $path = 'backups/nutrition-backup-'.now()->format('Ymd-His').'.json';
        Storage::disk('local')->put($path, $payload);

        return $path;
    }

    public function restore(UploadedFile $file): void
    {
        $decoded = json_decode($file->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (($decoded['format'] ?? null) !== 'nutrition-diary-backup' || ($decoded['version'] ?? null) !== 1 || ! is_array($decoded['tables'] ?? null)) {
            throw ValidationException::withMessages(['backup' => 'Tệp sao lưu không đúng định dạng của ứng dụng.']);
        }

        $tables = $decoded['tables'];
        foreach (self::TABLES as $table) {
            if (! isset($tables[$table]) || ! is_array($tables[$table])) {
                throw ValidationException::withMessages(['backup' => "Tệp sao lưu thiếu bảng {$table}."]);
            }
        }
        if (! collect($tables['users'])->contains(fn (mixed $user): bool => is_array($user) && ($user['role'] ?? null) === 'admin')) {
            throw ValidationException::withMessages(['backup' => 'Tệp sao lưu phải chứa ít nhất một tài khoản quản trị.']);
        }

        Schema::disableForeignKeyConstraints();
        try {
            DB::transaction(function () use ($tables): void {
                foreach (array_reverse(self::TABLES) as $table) {
                    DB::table($table)->delete();
                }
                foreach (self::TABLES as $table) {
                    foreach (array_chunk($tables[$table], 250) as $rows) {
                        if ($rows !== []) {
                            DB::table($table)->insert($rows);
                        }
                    }
                }
            });
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    /** @return list<array{path: string, name: string, size: int, modified: int}> */
    public function files(): array
    {
        return collect(Storage::disk('local')->files('backups'))
            ->filter(fn (string $path): bool => str_ends_with($path, '.json'))
            ->map(fn (string $path): array => [
                'path' => $path,
                'name' => basename($path),
                'size' => Storage::disk('local')->size($path),
                'modified' => Storage::disk('local')->lastModified($path),
            ])->sortByDesc('modified')->values()->all();
    }
}

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('nutrition:install {--fresh : Xóa bảng cũ trước khi tạo và nạp dữ liệu mẫu}', function (): int {
    $connection = config('database.connections.mysql');
    $database = (string) ($connection['database'] ?? '');
    if (! preg_match('/^[A-Za-z0-9_]+$/', $database)) {
        $this->error('Tên database trong DB_DATABASE không hợp lệ.');

        return Command::FAILURE;
    }

    try {
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $connection['host'], $connection['port']),
            (string) $connection['username'],
            (string) ($connection['password'] ?? ''),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
        );
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    } catch (Throwable $exception) {
        $this->error('Không thể kết nối MySQL để tạo database: '.$exception->getMessage());

        return Command::FAILURE;
    }

    $this->info("Database {$database} đã sẵn sàng.");
    $command = $this->option('fresh') ? 'migrate:fresh' : 'migrate';

    return $this->call($command, ['--seed' => true, '--force' => true]);
})->purpose('Tạo database MySQL, chạy migration và nạp dữ liệu mẫu cho ứng dụng');

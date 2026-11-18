<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->json('permissions');
            $table->timestamps();
        });
        Schema::table('users', fn (Blueprint $table) => $table->foreignId('permission_group_id')->nullable()->constrained()->nullOnDelete());
        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 20);
            $table->string('route_name')->nullable();
            $table->string('path');
            $table->string('ip_address', 45)->nullable();
            $table->unsignedSmallInteger('status_code');
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('permission_group_id'));
        Schema::dropIfExists('permission_groups');
    }
};

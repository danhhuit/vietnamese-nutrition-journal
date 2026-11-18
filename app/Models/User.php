<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'locale', 'dark_mode'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** @return HasOne<HealthProfile, $this> */
    public function healthProfile(): HasOne
    {
        return $this->hasOne(HealthProfile::class);
    }

    /** @return HasMany<DailyLog, $this> */
    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    /** @return HasMany<WaterLog, $this> */
    public function waterLogs(): HasMany
    {
        return $this->hasMany(WaterLog::class);
    }

    /** @return HasMany<WeightEntry, $this> */
    public function weightEntries(): HasMany
    {
        return $this->hasMany(WeightEntry::class);
    }

    /** @return HasMany<Favorite, $this> */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** @return BelongsTo<PermissionGroup, $this> */
    public function permissionGroup(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class);
    }

    public function canAccess(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->permission_group_id === null) {
            return false;
        }

        return in_array($permission, $this->permissionGroup()->first()->permissions, true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dark_mode' => 'boolean',
        ];
    }
}

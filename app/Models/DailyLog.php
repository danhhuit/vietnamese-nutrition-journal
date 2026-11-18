<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/** @property Carbon $logged_on */
class DailyLog extends Model
{
    protected $fillable = ['logged_on'];

    protected function casts(): array
    {
        return ['logged_on' => 'date'];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<FoodLogEntry, $this> */
    public function entries(): HasMany
    {
        return $this->hasMany(FoodLogEntry::class);
    }
}

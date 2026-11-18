<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'label'];

    public static function number(string $key, float $default): float
    {
        $value = static::query()->where('key', $key)->value('value');

        return $value === null ? $default : (float) $value;
    }

    /** @return array<string, float> */
    public static function numbers(): array
    {
        return static::query()->where('type', 'number')->pluck('value', 'key')
            ->map(fn (string $value): float => (float) $value)
            ->all();
    }
}

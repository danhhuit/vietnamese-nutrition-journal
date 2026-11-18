<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** @property array<int, string> $permissions */
class PermissionGroup extends Model
{
    protected $fillable = ['name', 'permissions'];

    protected function casts(): array
    {
        return ['permissions' => 'array'];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AllergyTag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class);
    }

    public function healthProfiles(): BelongsToMany
    {
        return $this->belongsToMany(HealthProfile::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodGroup extends Model
{
    protected $fillable = ['name_vi', 'name_en', 'slug'];

    public function foods(): HasMany
    {
        return $this->hasMany(Food::class);
    }
}

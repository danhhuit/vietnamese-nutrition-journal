<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServingUnit extends Model
{
    protected $fillable = ['name_vi', 'name_en', 'grams', 'applies_to'];
}

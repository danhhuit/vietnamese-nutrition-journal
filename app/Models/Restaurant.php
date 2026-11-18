<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'normalized_name', 'address', 'area', 'latitude', 'longitude', 'opening_hours', 'opens_at', 'closes_at', 'phone', 'price_level', 'image_url', 'data_updated_at'];

    protected function casts(): array
    {
        return ['data_updated_at' => 'date'];
    }

    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class)->withPivot(['price', 'is_available'])->withTimestamps();
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $term ? $query->where('normalized_name', 'like', '%'.Food::normalize($term).'%') : $query;
    }

    public function mapsUrl(): string
    {
        return 'https://www.google.com/maps/search/?api=1&query='.urlencode($this->latitude && $this->longitude ? "{$this->latitude},{$this->longitude}" : $this->address);
    }

    public function embedMapUrl(): ?string
    {
        if (! $this->latitude || ! $this->longitude) {
            return null;
        }

        $latitude = (float) $this->latitude;
        $longitude = (float) $this->longitude;
        $box = implode(',', [$longitude - .01, $latitude - .01, $longitude + .01, $latitude + .01]);

        return 'https://www.openstreetmap.org/export/embed.html?'.http_build_query([
            'bbox' => $box,
            'layer' => 'mapnik',
            'marker' => $latitude.','.$longitude,
        ]);
    }
}

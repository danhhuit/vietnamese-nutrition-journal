<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class GooglePlacesService
{
    private const ENDPOINT = 'https://places.googleapis.com/v1/places:searchText';

    private const FIELD_MASK = 'places.id,places.displayName,places.formattedAddress,places.googleMapsUri,nextPageToken';

    /**
     * Google Places content is returned for immediate display only. The
     * application deliberately does not persist names, addresses or URLs.
     *
     * @return array{places: list<array{id: string, name: string, address: string, maps_url: string}>, next_page_token: ?string}
     */
    public function search(string $query, ?string $pageToken = null): array
    {
        $key = (string) config('services.google_places.key');
        if ($key === '') {
            return ['places' => [], 'next_page_token' => null];
        }

        $payload = [
            'textQuery' => trim($query).' tại Thành phố Hồ Chí Minh',
            'languageCode' => app()->getLocale() === 'en' ? 'en' : 'vi',
            'regionCode' => 'VN',
            'pageSize' => 20,
        ];
        if ($pageToken) {
            $payload['pageToken'] = $pageToken;
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders([
                    'X-Goog-Api-Key' => $key,
                    'X-Goog-FieldMask' => self::FIELD_MASK,
                ])
                ->timeout(10)
                ->retry(2, 200)
                ->post(self::ENDPOINT, $payload);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Không thể kết nối Google Places. Vui lòng thử lại sau.', previous: $exception);
        }

        if (! $response->successful()) {
            report(new RuntimeException('Google Places response '.$response->status()));
            throw new RuntimeException('Google Places tạm thời không khả dụng.');
        }

        $places = collect($response->json('places', []))->map(static fn (array $place): array => [
            'id' => (string) ($place['id'] ?? ''),
            'name' => (string) data_get($place, 'displayName.text', ''),
            'address' => (string) ($place['formattedAddress'] ?? ''),
            'maps_url' => (string) ($place['googleMapsUri'] ?? ''),
        ])->filter(static fn (array $place): bool => $place['id'] !== '' && $place['name'] !== '')->values()->all();

        return [
            'places' => $places,
            'next_page_token' => $response->json('nextPageToken'),
        ];
    }
}

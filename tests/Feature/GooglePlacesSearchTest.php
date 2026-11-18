<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class GooglePlacesSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_explains_configuration_when_api_key_is_missing(): void
    {
        config(['services.google_places.key' => null]);

        $this->get(route('restaurants.google'))
            ->assertOk()
            ->assertSee('GOOGLE_PLACES_API_KEY');
    }

    public function test_it_searches_google_places_without_persisting_results(): void
    {
        config(['services.google_places.key' => 'test-key']);
        Http::fake([
            'places.googleapis.com/*' => Http::response([
                'places' => [[
                    'id' => 'ChIJ-test',
                    'displayName' => ['text' => 'Phở Việt Thật'],
                    'formattedAddress' => '123 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh',
                    'googleMapsUri' => 'https://maps.google.com/?cid=123',
                ]],
                'nextPageToken' => 'next-token',
            ]),
        ]);

        $this->get(route('restaurants.google', ['q' => 'phở']))
            ->assertOk()
            ->assertSee('Phở Việt Thật')
            ->assertSee('123 Nguyễn Huệ')
            ->assertSee('next-token', false);

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://places.googleapis.com/v1/places:searchText'
                && $request->hasHeader('X-Goog-Api-Key', 'test-key')
                && str_contains($request->header('X-Goog-FieldMask')[0] ?? '', 'places.id')
                && $request['textQuery'] === 'phở tại Thành phố Hồ Chí Minh';
        });

        $this->assertDatabaseCount('restaurants', 0);
    }
}

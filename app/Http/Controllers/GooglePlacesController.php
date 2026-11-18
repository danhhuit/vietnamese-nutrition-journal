<?php

namespace App\Http\Controllers;

use App\Services\GooglePlacesService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

final class GooglePlacesController extends Controller
{
    public function __invoke(Request $request, GooglePlacesService $places): View
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'page_token' => ['nullable', 'string', 'max:2048'],
        ]);
        $query = trim($data['q'] ?? 'quán ăn món Việt');
        $configured = filled(config('services.google_places.key'));
        $results = ['places' => [], 'next_page_token' => null];
        $error = null;

        if ($configured) {
            try {
                $results = $places->search($query, $data['page_token'] ?? null);
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        }

        return view('catalog.google-places', [
            'query' => $query,
            'configured' => $configured,
            'places' => $results['places'],
            'nextPageToken' => $results['next_page_token'],
            'error' => $error,
        ]);
    }
}

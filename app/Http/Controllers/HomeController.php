<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Food;
use App\Models\FoodGroup;
use App\Models\Restaurant;
use App\Models\SearchLog;
use App\Models\ServingUnit;
use App\Models\Setting;
use App\Services\NutritionCalculator;
use App\Services\NutritionSummary;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function index(Request $request, NutritionSummary $summary, RecommendationService $recommendations): View
    {
        if (! $request->user()) {
            return view('welcome');
        }
        $user = $request->user();
        $log = $user->dailyLogs()->with('entries')->whereDate('logged_on', today())->first()
            ?? $user->dailyLogs()->create(['logged_on' => today()]);
        $totals = $summary->totals($log);
        $target = (float) ($user->healthProfile?->daily_calorie_target ?: 2000);

        return view('dashboard', [
            'profile' => $user->healthProfile,
            'log' => $log,
            'totals' => $totals,
            'target' => $target,
            'warnings' => $summary->warnings($totals, $target),
            'recommendations' => $recommendations->forUser($user, max(0, $target - $totals['calories']), limit: 6),
        ]);
    }

    public function search(Request $request): View
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $query = trim($data['q'] ?? '');
        $foods = Food::search($query)->limit(12)->get();
        $dishes = Dish::search($query)->limit(12)->get();
        $restaurants = Restaurant::search($query)->limit(12)->get();
        if ($query !== '') {
            SearchLog::create(['user_id' => Auth::id(), 'query' => $query, 'results_count' => $foods->count() + $dishes->count() + $restaurants->count()]);
        }

        return view('catalog.search', compact('query', 'foods', 'dishes', 'restaurants'));
    }

    public function foods(Request $request): View
    {
        $query = Food::query()->with('foodGroup')->search($request->string('q')->toString());
        $query->when($request->filled('group'), fn ($q) => $q->where('food_group_id', $request->integer('group')))
            ->when($request->filled('origin'), fn ($q) => $q->where('origin', $request->string('origin')))
            ->when($request->input('energy') === '0-500', fn ($q) => $q->whereBetween('calories', [0, 500]))
            ->when($request->input('energy') === '500-1000', fn ($q) => $q->whereBetween('calories', [500, 1000]))
            ->when($request->input('energy') === '1000+', fn ($q) => $q->where('calories', '>', 1000));

        return view('catalog.foods', ['foods' => $query->orderBy('name')->paginate(15)->withQueryString(), 'groups' => FoodGroup::orderBy('name_vi')->get()]);
    }

    public function food(Food $food): View
    {
        $food->increment('viewed_count');

        return view('catalog.food', ['food' => $food->load('foodGroup'), 'units' => ServingUnit::whereIn('applies_to', ['food', 'both'])->get()]);
    }

    public function dishes(Request $request): View
    {
        $dishes = Dish::query()->search($request->string('q')->toString())
            ->when($request->boolean('vegetarian'), fn ($q) => $q->where('is_vegetarian', true))
            ->when($request->boolean('low_calorie'), fn ($q) => $q->where('is_low_calorie', true))
            ->when($request->boolean('high_protein'), fn ($q) => $q->where('is_high_protein', true))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('catalog.dishes', compact('dishes'));
    }

    public function dish(Request $request, Dish $dish): View
    {
        $dish->increment('viewed_count');
        $dish->load(['ingredients', 'restaurants', 'allergies']);
        $grams = max(1, $request->float('grams', (float) $dish->serving_grams));
        $userAllergies = $request->user()?->healthProfile?->allergies?->modelKeys() ?? [];

        return view('catalog.dish', ['dish' => $dish, 'grams' => $grams, 'nutrition' => $dish->scaled($grams), 'units' => ServingUnit::whereIn('applies_to', ['dish', 'both'])->get(), 'hasAllergy' => $dish->allergies->whereIn('id', $userAllergies)->isNotEmpty()]);
    }

    public function restaurants(Request $request): View
    {
        $restaurants = Restaurant::query()->search($request->string('q')->toString())
            ->when($request->filled('area'), fn ($q) => $q->where('area', $request->string('area')))
            ->when($request->filled('price_level'), fn ($q) => $q->where('price_level', $request->string('price_level')))
            ->when($request->boolean('open_now'), fn ($q) => $q->where('opens_at', '<=', now()->format('H:i:s'))->where('closes_at', '>=', now()->format('H:i:s')))
            ->withCount('dishes')->orderBy('name')->paginate(12)->withQueryString();

        return view('catalog.restaurants', ['restaurants' => $restaurants, 'areas' => Restaurant::distinct()->orderBy('area')->pluck('area')]);
    }

    public function restaurant(Restaurant $restaurant): View
    {
        return view('catalog.restaurant', ['restaurant' => $restaurant->load(['dishes' => fn ($q) => $q->wherePivot('is_available', true)])]);
    }

    public function compare(Request $request): View
    {
        $data = $request->validate(['type' => ['nullable', 'in:dish,food'], 'first' => ['nullable', 'integer'], 'second' => ['nullable', 'integer']]);
        $type = $data['type'] ?? 'dish';
        $class = $type === 'food' ? Food::class : Dish::class;
        $items = $class::orderBy('name')->get();
        $first = isset($data['first']) ? $class::find($data['first']) : null;
        $second = isset($data['second']) ? $class::find($data['second']) : null;

        return view('catalog.compare', compact('type', 'items', 'first', 'second'));
    }

    public function calculator(Request $request): View
    {
        $result = null;
        if ($request->isMethod('post')) {
            $data = $request->validate(['gender' => ['required', 'in:male,female'], 'weight' => ['required', 'numeric', 'between:30,300'], 'height' => ['required', 'numeric', 'between:100,250'], 'age' => ['required', 'integer', 'between:15,100'], 'activity' => ['required', 'in:sedentary,light,moderate,active,very_active'], 'goal' => ['required', 'in:maintain,lose,gain']]);
            $result = (new NutritionCalculator(Setting::numbers()))->calculate($data['gender'], $data['weight'], $data['height'], $data['age'], $data['activity'], $data['goal']);
        }

        return view('tools.calculator', compact('result'));
    }

    public function suggestions(Request $request, NutritionSummary $summary, RecommendationService $service): View
    {
        $user = $request->user();
        $log = $user->dailyLogs()->with('entries')->whereDate('logged_on', today())->first()
            ?? $user->dailyLogs()->create(['logged_on' => today()]);
        $totals = $summary->totals($log);
        $target = (float) ($user->healthProfile?->daily_calorie_target ?: 2000);
        $remaining = max(0, $target - $totals['calories']);

        return view('catalog.suggestions', ['dishes' => $service->forUser($user, $remaining, $request->string('goal')->toString() ?: null, 24), 'remaining' => $remaining]);
    }

    public function about(): View
    {
        return view('about');
    }
}

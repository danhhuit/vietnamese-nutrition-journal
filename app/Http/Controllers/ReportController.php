<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use App\Models\FoodLogEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('reports.index', $this->data($request));
    }

    public function pdf(Request $request): Response
    {
        $data = $this->data($request);

        return Pdf::loadView('reports.pdf', $data)->download('bao-cao-dinh-duong-'.$data['start']->format('Y-m-d').'.pdf');
    }

    private function data(Request $request): array
    {
        $start = $request->date('start') ?? now()->startOfWeek();
        $end = $request->date('end') ?? $start->copy()->endOfWeek();
        $logs = $request->user()->dailyLogs()->with('entries.dish', 'entries.restaurant')->whereBetween('logged_on', [$start, $end])->orderBy('logged_on')->get();
        $daily = $logs->map(fn (DailyLog $log) => ['date' => $log->logged_on->format('d/m'), 'calories' => round((float) $log->entries->sum('calories')), 'protein' => round((float) $log->entries->sum('protein_g'), 1), 'fat' => round((float) $log->entries->sum('fat_g'), 1), 'carb' => round((float) $log->entries->sum('carb_g'), 1)]);
        $target = (float) ($request->user()->healthProfile?->daily_calorie_target ?: 2000);
        $daysOnTarget = $daily->filter(fn (array $day): bool => $day['calories'] >= $target * .9 && $day['calories'] <= $target * 1.1)->count();
        $topDishes = FoodLogEntry::query()
            ->whereHas('dailyLog', fn ($query) => $query->where('user_id', $request->user()->id)->whereBetween('logged_on', [$start, $end]))
            ->whereNotNull('dish_id')
            ->select('item_name')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('dish_id', 'item_name')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('item_name');
        $topRestaurants = FoodLogEntry::query()
            ->join('daily_logs', 'food_log_entries.daily_log_id', '=', 'daily_logs.id')
            ->join('restaurants', 'food_log_entries.restaurant_id', '=', 'restaurants.id')
            ->where('daily_logs.user_id', $request->user()->id)
            ->whereBetween('daily_logs.logged_on', [$start, $end])
            ->select('restaurants.name')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('restaurants.id', 'restaurants.name')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('restaurants.name');
        $macroTotals = [
            'protein' => round((float) $daily->sum('protein'), 1),
            'fat' => round((float) $daily->sum('fat'), 1),
            'carb' => round((float) $daily->sum('carb'), 1),
        ];
        $weights = $request->user()->weightEntries()->whereBetween('recorded_on', [$start, $end])->orderBy('recorded_on')->get();

        return compact('start', 'end', 'daily', 'target', 'daysOnTarget', 'topDishes', 'topRestaurants', 'macroTotals', 'weights');
    }
}

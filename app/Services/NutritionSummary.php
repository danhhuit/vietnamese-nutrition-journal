<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\Setting;

final class NutritionSummary
{
    public function totals(?DailyLog $log): array
    {
        if ($log === null) {
            return array_fill_keys(['calories', 'protein_g', 'fat_g', 'carb_g', 'sodium_mg', 'sugar_g'], 0.0);
        }

        $entries = $log->entries;

        return collect(['calories', 'protein_g', 'fat_g', 'carb_g', 'sodium_mg', 'sugar_g'])
            ->mapWithKeys(fn (string $key): array => [$key => round((float) $entries->sum($key), 1)])
            ->all();
    }

    public function warnings(array $totals, float $calorieTarget): array
    {
        $warnings = [];
        $warningRatio = Setting::number('calorie_warning_ratio', .9);
        $sodiumLimit = Setting::number('sodium_limit_mg', 2000);
        $fatRatioLimit = Setting::number('fat_energy_ratio_limit', .35);
        $ratio = $calorieTarget > 0 ? $totals['calories'] / $calorieTarget : 0;
        if ($ratio > 1) {
            $warnings[] = ['level' => 'danger', 'message' => 'Bạn đã vượt hạn mức năng lượng hôm nay.'];
        } elseif ($ratio >= $warningRatio) {
            $warnings[] = ['level' => 'warning', 'message' => 'Bạn đã gần chạm hạn mức năng lượng hôm nay.'];
        } elseif ($ratio < .5 && now()->hour >= 18) {
            $warnings[] = ['level' => 'info', 'message' => 'Năng lượng nạp hôm nay đang thấp hơn khuyến nghị.'];
        }
        if ($totals['sodium_mg'] > $sodiumLimit) {
            $warnings[] = ['level' => 'danger', 'message' => 'Natri đã vượt ngưỡng '.number_format($sodiumLimit).' mg/ngày.'];
        }
        if ($calorieTarget * .1 < $totals['sugar_g'] * 4) {
            $warnings[] = ['level' => 'warning', 'message' => 'Đường đã vượt 10% năng lượng khuyến nghị.'];
        }
        if ($totals['calories'] > 0 && $fatRatioLimit < $totals['fat_g'] * 9 / $totals['calories']) {
            $warnings[] = ['level' => 'warning', 'message' => 'Tỉ lệ năng lượng từ chất béo đang cao hơn khuyến nghị.'];
        }

        return $warnings;
    }
}

<?php

namespace App\Services;

final class NutritionCalculator
{
    private const ACTIVITY = ['sedentary' => 1.2, 'light' => 1.375, 'moderate' => 1.55, 'active' => 1.725, 'very_active' => 1.9];

    private const MACROS = ['maintain' => [.20, .30, .50], 'lose' => [.30, .25, .45], 'gain' => [.30, .25, .45]];

    public function __construct(private readonly array $settings = []) {}

    public function calculate(string $gender, float $weightKg, float $heightCm, int $age, string $activity, string $goal): array
    {
        $bmr = 10 * $weightKg + 6.25 * $heightCm - 5 * $age + ($gender === 'male' ? 5 : -161);
        $bmr = round($bmr, 1);
        $factor = (float) ($this->settings['activity_'.$activity] ?? $this->settings[$activity] ?? self::ACTIVITY[$activity] ?? self::ACTIVITY['moderate']);
        $tdee = round($bmr * $factor, 1);
        $adjustment = match ($goal) {
            'lose' => (float) ($this->settings['calorie_adjustment_lose'] ?? -500),
            'gain' => (float) ($this->settings['calorie_adjustment_gain'] ?? 300),
            default => 0,
        };
        $calories = round(max((float) ($this->settings['calorie_floor'] ?? 1200), $tdee + $adjustment), 1);
        [$defaultProtein, $defaultFat, $defaultCarb] = self::MACROS[$goal] ?? self::MACROS['maintain'];
        $protein = $this->macroRatio($goal, 'protein', $defaultProtein);
        $fat = $this->macroRatio($goal, 'fat', $defaultFat);
        $carb = $this->macroRatio($goal, 'carb', $defaultCarb);
        $macroTotal = $protein + $fat + $carb;
        if ($macroTotal <= 0) {
            [$protein, $fat, $carb] = [$defaultProtein, $defaultFat, $defaultCarb];
            $macroTotal = 1;
        }
        [$protein, $fat, $carb] = [$protein / $macroTotal, $fat / $macroTotal, $carb / $macroTotal];
        $bmi = round($weightKg / (($heightCm / 100) ** 2), 1);

        return [
            'bmi' => $bmi,
            'bmi_classification' => $this->classifyBmi($bmi),
            'bmr' => $bmr,
            'tdee' => $tdee,
            'daily_calories' => $calories,
            'protein_g' => round($calories * $protein / 4, 1),
            'fat_g' => round($calories * $fat / 9, 1),
            'carb_g' => round($calories * $carb / 4, 1),
        ];
    }

    public function classifyBmi(float $bmi): string
    {
        return match (true) {
            $bmi < 18.5 => 'Thiếu cân',
            $bmi < 23 => 'Bình thường',
            $bmi < 25 => 'Thừa cân',
            default => 'Béo phì',
        };
    }

    private function macroRatio(string $goal, string $macro, float $default): float
    {
        $value = (float) ($this->settings["macro_{$goal}_{$macro}"] ?? $default);

        return $value > 1 ? $value / 100 : $value;
    }
}

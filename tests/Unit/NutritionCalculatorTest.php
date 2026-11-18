<?php

namespace Tests\Unit;

use App\Services\NutritionCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class NutritionCalculatorTest extends TestCase
{
    #[DataProvider('metricCases')]
    public function test_it_calculates_bmi_bmr_tdee_and_daily_targets(
        string $gender,
        float $weight,
        float $height,
        int $age,
        string $activity,
        string $goal,
        float $expectedBmr,
        float $expectedTdee,
        float $expectedCalories,
    ): void {
        $calculator = new NutritionCalculator;

        $result = $calculator->calculate($gender, $weight, $height, $age, $activity, $goal);

        self::assertSame(round($weight / (($height / 100) ** 2), 1), $result['bmi']);
        self::assertSame($expectedBmr, $result['bmr']);
        self::assertSame($expectedTdee, $result['tdee']);
        self::assertSame($expectedCalories, $result['daily_calories']);
        self::assertEqualsWithDelta($expectedCalories, $result['protein_g'] * 4 + $result['fat_g'] * 9 + $result['carb_g'] * 4, 2.0);
    }

    public static function metricCases(): array
    {
        return [
            'male maintain' => ['male', 70, 175, 25, 'moderate', 'maintain', 1673.8, 2594.4, 2594.4],
            'female lose' => ['female', 60, 160, 30, 'light', 'lose', 1289.0, 1772.4, 1272.4],
            'calorie floor' => ['female', 45, 150, 65, 'sedentary', 'lose', 901.5, 1081.8, 1200.0],
        ];
    }

    public function test_it_classifies_asian_pacific_bmi_boundaries(): void
    {
        $calculator = new NutritionCalculator;

        self::assertSame('Thiếu cân', $calculator->classifyBmi(18.4));
        self::assertSame('Bình thường', $calculator->classifyBmi(18.5));
        self::assertSame('Thừa cân', $calculator->classifyBmi(23.0));
        self::assertSame('Béo phì', $calculator->classifyBmi(25.0));
    }

    public function test_admin_formula_overrides_are_applied_and_macro_ratios_are_normalized(): void
    {
        $calculator = new NutritionCalculator([
            'activity_moderate' => 1.6,
            'calorie_adjustment_lose' => -400,
            'calorie_floor' => 1000,
            'macro_lose_protein' => 40,
            'macro_lose_fat' => 20,
            'macro_lose_carb' => 40,
        ]);

        $result = $calculator->calculate('male', 70, 175, 25, 'moderate', 'lose');

        self::assertSame(2278.1, $result['daily_calories']);
        self::assertEqualsWithDelta(2278.1, $result['protein_g'] * 4 + $result['fat_g'] * 9 + $result['carb_g'] * 4, 2.0);
        self::assertGreaterThan($result['fat_g'], $result['protein_g']);
    }
}

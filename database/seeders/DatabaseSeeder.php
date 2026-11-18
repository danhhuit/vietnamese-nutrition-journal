<?php

namespace Database\Seeders;

use App\Models\AllergyTag;
use App\Models\Dish;
use App\Models\Food;
use App\Models\FoodGroup;
use App\Models\HealthProfile;
use App\Models\Restaurant;
use App\Models\ServingUnit;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $admin = User::factory()->admin()->create(['name' => 'Quản trị viên', 'email' => 'admin@dinhduong.test', 'password' => 'Admin@123']);
            $user = User::factory()->create(['name' => 'Nguyễn Minh', 'email' => 'user@dinhduong.test', 'password' => 'User@123']);
            $profile = HealthProfile::create(['user_id' => $user->id, 'birth_date' => '2000-05-15', 'gender' => 'male', 'height_cm' => 172, 'weight_kg' => 68, 'activity_level' => 'moderate', 'goal' => 'maintain', 'target_weight_kg' => 65, 'target_date' => now()->addMonths(3), 'daily_calorie_target' => 2400, 'protein_target_g' => 120, 'fat_target_g' => 80, 'carb_target_g' => 300, 'water_target_ml' => 2200, 'reminders' => ['meals' => true, 'water' => true]]);

            $groups = collect(['Nước giải khát', 'Ngũ cốc', 'Khoai củ', 'Hạt, quả giàu đạm béo', 'Quả chín', 'Rau, quả, củ dùng làm rau', 'Dầu, mỡ, bơ', 'Thịt', 'Thức ăn truyền thống', 'Thủy sản', 'Trứng', 'Sữa', 'Đồ hộp', 'Đồ ngọt', 'Gia vị, nước chấm'])->mapWithKeys(function (string $name): array {
                $group = FoodGroup::create(['name_vi' => $name, 'name_en' => Str::headline(Str::ascii($name)), 'slug' => Str::slug($name)]);

                return [$name => $group];
            });
            $allergies = collect(['Đậu phộng', 'Hải sản', 'Gluten', 'Sữa', 'Trứng', 'Đậu nành'])->mapWithKeys(function (string $name): array {
                $tag = AllergyTag::create(['name' => $name, 'slug' => Str::slug($name)]);

                return [$name => $tag];
            });
            $profile->allergies()->attach($allergies['Đậu phộng']);
            foreach ([['Muỗng', 15], ['Chén', 150], ['Bát', 250], ['Tô nhỏ', 300], ['Tô lớn', 450], ['Lát', 30], ['Ly', 240], ['Phần', 350]] as [$name,$grams]) {
                ServingUnit::create(['name_vi' => $name, 'name_en' => Str::ascii($name), 'grams' => $grams, 'applies_to' => 'both']);
            }

            $foods = [
                ['Gạo tẻ', 'Ngũ cốc', 344, 7.5, .6, 76.2, 2], ['Gạo lứt', 'Ngũ cốc', 350, 7.9, 2.9, 72.8, 5], ['Bún tươi', 'Ngũ cốc', 110, 1.7, .4, 25.7, 20], ['Phở tươi', 'Ngũ cốc', 143, 3.2, .5, 31.8, 15], ['Bánh mì', 'Ngũ cốc', 265, 9, 3.2, 49, 491], ['Mì trứng', 'Ngũ cốc', 138, 4.5, 2.1, 25, 350],
                ['Khoai lang', 'Khoai củ', 119, 1.2, .3, 28.5, 40], ['Khoai tây', 'Khoai củ', 87, 1.9, .1, 20.1, 6], ['Đậu phộng', 'Hạt, quả giàu đạm béo', 567, 25.8, 49.2, 16.1, 18], ['Hạt sen', 'Hạt, quả giàu đạm béo', 350, 17, 2, 63, 5],
                ['Chuối', 'Quả chín', 89, 1.1, .3, 22.8, 1], ['Cam', 'Quả chín', 47, .9, .1, 11.8, 0], ['Xoài', 'Quả chín', 60, .8, .4, 15, 1], ['Thanh long', 'Quả chín', 50, 1.1, .4, 11, 2],
                ['Rau muống', 'Rau, quả, củ dùng làm rau', 19, 2.6, .2, 3.1, 113], ['Giá đỗ', 'Rau, quả, củ dùng làm rau', 31, 3, .2, 6, 6], ['Cà chua', 'Rau, quả, củ dùng làm rau', 18, .9, .2, 3.9, 5], ['Dưa leo', 'Rau, quả, củ dùng làm rau', 15, .7, .1, 3.6, 2], ['Cà rốt', 'Rau, quả, củ dùng làm rau', 41, .9, .2, 9.6, 69], ['Cải xanh', 'Rau, quả, củ dùng làm rau', 27, 2.9, .4, 4.7, 20], ['Hành tây', 'Rau, quả, củ dùng làm rau', 40, 1.1, .1, 9.3, 4], ['Hành lá', 'Rau, quả, củ dùng làm rau', 32, 1.8, .2, 7.3, 16], ['Ngò rí', 'Rau, quả, củ dùng làm rau', 23, 2.1, .5, 3.7, 46], ['Tỏi', 'Rau, quả, củ dùng làm rau', 149, 6.4, .5, 33.1, 17],
                ['Dầu đậu nành', 'Dầu, mỡ, bơ', 884, 0, 100, 0, 0], ['Mỡ heo', 'Dầu, mỡ, bơ', 902, 0, 100, 0, 0],
                ['Thịt bò nạc', 'Thịt', 250, 26, 15, 0, 72], ['Thịt heo nạc', 'Thịt', 242, 27, 14, 0, 62], ['Sườn heo', 'Thịt', 277, 18, 23, 0, 80], ['Ức gà', 'Thịt', 165, 31, 3.6, 0, 74], ['Chả lụa', 'Thức ăn truyền thống', 230, 21, 14, 4, 800], ['Đậu hũ', 'Thức ăn truyền thống', 76, 8, 4.8, 1.9, 7],
                ['Cá lóc', 'Thủy sản', 97, 18.2, 2.7, 0, 60], ['Tôm sú', 'Thủy sản', 99, 24, .3, .2, 111], ['Cua biển', 'Thủy sản', 97, 19, 1.5, 0, 107], ['Trứng gà', 'Trứng', 155, 13, 11, 1.1, 124], ['Sữa tươi', 'Sữa', 61, 3.2, 3.3, 4.8, 43],
                ['Nước dừa', 'Nước giải khát', 19, .7, .2, 3.7, 105], ['Cà phê đen', 'Nước giải khát', 2, .3, 0, 0, 5], ['Đường cát', 'Đồ ngọt', 387, 0, 0, 100, 1], ['Nước mắm', 'Gia vị, nước chấm', 35, 5, 0, 3.6, 7800], ['Nước tương', 'Gia vị, nước chấm', 53, 8.1, .6, 4.9, 5493], ['Muối', 'Gia vị, nước chấm', 0, 0, 0, 0, 38758],
            ];
            foreach ($foods as [$name,$group,$cal,$protein,$fat,$carb,$sodium]) {
                Food::create(['food_group_id' => $groups[$group]->id, 'name' => $name, 'normalized_name' => Food::normalize($name), 'origin' => in_array($group, ['Thịt', 'Thủy sản', 'Trứng', 'Sữa']) ? 'animal' : 'plant', 'source' => 'Dữ liệu tổng hợp tham khảo cho mục đích học tập', 'calories' => $cal, 'protein_g' => $protein, 'fat_g' => $fat, 'carb_g' => $carb, 'fiber_g' => str_contains($group, 'Rau') || str_contains($group, 'Quả') ? 2.5 : 0, 'calcium_mg' => 20, 'iron_mg' => 1.2, 'sodium_mg' => $sodium, 'potassium_mg' => 180, 'vitamin_a_mcg' => 20, 'vitamin_c_mg' => str_contains($group, 'Rau') || str_contains($group, 'Quả') ? 20 : 0]);
            }

            $dishRows = [
                ['Phở bò', 450, 30, 12, 58, 1300, false, false, true, false], ['Bún bò Huế', 534, 28, 20, 62, 1600, false, false, true, false], ['Cơm tấm sườn', 620, 29, 22, 78, 1100, false, false, true, false], ['Bánh mì thịt', 430, 20, 17, 51, 1200, false, false, false, false], ['Gỏi cuốn tôm thịt', 280, 18, 7, 35, 620, true, true, false, false], ['Bún chả', 550, 26, 18, 70, 1250, false, false, true, false], ['Hủ tiếu Nam Vang', 480, 24, 14, 65, 1400, false, false, false, false], ['Mì Quảng', 520, 25, 16, 68, 1350, false, false, true, false], ['Cao lầu', 490, 23, 15, 66, 1200, false, false, false, false], ['Bánh xèo', 580, 20, 28, 60, 900, false, true, false, false],
                ['Bún riêu', 430, 22, 14, 55, 1450, true, false, false, false], ['Bánh cuốn', 360, 17, 10, 50, 850, true, true, false, false], ['Cháo gà', 320, 20, 8, 43, 700, true, true, true, false], ['Cơm gà Hội An', 590, 32, 18, 73, 1050, false, false, true, false], ['Cá kho tộ', 390, 30, 18, 22, 1350, true, false, true, false], ['Canh chua cá', 210, 20, 5, 22, 800, true, true, true, false], ['Thịt kho trứng', 510, 29, 35, 18, 1100, false, false, true, false], ['Bún thịt nướng', 500, 25, 17, 64, 1050, false, false, false, false], ['Bò kho', 460, 31, 19, 41, 1250, true, false, true, false], ['Bún mắm', 550, 29, 18, 72, 1900, false, false, true, false],
                ['Bánh canh cua', 490, 24, 13, 69, 1500, false, false, false, false], ['Xôi gà', 610, 28, 18, 83, 900, false, false, true, false], ['Bún đậu mắm tôm', 570, 25, 22, 67, 1800, false, false, false, false], ['Cơm chiên Dương Châu', 650, 24, 25, 82, 1150, false, false, false, false], ['Gỏi gà', 320, 29, 12, 23, 720, true, true, true, false], ['Đậu hũ sốt cà', 280, 16, 12, 28, 520, true, true, false, true], ['Cơm gạo lứt ức gà', 430, 38, 8, 51, 480, true, true, true, false], ['Salad rau củ', 180, 6, 7, 24, 220, true, true, false, true], ['Chè đậu xanh', 310, 8, 6, 58, 80, true, true, false, true], ['Sữa chua trái cây', 240, 8, 6, 42, 95, true, true, false, true],
            ];
            $foodModels = Food::all();
            $dishes = collect();
            foreach ($dishRows as $i => $row) {
                [$name,$cal,$protein,$fat,$carb,$sodium,$lowCal,$lowSodium,$highProtein,$vegetarian] = $row;
                $dish = Dish::create(['name' => $name, 'normalized_name' => Food::normalize($name), 'description' => 'Món Việt quen thuộc, số liệu dinh dưỡng ước tính theo một khẩu phần chuẩn.', 'serving_grams' => 300 + ($i % 4) * 50, 'calories' => $cal, 'protein_g' => $protein, 'fat_g' => $fat, 'carb_g' => $carb, 'fiber_g' => $vegetarian ? 5 : 2, 'sodium_mg' => $sodium, 'sugar_g' => 4 + ($i % 8), 'is_low_calorie' => $lowCal, 'is_low_sodium' => $lowSodium, 'is_high_protein' => $highProtein, 'is_vegetarian' => $vegetarian, 'data_source' => 'Dữ liệu tổng hợp minh họa; xem trang Giới thiệu']);
                $ingredientIds = $foodModels->slice(($i * 3) % max(1, $foodModels->count() - 4), 4)->pluck('id');
                $dish->ingredients()->sync($ingredientIds->mapWithKeys(fn ($id, $k) => [$id => ['grams' => 50 + $k * 25]])->all());
                if (str_contains($name, 'Tôm') || str_contains($name, 'Cua') || str_contains($name, 'mắm')) {
                    $dish->allergies()->attach($allergies['Hải sản']);
                } if (str_contains($name, 'Bánh mì') || str_contains($name, 'Mì')) {
                    $dish->allergies()->attach($allergies['Gluten']);
                } if (str_contains($name, 'Trứng') || str_contains($name, 'Dương Châu')) {
                    $dish->allergies()->attach($allergies['Trứng']);
                } if (str_contains($name, 'Sữa')) {
                    $dish->allergies()->attach($allergies['Sữa']);
                } if (str_contains($name, 'Đậu')) {
                    $dish->allergies()->attach($allergies['Đậu nành']);
                } $dishes->push($dish);
            }

            $restaurantRows = [['Phở Sài Gòn', 'Quận 1'], ['Bếp Việt Xưa', 'Quận 3'], ['Cơm Tấm Ba Ghiền', 'Quận Phú Nhuận'], ['Bún Bò Cố Đô', 'Quận 10'], ['Gánh Quê', 'Quận Bình Thạnh'], ['Món Ngon Hà Nội', 'Quận 1'], ['Bếp Nhà An', 'Quận 7'], ['Quán Chay Lá Bồ Đề', 'Quận 5'], ['Hương Việt', 'Quận Tân Bình'], ['Cơm Gà Hội An', 'Quận 3'], ['Bún Chả 36', 'Quận 1'], ['Gỏi Cuốn Cô Ba', 'Quận 4'], ['Hủ Tiếu Thành Đạt', 'Quận 11'], ['Mì Quảng Miền Trung', 'Quận Gò Vấp'], ['Bánh Xèo Miền Tây', 'Quận 6'], ['Dinh Dưỡng Xanh', 'Thành phố Thủ Đức']];
            foreach ($restaurantRows as $i => [$name,$area]) {
                $restaurant = Restaurant::create(['name' => $name, 'normalized_name' => Food::normalize($name), 'address' => (100 + $i).' Đường Ẩm Thực, '.$area.', TP.HCM', 'area' => $area, 'latitude' => 10.76 + $i * .003, 'longitude' => 106.66 + $i * .004, 'opening_hours' => '06:00 - 22:00', 'opens_at' => '06:00', 'closes_at' => '22:00', 'phone' => '090'.str_pad((string) $i, 7, '0', STR_PAD_LEFT), 'price_level' => ['budget', 'medium', 'premium'][$i % 3], 'data_updated_at' => today()]);
                $menu = $dishes->slice(($i * 2) % 20, 8)->mapWithKeys(fn ($dish, $k) => [$dish->id => ['price' => 30000 + $k * 5000 + $i * 1000, 'is_available' => true]])->all();
                $restaurant->dishes()->sync($menu);
            }

            foreach ([
                'activity_sedentary' => ['Hệ số vận động: ít', 1.2], 'activity_light' => ['Hệ số vận động: nhẹ', 1.375], 'activity_moderate' => ['Hệ số vận động: vừa', 1.55], 'activity_active' => ['Hệ số vận động: nhiều', 1.725], 'activity_very_active' => ['Hệ số vận động: rất nhiều', 1.9],
                'calorie_floor' => ['Ngưỡng calo tối thiểu', 1200], 'calorie_adjustment_lose' => ['Điều chỉnh calo giảm cân', -500], 'calorie_adjustment_gain' => ['Điều chỉnh calo tăng cơ', 300],
                'macro_maintain_protein' => ['Duy trì: % Protein', 20], 'macro_maintain_fat' => ['Duy trì: % Fat', 30], 'macro_maintain_carb' => ['Duy trì: % Carb', 50],
                'macro_lose_protein' => ['Giảm cân: % Protein', 30], 'macro_lose_fat' => ['Giảm cân: % Fat', 25], 'macro_lose_carb' => ['Giảm cân: % Carb', 45],
                'macro_gain_protein' => ['Tăng cơ: % Protein', 30], 'macro_gain_fat' => ['Tăng cơ: % Fat', 25], 'macro_gain_carb' => ['Tăng cơ: % Carb', 45],
                'sodium_limit_mg' => ['Ngưỡng natri (mg)', 2000], 'calorie_warning_ratio' => ['Tỉ lệ cảnh báo calo', .9], 'fat_energy_ratio_limit' => ['Tỉ lệ năng lượng từ chất béo tối đa', .35],
            ] as $key => [$label,$value]) {
                Setting::create(['key' => $key, 'label' => $label, 'value' => (string) $value, 'type' => 'number']);
            }

            $todayLog = $user->dailyLogs()->create(['logged_on' => today()]);
            foreach ($dishes->take(2) as $i => $dish) {
                $todayLog->entries()->create(['dish_id' => $dish->id, 'meal_type' => $i ? 'lunch' : 'breakfast', 'item_name' => $dish->name, 'quantity' => 1, 'serving_grams' => $dish->serving_grams, 'calories' => $dish->calories, 'protein_g' => $dish->protein_g, 'fat_g' => $dish->fat_g, 'carb_g' => $dish->carb_g, 'sodium_mg' => $dish->sodium_mg, 'sugar_g' => $dish->sugar_g, 'source' => 'database']);
            }
            $user->waterLogs()->create(['logged_on' => today(), 'amount_ml' => 500]);
            $user->weightEntries()->create(['recorded_on' => today(), 'weight_kg' => 68]);
        });
    }
}

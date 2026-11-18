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
                ['Gạo nếp cái', 'Ngũ cốc', 348, 8.6, 1.5, 75.1, 3], ['Kê', 'Ngũ cốc', 345, 7, 3, 72.4, 7], ['Ngô bắp tươi', 'Ngũ cốc', 200, 4.1, 2.3, 40.8, 3], ['Bánh đa nem', 'Ngũ cốc', 335, 4, .2, 79.4, 0], ['Bánh đúc', 'Ngũ cốc', 52, .9, .3, 11.4, 0], ['Củ đậu', 'Khoai củ', 31, 1, 0, 6.7, 0], ['Sữa dê tươi', 'Sữa', 69, 3.5, 4.1, 4.5, 50], ['Nước cam tươi', 'Nước giải khát', 23, .7, .2, 4.7, 1],
                ['Khoai lang', 'Khoai củ', 119, 1.2, .3, 28.5, 40], ['Khoai tây', 'Khoai củ', 87, 1.9, .1, 20.1, 6], ['Đậu phộng', 'Hạt, quả giàu đạm béo', 567, 25.8, 49.2, 16.1, 18], ['Hạt sen', 'Hạt, quả giàu đạm béo', 350, 17, 2, 63, 5],
                ['Chuối', 'Quả chín', 89, 1.1, .3, 22.8, 1], ['Cam', 'Quả chín', 47, .9, .1, 11.8, 0], ['Xoài', 'Quả chín', 60, .8, .4, 15, 1], ['Thanh long', 'Quả chín', 50, 1.1, .4, 11, 2],
                ['Rau muống', 'Rau, quả, củ dùng làm rau', 19, 2.6, .2, 3.1, 113], ['Giá đỗ', 'Rau, quả, củ dùng làm rau', 31, 3, .2, 6, 6], ['Cà chua', 'Rau, quả, củ dùng làm rau', 18, .9, .2, 3.9, 5], ['Dưa leo', 'Rau, quả, củ dùng làm rau', 15, .7, .1, 3.6, 2], ['Cà rốt', 'Rau, quả, củ dùng làm rau', 41, .9, .2, 9.6, 69], ['Cải xanh', 'Rau, quả, củ dùng làm rau', 27, 2.9, .4, 4.7, 20], ['Hành tây', 'Rau, quả, củ dùng làm rau', 40, 1.1, .1, 9.3, 4], ['Hành lá', 'Rau, quả, củ dùng làm rau', 32, 1.8, .2, 7.3, 16], ['Ngò rí', 'Rau, quả, củ dùng làm rau', 23, 2.1, .5, 3.7, 46], ['Tỏi', 'Rau, quả, củ dùng làm rau', 149, 6.4, .5, 33.1, 17],
                ['Dầu đậu nành', 'Dầu, mỡ, bơ', 884, 0, 100, 0, 0], ['Mỡ heo', 'Dầu, mỡ, bơ', 902, 0, 100, 0, 0],
                ['Thịt bò nạc', 'Thịt', 250, 26, 15, 0, 72], ['Thịt heo nạc', 'Thịt', 242, 27, 14, 0, 62], ['Sườn heo', 'Thịt', 277, 18, 23, 0, 80], ['Ức gà', 'Thịt', 165, 31, 3.6, 0, 74], ['Chả lụa', 'Thức ăn truyền thống', 230, 21, 14, 4, 800], ['Đậu hũ', 'Thức ăn truyền thống', 76, 8, 4.8, 1.9, 7],
                ['Cá lóc', 'Thủy sản', 97, 18.2, 2.7, 0, 60], ['Tôm sú', 'Thủy sản', 99, 24, .3, .2, 111], ['Cua biển', 'Thủy sản', 97, 19, 1.5, 0, 107], ['Trứng gà', 'Trứng', 155, 13, 11, 1.1, 124], ['Sữa tươi', 'Sữa', 61, 3.2, 3.3, 4.8, 43],
                ['Nước dừa', 'Nước giải khát', 19, .7, .2, 3.7, 105], ['Cà phê đen', 'Nước giải khát', 2, .3, 0, 0, 5], ['Đường cát', 'Đồ ngọt', 387, 0, 0, 100, 1], ['Nước mắm', 'Gia vị, nước chấm', 35, 5, 0, 3.6, 7800], ['Nước tương', 'Gia vị, nước chấm', 53, 8.1, .6, 4.9, 5493], ['Muối', 'Gia vị, nước chấm', 0, 0, 0, 0, 38758],
            ];
            // Bảng thành phần thực phẩm Việt Nam 2017, tính trên 100 g phần ăn được.
            // Mã thực phẩm và trang PDF giúp đối chiếu từng dòng; các dòng khác là ước tính minh họa.
            $verifiedFoods = [
                'Gạo tẻ' => ['01004', 24, 346, 7.9, 1, 76.3, .4, 30, 1.3, 5, 241, null, 0],
                'Gạo lứt' => ['01005', 24, 359, 7.5, 2.68, 76.18, 3.4, 16, 2.8, 5, 202, null, 0],
                'Bánh mì' => ['01012', 24, 250, 7.9, .8, 52.8, .2, 28, 2, 0, 0, null, 0],
                'Gạo nếp cái' => ['01001', 24, 348, 8.6, 1.5, 75.1, .6, 32, 1.2, 3, 282, null, 0],
                'Kê' => ['01006', 24, 345, 7, 3, 72.4, 3.4, 22, 2.7, 7, 249, null, 0],
                'Ngô bắp tươi' => ['01007', 24, 200, 4.1, 2.3, 40.8, 1.2, 20, 1.5, 3, 306, null, 12],
                'Bánh đa nem' => ['01010', 24, 335, 4, .2, 79.4, .5, 20, .3, 0, 0, null, 0],
                'Bánh đúc' => ['01011', 24, 52, .9, .3, 11.4, .1, 50, .4, 0, 0, null, 0],
                'Củ đậu' => ['04023', 47, 31, 1, 0, 6.7, .7, 8, 0, 0, 0, null, 0],
                'Sữa dê tươi' => ['10002', 111, 69, 3.5, 4.1, 4.5, 0, 147, .1, 50, 204, null, 3],
                'Nước cam tươi' => ['14005', 133, 23, .7, .2, 4.7, 0, 26, .3, 1, 200, null, 54],
                'Khoai lang' => ['02008', 31, 124, .8, .2, 29.8, 1.3, 34, 1, 31, 210, null, 23],
                'Cà chua' => ['04005', 45, 24, .6, .23, 4.77, .8, 12, 1.4, 12, 275, null, 40],
                'Rau muống' => ['04083', 55, 28, 3.2, .24, 3.26, 1, 100, 1.4, 37, 331, null, 20],
                'Cam' => ['05002', 65, 43, .9, .06, 9.74, 1.4, 34, .4, 4, 108, null, 40],
                'Thịt bò nạc' => ['07003', 79, 118, 21, 3.8, 0, 0, 12, 3.1, 83, 378, null, 1],
                'Thịt heo nạc' => ['07017', 81, 139, 19, 7, 0, 0, 7, .96, 76, 341, null, .8],
                'Sữa tươi' => ['10001', 111, 74, 3.9, 4.4, 4.8, 0, 120, .1, 380, 143, null, 1],
            ];
            foreach ($foods as [$name,$group,$cal,$protein,$fat,$carb,$sodium]) {
                $verified = $verifiedFoods[$name] ?? null;
                $fiber = $calcium = $iron = $potassium = $vitaminC = 0;
                if ($verified) {
                    [$code, $page, $cal, $protein, $fat, $carb, $fiber, $calcium, $iron, $sodium, $potassium, , $vitaminC] = $verified;
                }
                Food::create(['food_group_id' => $groups[$group]->id, 'name' => $name, 'normalized_name' => Food::normalize($name), 'origin' => in_array($group, ['Thịt', 'Thủy sản', 'Trứng', 'Sữa']) ? 'animal' : 'plant', 'source' => $verified ? "Bảng thành phần thực phẩm Việt Nam 2017, mã {$code}, trang PDF {$page}" : 'Ước tính minh họa, chưa đối chiếu nguồn', 'calories' => $cal, 'protein_g' => $protein, 'fat_g' => $fat, 'carb_g' => $carb, 'fiber_g' => $fiber, 'calcium_mg' => $calcium, 'iron_mg' => $iron, 'sodium_mg' => $sodium, 'potassium_mg' => $potassium, 'vitamin_a_mcg' => 0, 'vitamin_c_mg' => $vitaminC]);
            }

            $dishRows = [
                ['Phở bò', 450, 30, 12, 58, 1300, false, false, true, false], ['Bún bò Huế', 534, 28, 20, 62, 1600, false, false, true, false], ['Cơm tấm sườn', 620, 29, 22, 78, 1100, false, false, true, false], ['Bánh mì thịt', 430, 20, 17, 51, 1200, false, false, false, false], ['Gỏi cuốn tôm thịt', 280, 18, 7, 35, 620, true, true, false, false], ['Bún chả', 550, 26, 18, 70, 1250, false, false, true, false], ['Hủ tiếu Nam Vang', 480, 24, 14, 65, 1400, false, false, false, false], ['Mì Quảng', 520, 25, 16, 68, 1350, false, false, true, false], ['Cao lầu', 490, 23, 15, 66, 1200, false, false, false, false], ['Bánh xèo', 580, 20, 28, 60, 900, false, true, false, false],
                ['Bún riêu', 430, 22, 14, 55, 1450, true, false, false, false], ['Bánh cuốn', 360, 17, 10, 50, 850, true, true, false, false], ['Cháo gà', 320, 20, 8, 43, 700, true, true, true, false], ['Cơm gà Hội An', 590, 32, 18, 73, 1050, false, false, true, false], ['Cá kho tộ', 390, 30, 18, 22, 1350, true, false, true, false], ['Canh chua cá', 210, 20, 5, 22, 800, true, true, true, false], ['Thịt kho trứng', 510, 29, 35, 18, 1100, false, false, true, false], ['Bún thịt nướng', 500, 25, 17, 64, 1050, false, false, false, false], ['Bò kho', 460, 31, 19, 41, 1250, true, false, true, false], ['Bún mắm', 550, 29, 18, 72, 1900, false, false, true, false],
                ['Bánh canh cua', 490, 24, 13, 69, 1500, false, false, false, false], ['Xôi gà', 610, 28, 18, 83, 900, false, false, true, false], ['Bún đậu mắm tôm', 570, 25, 22, 67, 1800, false, false, false, false], ['Cơm chiên Dương Châu', 650, 24, 25, 82, 1150, false, false, false, false], ['Gỏi gà', 320, 29, 12, 23, 720, true, true, true, false], ['Đậu hũ sốt cà', 280, 16, 12, 28, 520, true, true, false, true], ['Cơm gạo lứt ức gà', 430, 38, 8, 51, 480, true, true, true, false], ['Salad rau củ', 180, 6, 7, 24, 220, true, true, false, true], ['Chè đậu xanh', 310, 8, 6, 58, 80, true, true, false, true], ['Sữa chua trái cây', 240, 8, 6, 42, 95, true, true, false, true],
            ];
            $ingredientExamples = [
                'Phở bò' => ['Phở tươi' => 200, 'Thịt bò nạc' => 80, 'Hành tây' => 20, 'Ngò rí' => 5],
                'Bún bò Huế' => ['Bún tươi' => 200, 'Thịt bò nạc' => 80, 'Sườn heo' => 50],
                'Cơm tấm sườn' => ['Gạo tẻ' => 180, 'Sườn heo' => 120, 'Dưa leo' => 30],
                'Bánh mì thịt' => ['Bánh mì' => 100, 'Thịt heo nạc' => 80, 'Dưa leo' => 20],
                'Gỏi cuốn tôm thịt' => ['Bánh đa nem' => 40, 'Tôm sú' => 60, 'Thịt heo nạc' => 40],
                'Bún chả' => ['Bún tươi' => 180, 'Thịt heo nạc' => 100],
                'Bún riêu' => ['Bún tươi' => 180, 'Cua biển' => 70, 'Cà chua' => 50],
                'Cháo gà' => ['Gạo tẻ' => 60, 'Ức gà' => 80, 'Hành lá' => 10],
                'Bánh cuốn' => ['Gạo tẻ' => 90, 'Thịt heo nạc' => 50],
                'Bánh xèo' => ['Gạo tẻ' => 90, 'Tôm sú' => 40, 'Giá đỗ' => 50],
                'Bánh canh cua' => ['Cua biển' => 100, 'Bún tươi' => 200],
                'Xôi gà' => ['Gạo nếp cái' => 180, 'Ức gà' => 80],
                'Bún thịt nướng' => ['Bún tươi' => 180, 'Thịt heo nạc' => 100],
                'Bò kho' => ['Thịt bò nạc' => 120, 'Cà rốt' => 60, 'Bánh mì' => 100],
            ];
            $dishes = collect();
            foreach ($dishRows as $i => $row) {
                [$name,$cal,$protein,$fat,$carb,$sodium,$lowCal,$lowSodium,$highProtein,$vegetarian] = $row;
                $dish = Dish::create(['name' => $name, 'normalized_name' => Food::normalize($name), 'description' => 'Món Việt quen thuộc, số liệu dinh dưỡng ước tính theo một khẩu phần chuẩn.', 'serving_grams' => 300 + ($i % 4) * 50, 'calories' => $cal, 'protein_g' => $protein, 'fat_g' => $fat, 'carb_g' => $carb, 'fiber_g' => $vegetarian ? 5 : 2, 'sodium_mg' => $sodium, 'sugar_g' => 4 + ($i % 8), 'is_low_calorie' => $lowCal, 'is_low_sodium' => $lowSodium, 'is_high_protein' => $highProtein, 'is_vegetarian' => $vegetarian, 'data_source' => 'Dữ liệu tổng hợp minh họa; xem trang Giới thiệu']);
                foreach ($ingredientExamples[$name] ?? [] as $foodName => $grams) {
                    if ($food = Food::where('name', $foodName)->first()) {
                        $dish->ingredients()->attach($food, ['grams' => $grams]);
                    }
                }
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

            $phoGa = Dish::create(['name' => 'Phở gà', 'normalized_name' => Food::normalize('Phở gà'), 'description' => 'Ước tính dinh dưỡng cho một tô phở gà; thay đổi theo khẩu phần.', 'serving_grams' => 400, 'calories' => 420, 'protein_g' => 27, 'fat_g' => 10, 'carb_g' => 53, 'fiber_g' => 2, 'sodium_mg' => 1200, 'sugar_g' => 3, 'data_source' => 'Ước tính minh họa, chưa đối chiếu nguồn']);
            $dishes->push($phoGa);

            // Địa chỉ theo Michelin Guide; không lưu tọa độ, giờ, giá hoặc số điện thoại chưa kiểm chứng.
            $restaurantRows = [
                ['Cơm Tấm Ba Ghiền', '84 Đặng Văn Ngữ, Phú Nhuận, TP. Hồ Chí Minh', 'Phú Nhuận', 'com-tam-ba-ghien', 'Cơm tấm sườn'],
                ['Phở Lệ (An Đông)', '413–415 Nguyễn Trãi, An Đông, TP. Hồ Chí Minh', 'An Đông', 'pho-le-district-5', 'Phở bò'],
                ['Bánh Xèo 46A', '46 Đinh Công Tráng, Tân Định, TP. Hồ Chí Minh', 'Tân Định', 'banh-xeo-46a', 'Bánh xèo'],
                ['Bún Bò Huế 14B', '14B Đường số 46, Vĩnh Hội, TP. Hồ Chí Minh', 'Vĩnh Hội', 'bun-bo-hue-14b', 'Bún bò Huế'],
                ['Phở Phượng', '25 Hoàng Sa, Sài Gòn, TP. Hồ Chí Minh', 'Sài Gòn', 'pho-phuong', 'Phở bò'],
                ['Phở Minh (Sài Gòn)', '63/6 Pasteur, Sài Gòn, TP. Hồ Chí Minh', 'Sài Gòn', 'pho-minh', 'Phở bò'],
                ['Xôi Gà Number One', '15 Nguyễn Trung Trực, Bến Thành, TP. Hồ Chí Minh', 'Bến Thành', 'xoi-ga-number-one', 'Xôi gà'],
                ['Phở Miến Gà Kỳ Đồng', '14/5 Bis Kỳ Đồng, Xuân Hòa, TP. Hồ Chí Minh', 'Xuân Hòa', 'pho-mien-ga-ky-%C4%91ong', 'Phở gà'],
                ['Bánh Canh Cua Bà Ba', '84/6 Nguyễn Biểu, Chợ Quán, TP. Hồ Chí Minh', 'Chợ Quán', 'banh-canh-cua-ba-ba', 'Bánh canh cua'],
                ['Bún Riêu Yến', '1346 Trường Sa, Tân Sơn Hòa, TP. Hồ Chí Minh', 'Tân Sơn Hòa', 'bun-rieu-yen', 'Bún riêu'],
                ['Bò Kho Gánh', '029 Lô H, chung cư Ngô Gia Tự, Sư Vạn Hạnh, Vườn Lài, TP. Hồ Chí Minh', 'Vườn Lài', 'bo-kho-ganh', 'Bò kho'],
                ['Bánh Cuốn Tây Hồ 127', '127 Đinh Tiên Hoàng, Tân Định, TP. Hồ Chí Minh', 'Tân Định', 'banh-cuon-tay-ho-127', 'Bánh cuốn'],
                ['Bún Thịt Nướng Hoàng Văn', '137 Chấn Hưng, Tân Hòa, TP. Hồ Chí Minh', 'Tân Hòa', 'bun-thit-nuong-hoang-van', 'Bún thịt nướng'],
                ['Phở Hương Bình', '148 Võ Thị Sáu, Xuân Hòa, TP. Hồ Chí Minh', 'Xuân Hòa', 'pho-huong-binh', 'Phở bò'],
                ['Phở Chào', '52 Nguyễn Công Trứ, Thạnh Mỹ Tây, TP. Hồ Chí Minh', 'Thạnh Mỹ Tây', 'pho-chao', 'Phở bò'],
                ['Bún Bò Huế Cô Như', '274/29 Võ Văn Tần, Bàn Cờ, TP. Hồ Chí Minh', 'Bàn Cờ', 'bun-bo-hue-co-nhu', 'Bún bò Huế'],
            ];
            foreach ($restaurantRows as [$name, $address, $area, $slug, $dishName]) {
                $restaurant = Restaurant::create(['name' => $name, 'normalized_name' => Food::normalize($name), 'address' => $address, 'area' => $area, 'source_url' => 'https://guide.michelin.com/en/ho-chi-minh/ho-chi-minh_2978179/restaurant/'.$slug, 'price_level' => 'budget', 'data_updated_at' => today()]);
                if ($dish = $dishes->firstWhere('name', $dishName)) {
                    $restaurant->dishes()->attach($dish, ['price' => 0, 'is_available' => true]);
                }
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

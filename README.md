# Nhật ký dinh dưỡng & Gợi ý quán ăn món Việt

Ứng dụng web responsive phục vụ đồ án học phần Lập trình mã nguồn mở. Người dùng có thể tra cứu dinh dưỡng thực phẩm/món Việt, tính nhu cầu cá nhân, tìm quán bán món, ghi nhật ký 4 bữa và theo dõi báo cáo. Khu vực Admin quản lý toàn bộ dữ liệu và công thức.

> Số liệu dinh dưỡng, giá và thông tin quán trong bộ dữ liệu mẫu chỉ dùng cho mục đích học tập, không thay thế tư vấn của bác sĩ hoặc chuyên gia dinh dưỡng.

## Công nghệ

- PHP 8.3+, Laravel 13, MySQL 8/MariaDB tương thích.
- Blade, Tailwind CSS 4, Alpine.js, Chart.js.
- DomPDF để xuất báo cáo.
- PHPUnit, Laravel Pint và Larastan/PHPStan.
- PostCSS + esbuild để đóng gói frontend, không cần Vite dev server.

## Phạm vi chức năng

Toàn bộ 40 chức năng trong đặc tả đã được triển khai.

### 25 chức năng bắt buộc

| Module | Chức năng |
| --- | --- |
| Tra cứu | 1.1 lọc thực phẩm; 1.2 bảng dinh dưỡng; 1.3 món Việt và nguyên liệu; 1.4 quy đổi khẩu phần; 1.5 tìm kiếm không dấu; 1.6 BMI/BMR/TDEE/Macro. |
| Quán ăn | 2.1 danh sách quán; 2.2 quán bán món; 2.3 thực đơn; 2.4 lọc theo mục tiêu; 2.5 gợi ý theo calo còn lại; 2.6 ghi nhanh món từ quán. |
| Nhật ký | 3.1 hồ sơ thể trạng; 3.2 hạn mức cá nhân; 3.3 CRUD bốn bữa; 3.4 tổng hợp Kcal/Macro; 3.5 cảnh báo calo, natri, đường và chất béo. |
| Báo cáo | 4.1 biểu đồ calo và macro; 4.2 báo cáo tuần, món/quán thường dùng. |
| Tài khoản | 5.1 đăng ký/đăng nhập; 5.2 hồ sơ; 5.3 giới thiệu và nguồn dữ liệu. |
| Admin | 6.1 CRUD thực phẩm, món, nhóm, khẩu phần, dị ứng; 6.2 CRUD quán và thực đơn; 6.3 khóa/mở tài khoản. |

### 15 chức năng tùy chọn

- 1.7 so sánh hai món/thực phẩm; 1.8 yêu thích; 1.9 cảnh báo dị ứng.
- 2.7 lọc khu vực, mức giá, giờ mở cửa; 2.8 OpenStreetMap, chỉ đường và sắp xếp gần tôi; 2.9 quán yêu thích.
- 3.6 mục tiêu cân nặng; 3.7 nhật ký nước; 3.8 nhắc bữa ăn/uống nước bằng Web Notification.
- 4.3 biểu đồ cân nặng; 4.4 xuất PDF theo khoảng ngày.
- 5.4 khôi phục mật khẩu; 5.5 tiếng Việt/tiếng Anh cho điều hướng chính và chế độ tối.
- 6.4 cấu hình hệ số vận động, sàn/điều chỉnh calo, macro và ngưỡng cảnh báo; 6.5 dashboard thống kê tổng quan.

## Dữ liệu mẫu

Seeder tạo sẵn:

- 15 nhóm thực phẩm, 43 nguyên liệu/thực phẩm, 30 món Việt và 8 đơn vị khẩu phần.
- 16 quán ăn, mỗi quán có thực đơn và giá tham khảo.
- 6 nhãn dị ứng, cấu hình công thức, nhật ký/nước/cân nặng minh họa.
- Tổng dữ liệu thực phẩm + món: 73 mục.

Tài khoản demo:

| Vai trò | Email | Mật khẩu |
| --- | --- | --- |
| Admin | `admin@dinhduong.test` | `Admin@123` |
| Người dùng | `user@dinhduong.test` | `User@123` |

Hãy đổi mật khẩu nếu dùng ngoài môi trường trình diễn cục bộ.

## Cài đặt trên Laragon

### 1. Yêu cầu

- Laragon đang chạy MySQL trên `127.0.0.1:3306`.
- Tài khoản MySQL mặc định: `root`, mật khẩu rỗng. Nếu máy đã đổi mật khẩu root, cập nhật `DB_PASSWORD` trong `.env` hoặc dùng `Menu > MySQL > Reset root password` của Laragon.
- PHP 8.3+, Composer 2, Node.js 22+ và npm.

### 2. Cài dependency và môi trường

Mở Laragon Terminal hoặc PowerShell:

```powershell
cd D:\lap-trinh-ma-nguon-mo\src
composer install
Copy-Item .env.example .env
php artisan key:generate
npm ci
npm run build
```

File `.env.example` đã cấu hình sẵn:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nhat_ky_dinh_duong
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Tạo database, migration và seed tự động

```powershell
php artisan nutrition:install --fresh
```

Lệnh trên tự tạo database UTF-8 nếu chưa tồn tại, chạy toàn bộ migration và nạp dữ liệu mẫu. Tùy chọn `--fresh` sẽ xóa các bảng hiện có trong đúng database trước khi tạo lại.

Có thể dùng lệnh Laravel chuẩn tương đương sau khi database đã tồn tại:

```powershell
php artisan migrate:fresh --seed
```

### 4. Chạy ứng dụng

```powershell
php artisan serve
```

Mở `http://127.0.0.1:8000`. Nếu đã tạo virtual host Laragon trỏ document root đến thư mục `public`, dùng địa chỉ trong `APP_URL`.

Khi phát triển giao diện:

```powershell
npm run dev
```

Lệnh này theo dõi CSS, JavaScript và Blade; nhấn `Ctrl+C` để dừng.

## Kiểm thử và kiểm định

Test dùng SQLite in-memory độc lập với MySQL của Laragon:

```powershell
php artisan config:clear
php artisan test
vendor\bin\pint --test
vendor\bin\phpstan analyse --no-progress --memory-limit=1G
composer audit
npm audit
npm run build
```

Kết quả xác minh tại thời điểm bàn giao: 40 test, 134 assertion, 0 lỗi Larastan, 0 advisory Composer và 0 vulnerability npm.

## Cấu trúc dữ liệu

Các bảng nghiệp vụ chính gồm `users`, `health_profiles`, `food_groups`, `foods`, `serving_units`, `dishes`, `restaurants`, `daily_logs`, `food_log_entries`, `water_logs`, `weight_entries` và `favorites`. Các bảng liên kết/danh mục bổ sung quản lý nguyên liệu món, thực đơn quán, dị ứng, cấu hình và lịch sử tìm kiếm.

## Bảo mật đã áp dụng

- CSRF cho mọi thao tác thay đổi dữ liệu, validation theo whitelist và Eloquent binding.
- Middleware phân quyền Admin, chặn tài khoản bị khóa và kiểm tra quyền sở hữu bản ghi nhật ký.
- Hash mật khẩu, xác minh email/khôi phục mật khẩu theo Laravel, giới hạn đăng nhập của Breeze.
- Header `nosniff`, chống iframe, referrer/permissions policy; escape Blade mặc định.
- Không lưu bí mật trong repository; `.env` nằm trong `.gitignore`.

## Lưu ý trình duyệt

- “Gần tôi nhất” cần quyền vị trí và hoạt động tốt nhất trên HTTPS hoặc localhost.
- Nhắc nhở cần người dùng bấm “Cho phép thông báo”; trình duyệt có thể giới hạn thông báo nền.
- Bản đồ nhúng dùng OpenStreetMap; nút chỉ đường mở Google Maps mà không cần API key.

## Giấy phép

Mã nguồn dùng cho mục đích học tập theo giấy phép MIT của Laravel và các thư viện đi kèm.

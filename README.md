
# News Web

Website tin tức/blog xây dựng bằng Laravel 13, Blade, Tailwind CSS 4 và Vite. Ứng dụng hỗ trợ duyệt tin, quản trị nội dung, tương tác bình luận và cá nhân hóa trải nghiệm đọc.

## Kiến trúc dự án

- `app/Http/Controllers`: xử lý xác thực, trang chủ, bài viết, bình luận, tài khoản và quản trị.
- `app/Http/Requests/Admin`: xác thực dữ liệu tạo/cập nhật bài viết quản trị.
- `app/Policies`: phân quyền thao tác với bình luận bằng Laravel Policy.
- `app/Models`: Eloquent models cho người dùng, danh mục, bài viết, bình luận và bài đã lưu.
- `app/Console/Commands/FetchRssNews.php`, `config/feeds.php`: lấy tin từ các nguồn RSS cấu hình sẵn.
- `database/migrations`, `database/seeders`: cấu trúc CSDL và dữ liệu mẫu.
- `resources/views`: Blade templates cho trang tin, bài viết, đăng nhập, tài khoản và quản trị.
- `resources/css`, `resources/js`: giao diện và tương tác phía trình duyệt.
- `public/manifest.json`, `public/sw.js`: PWA manifest và Service Worker cho đọc bài đã cache khi offline.
- `routes/web.php`: các route web công khai, xác thực và quản trị.
- `tests/Feature`: kiểm thử luồng xác thực, phân quyền, CRUD, bình luận và bài viết đã lưu.

## Sáu nhóm tính năng

1. **Duyệt tin:** trang chủ, danh mục, tìm kiếm và sắp xếp bài viết.
2. **Quản trị nội dung:** đăng nhập admin, CRUD danh mục/bài viết, dashboard và lấy tin RSS.
3. **Trải nghiệm đọc:** lượt xem chống tăng lặp trong session, nội dung HTML/RSS được lọc an toàn, bài liên quan, Open Graph, chia sẻ và điều chỉnh cỡ chữ.
4. **Bình luận:** gửi và tải bình luận bằng Fetch API, đăng công khai ngay, giới hạn 500 ký tự, yêu cầu đăng nhập; `CommentPolicy` chỉ cho chủ bình luận hoặc admin xóa, admin có thể ẩn bình luận.
5. **Cá nhân hóa:** quan hệ `belongsToMany` để lưu/bỏ lưu bài viết; trang bài đã lưu và lịch sử đọc gần đây riêng, lịch sử lưu trong session.
6. **Đọc offline:** PWA cache CSS, JavaScript, biểu tượng và tối đa 20 bài đã mở/đã lưu; thông báo khi mất và khôi phục kết nối. Bình luận/bookmark không được xếp hàng để đồng bộ khi offline.

## Yêu cầu

- PHP 8.3 trở lên và Composer.
- Node.js và npm.
- MySQL/MariaDB (ví dụ Laragon) hoặc SQLite.

## Cài đặt trên Windows/Laragon

1. Clone dự án và mở PowerShell tại thư mục dự án:

   ```powershell
   git clone https://github.com/DTDQuynh/news-web.git
   cd news-web
   ```

2. Cài thư viện PHP và tạo cấu hình môi trường:

   ```powershell
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   ```

3. Chọn một cấu hình CSDL trong `.env`.

   **MySQL/MariaDB:** khởi động MySQL trong Laragon, tạo CSDL rỗng `news_db`, rồi đặt:

   ```dotenv
   APP_URL=http://127.0.0.1:8000
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=news_db
   DB_USERNAME=root
   DB_PASSWORD=
   SESSION_DRIVER=file
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   ```

   **SQLite:** giữ `DB_CONNECTION=sqlite` trong `.env.example` và tạo file CSDL nếu chưa có:

   ```powershell
   if (!(Test-Path database\database.sqlite)) {
       New-Item -ItemType File database\database.sqlite | Out-Null
   }
   ```

4. Tạo bảng, dữ liệu mẫu và liên kết lưu trữ ảnh:

   ```powershell
   php artisan migrate --seed
   php artisan storage:link
   ```

5. Cài và build frontend:

   ```powershell
   npm install
   npm run build
   ```

6. Chạy ứng dụng:

   ```powershell
   php artisan serve --host=127.0.0.1 --port=8000
   ```

   Mở [http://127.0.0.1:8000](http://127.0.0.1:8000). Khi phát triển giao diện, chạy `npm run dev` trong một terminal khác.

### Đọc offline/PWA

Service Worker chỉ hoạt động trên HTTPS hoặc địa chỉ loopback như `localhost`/`127.0.0.1`. Vì vậy `http://127.0.0.1:8000` dùng được cho phát triển; domain Laragon dạng `http://news-web-main.test:8080` cần bật HTTPS mới hỗ trợ PWA. Hãy mở bài viết hoặc đăng nhập để lưu bài khi còn mạng trước khi ngắt kết nối.

## Tài khoản dữ liệu mẫu

Được tạo bởi `php artisan migrate --seed`:

| Vai trò | Email | Mật khẩu |
| --- | --- | --- |
| Quản trị viên | `admin@newsapp.test` | `12345678` |
| Độc giả | `user@newsapp.test` | `12345678` |

Đây là thông tin dùng cho môi trường phát triển/kiểm thử; không dùng mật khẩu mẫu trên hệ thống công khai.

## Chạy kiểm thử

Kiểm thử dùng SQLite in-memory và không cần MySQL:

```powershell
php artisan test
```

Chạy một nhóm riêng:

```powershell
php artisan test tests\Feature\AuthAndAuthorizationTest.php
php artisan test tests\Feature\AdminPostManagementTest.php
```

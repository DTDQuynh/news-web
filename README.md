# Website tin tức và blog

# Hướng dẫn cài môi trường
1. Tải code từ Github về máy
   git clone https://github.com/DTDQuynh/news-web.git
2. Cài composer
3. Chỉnh sửa dữ liệu trong file .env 
   DB_DATABASE=news_db ,SESSION_DRIVER=file

   xóa dấu # của:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=

4. Tạo csdl rỗng tên news_db
5. Mở terminal chạy: php artisan migrate --seed
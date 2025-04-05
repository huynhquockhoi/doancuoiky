# Sử dụng PHP 7.4 Apache
FROM php:7.4-apache

# Cài đặt các extensions cần thiết của PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Cài đặt các công cụ hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Tạo thư mục uploads 
RUN mkdir -p /var/www/html/uploads
RUN chmod 777 /var/www/html/uploads

# Cấu hình Apache
RUN a2enmod rewrite

# Cấu hình upload file
RUN echo "file_uploads = On\n" \
    "upload_max_filesize = 10M\n" \
    "post_max_size = 10M\n" \
    "max_execution_time = 30\n" > /usr/local/etc/php/conf.d/uploads.ini

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Sao chép mã nguồn
COPY ./src /var/www/html/

# Expose port
EXPOSE 80

# Khởi động Apache
CMD ["apache2-foreground"]
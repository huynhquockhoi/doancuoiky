FROM php:7.4-apache

# Cài đặt các extensions cần thiết của PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Tạo thư mục uploads 
RUN mkdir -p /var/www/html/uploads
RUN chmod 777 /var/www/html/uploads

# Cấu hình cho việc upload file
RUN echo "file_uploads = On\n" \
    "upload_max_filesize = 10M\n" \
    "post_max_size = 10M\n" \
    "max_execution_time = 30\n" > /usr/local/etc/php/conf.d/uploads.ini
    
WORKDIR /var/www/html
COPY . /var/www/html/
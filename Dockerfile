FROM php:8.1-apache

# Cài các extension PHP cần thiết (tuỳ project)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Bật mod_rewrite
RUN a2enmod rewrite

# Copy toàn bộ mã nguồn vào container
COPY . /var/www/html/

# Set quyền
RUN chown -R www-data:www-data /var/www/html

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

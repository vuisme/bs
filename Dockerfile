FROM php:5.6-apache

# Cài ext cần thiết
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Bật mod_rewrite
RUN a2enmod rewrite

# Copy source vào container
COPY . /var/www/html/

# Set quyền
RUN chown -R www-data:www-data /var/www/html

# Tùy chỉnh Apache cho CI
RUN echo '<Directory /var/www/html/>\n\
    AllowOverride All\n\
</Directory>' > /etc/apache2/conf-available/allow-override.conf \
  && a2enconf allow-override

EXPOSE 80
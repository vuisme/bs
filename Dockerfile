FROM php:7.4-apache

# Cài ext cần thiết
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Bật mod_rewrite cho CI
RUN a2enmod rewrite

# Cho phép .htaccess override
RUN echo '<Directory /var/www/html/>\n\
    AllowOverride All\n\
</Directory>' > /etc/apache2/conf-available/allow-override.conf \
  && a2enconf allow-override

# Copy source vào container
COPY . /var/www/html/

# Set quyền cho apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
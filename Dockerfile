# Stage 1: Build Frontend
FROM node:20 AS frontend-builder
WORKDIR /app
COPY larapp /app
RUN npm install || true
# Ensure public/build exists so later COPY won't fail when dev build is performed by Vite service
RUN mkdir -p /app/public/build

# Stage 2: Build PHP Application
FROM php:8.2-apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install package yang dibutuhkan Laravel
RUN apt-get -y update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    git \
    curl \
    ca-certificates \
    gnupg \
    netcat-openbsd

# Konfigurasi PHP agar bisa upload file besar
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Install PHP extensions
RUN docker-php-ext-configure intl
RUN docker-php-ext-configure gd --with-jpeg --with-freetype
RUN docker-php-ext-install intl opcache pdo_mysql zip gd

# Enable Apache modules
RUN a2enmod rewrite

# Xdebug (opsional)
RUN pecl install xdebug || true

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy aplikasi Laravel
COPY larapp /var/www/html/

# Copy container entrypoint that will run migrations/seeds and then start apache
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy hasil build frontend dari stage sebelumnya
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

RUN chown -R www-data:www-data /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install PHP dependencies untuk production
RUN composer install --no-interaction --optimize-autoloader --no-dev

# L5 Swagger will be published/generated at runtime in the entrypoint (safer with mounted code)

# Use custom entrypoint to run migrations/seeds at container start, then exec default CMD
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
# Ensure Apache foreground is the default command so entrypoint's `exec "$@"` runs the webserver
CMD ["apache2-foreground"]

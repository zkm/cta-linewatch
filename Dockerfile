# syntax=docker/dockerfile:1

# ---------- Builder: install PHP deps with Composer ----------
FROM php:8.2-cli AS vendor

# Install Composer and required PHP extensions
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install intl and zip extensions, plus unzip utility
RUN apt-get update && \
    apt-get install -y libicu-dev libzip-dev unzip && \
    docker-php-ext-install intl zip && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader

# Copy the rest to ensure classmap can be optimized if needed (optional)
COPY app app

# ---------- Runtime: Apache + PHP ----------
FROM php:8.2-apache

# Install system deps and PHP extensions
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
      libicu-dev \
      libzip-dev \
      unzip \
    ; \
    docker-php-ext-install -j"$(nproc)" intl; \
    docker-php-ext-install -j"$(nproc)" opcache; \
    docker-php-ext-enable opcache; \
    rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite headers

WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Copy Composer vendor from builder
COPY --from=vendor /app/vendor /var/www/html/vendor

# Copy env file as .env if it doesn't exist
RUN if [ -f /var/www/html/env ] && [ ! -f /var/www/html/.env ]; then \
      cp /var/www/html/env /var/www/html/.env; \
    fi

# Configure Apache vhost to serve from public/ and route to index.php
RUN set -eux; \
    sed -ri 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' /etc/apache2/sites-available/000-default.conf; \
    printf '%s\n' \
      '<Directory /var/www/html/public>' \
      '    AllowOverride All' \
      '    Options FollowSymLinks' \
      '    DirectoryIndex index.php' \
      '    <IfModule mod_rewrite.c>' \
      '        RewriteEngine On' \
      '        RewriteCond %{REQUEST_FILENAME} !-f' \
      '        RewriteCond %{REQUEST_FILENAME} !-d' \
      '        RewriteRule ^ index.php [L]' \
      '    </IfModule>' \
      '</Directory>' \
      >> /etc/apache2/apache2.conf

# Set recommended PHP production settings (basic)
RUN { \
      echo 'opcache.enable=1'; \
      echo 'opcache.enable_cli=0'; \
      echo 'opcache.validate_timestamps=0'; \
      echo 'memory_limit=256M'; \
      echo 'upload_max_filesize=16M'; \
      echo 'post_max_size=16M'; \
    } > /usr/local/etc/php/conf.d/zz-production.ini

# Ensure writable permissions
RUN chown -R www-data:www-data /var/www/html/writable

# Expose and run
EXPOSE 80
CMD ["apache2-foreground"]

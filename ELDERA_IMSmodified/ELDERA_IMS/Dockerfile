FROM php:8.2-cli

# Install OS packages and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

WORKDIR /app

# Copy application code
COPY . /app

# Install dependencies and optimize autoloader
RUN composer install --no-dev --optimize-autoloader

# Default command: run PHP built-in server on Render's port
CMD ["sh", "-lc", "php -S 0.0.0.0:$PORT -t public"]
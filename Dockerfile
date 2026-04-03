# Use official PHP image
FROM php:8.2-fpm-bullseye

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js 22 from NodeSource
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_mysql \
    zip \
    bcmath \
    ctype \
    fileinfo \
    mbstring \
    xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .
COPY docker/app/entrypoint.sh /usr/local/bin/docker-app-entrypoint
COPY docker/app/start-vite.sh /usr/local/bin/docker-vite-start

# Create necessary directories with proper permissions
RUN mkdir -p /app/storage/framework/sessions /app/storage/framework/views /app/storage/framework/cache/data /app/bootstrap/cache && \
    chown -R www-data:www-data /app /app/storage /app/bootstrap/cache && \
    chmod -R 775 /app/storage /app/bootstrap/cache && \
    chmod +x /usr/local/bin/docker-app-entrypoint /usr/local/bin/docker-vite-start

# Configure PHP-FPM with increased limits
RUN echo "[www]" > /usr/local/etc/php-fpm.d/99-custom.conf && \
    echo "pm.max_children = 20" >> /usr/local/etc/php-fpm.d/99-custom.conf && \
    echo "pm.start_servers = 10" >> /usr/local/etc/php-fpm.d/99-custom.conf && \
    echo "pm.min_spare_servers = 5" >> /usr/local/etc/php-fpm.d/99-custom.conf && \
    echo "pm.max_spare_servers = 15" >> /usr/local/etc/php-fpm.d/99-custom.conf && \
    echo "pm.max_requests = 100" >> /usr/local/etc/php-fpm.d/99-custom.conf

# Install composer dependencies
RUN composer install --no-interaction --optimize-autoloader && \
    npm install

# Expose port
EXPOSE 9000

ENTRYPOINT ["docker-app-entrypoint"]

# Start PHP-FPM
CMD ["php-fpm"]

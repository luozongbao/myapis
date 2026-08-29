# =============================================================
# MyAPIs - PHP-FPM Image
# -------------------------------------------------------------
# Installs required PHP extensions for the bundled APIs:
#   - json, mbstring : required for all JSON-based APIs
#   - gd             : required for the PromptPay QR generator
#   - opcache        : performance
# =============================================================
FROM php:8.2-fpm-alpine

# Set working directory inside the container
WORKDIR /var/www/html

# Install system dependencies needed for building PHP extensions
RUN apk add --no-cache \
        bash \
        git \
        curl \
        tzdata \
        fcgi \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        icu-dev \
        oniguruma-dev \
        autoconf \
        g++ \
        make \
    && rm -rf /var/cache/apk/*

# Install commonly used PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        intl \
        mbstring \
        opcache \
        bcmath

# Copy a production-tuned opcache configuration
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copy the PHP ini template and an entrypoint that renders it
# using environment variables at container start-up.
COPY docker/php/php.ini.tpl /usr/local/etc/php/php.ini.tpl
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Install the PHP-FPM healthcheck script used by docker-compose
RUN curl -fsSL https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/v0.5.0/php-fpm-healthcheck \
        -o /usr/local/bin/php-fpm-healthcheck \
    && chmod +x /usr/local/bin/php-fpm-healthcheck

# Ensure the www-data user owns the project files
RUN chown -R www-data:www-data /var/www/html

# Default command (can be overridden in docker-compose)
CMD ["entrypoint.sh"]

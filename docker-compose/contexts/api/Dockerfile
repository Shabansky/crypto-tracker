# Use the official PHP image as a base
FROM php:8.4-fpm

# Define app dir for easy access
ENV APP_DIR=/var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd dom

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR $APP_DIR

# Apply ownership rules
RUN mkdir -p $APP_DIR/storage && chmod -R guo+w ${APP_DIR}/storage

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
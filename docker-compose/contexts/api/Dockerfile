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
    curl \
    cron

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd dom

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR $APP_DIR

# Apply ownership rules
RUN mkdir -p $APP_DIR/storage && chmod -R guo+w ${APP_DIR}/storage

# Copy crontab file and set permissions
COPY crontab /etc/cron.d/api-cron
RUN chmod 0744 /etc/cron.d/api-cron

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

# Copy the start script
COPY scripts.sh /usr/local/bin/scripts.sh
RUN chmod +x /usr/local/bin/scripts.sh

# Expose port 9000 and start services
EXPOSE 9000
CMD ["sh", "/usr/local/bin/scripts.sh"]
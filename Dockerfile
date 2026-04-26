FROM php:8.2-apache

# Install system dependencies and Node.js
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    nodejs \
    npm

# Install PHP extensions for Laravel (PostgreSQL and MySQL support)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql mbstring

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files into the container
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# Install frontend dependencies and build assets (Vite)
RUN npm install && npm run build

# Set appropriate permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Point Apache to the Laravel public directory
RUN sed -i -e 's/html/html\/public/g' /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]

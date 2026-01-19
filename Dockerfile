# Flynax Real Estate - Docker Image for Coolify
FROM php:8.3-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo \
        pdo_mysql \
        zip \
        intl \
        bcmath \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite headers expires deflate

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Update Apache configuration to allow .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Set PHP configuration
RUN echo "session.save_path = /tmp" >> /usr/local/etc/php/conf.d/flynax.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/flynax.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/flynax.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/flynax.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/flynax.ini \
    && echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/flynax.ini \
    && echo "max_input_vars = 5000" >> /usr/local/etc/php/conf.d/flynax.ini \
    && echo "display_errors = Off" >> /usr/local/etc/php/conf.d/flynax.ini \
    && echo "error_reporting = E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT" >> /usr/local/etc/php/conf.d/flynax.ini

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Create required directories and set permissions
RUN mkdir -p tmp/compile tmp/cache tmp/upload files plugins \
    && chmod -R 777 tmp \
    && chmod -R 777 files \
    && chmod -R 777 plugins \
    && chmod 1777 /tmp \
    && rm -f tmp/compile/*.php

# Clean Smarty compile cache on startup
RUN echo '#!/bin/bash\nrm -f /var/www/html/tmp/compile/*.php\napache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Set proper ownership
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["/usr/local/bin/start.sh"]

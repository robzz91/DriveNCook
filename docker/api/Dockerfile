FROM php:8.2-apache

# Activer extensions nécessaires (SQLite)
RUN apt-get update && apt-get install -y \
    libmariadb-dev \
    && docker-php-ext-install pdo_mysql

# DocumentRoot et permissions
WORKDIR /var/www/html
COPY . /var/www/html

# Apache écoute sur 80 par défaut, rien à faire
RUN a2enmod rewrite && \
    sed -ri 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/000-default.conf && \
    sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

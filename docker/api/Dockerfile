FROM php:8.2-fpm

# Installer dépendances
RUN apt-get update && apt-get install -y \
    curl zip unzip git libzip-dev \
    && docker-php-ext-install pdo_mysql zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www

# Copier les fichiers Laravel
COPY . .

# Installer les dépendances Laravel
RUN composer install

# Lancer le serveur
CMD php artisan serve --host=0.0.0.0 --port=8000

# Utiliser l'image PHP officielle avec Apache
FROM php:8.2-apache

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Installer Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Activer le module Apache rewrite
RUN a2enmod rewrite

# Configurer Apache pour Laravel
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Copier les fichiers de l'application
COPY . /var/www/html

# Créer les répertoires nécessaires et définir les permissions
RUN mkdir -p /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Installer les dépendances Node.js et compiler les assets
RUN npm install && npm run build

# Exposer le port 80
EXPOSE 80

# Script de démarrage
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Commande par défaut
CMD ["docker-entrypoint.sh"]
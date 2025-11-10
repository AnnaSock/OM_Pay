# ===============================
# Étape 1 : Builder PHP avec Composer
# ===============================
FROM composer:2.6 AS vendor

WORKDIR /var/www/html

# Copier uniquement les fichiers Composer
COPY composer.json composer.lock ./

# Installer les dépendances Laravel sans scripts (artisan n'existe pas encore)
RUN composer install --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# ===============================
# Étape 2 : Image finale PHP-FPM
# ===============================
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Installer les extensions PHP nécessaires à Laravel
RUN apk add --no-cache \
        bash \
        git \
        libpng-dev \
        libjpeg-turbo-dev \
        libzip-dev \
        oniguruma-dev \
        postgresql-dev \
        zip unzip \
    && docker-php-ext-configure gd \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip gd

# Copier les dépendances Laravel depuis la 1ère étape
COPY --from=vendor /var/www/html /var/www/html

# Copier tout le code Laravel
COPY . /var/www/html

# Définir les permissions et artisan exécutable
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/artisan

# Exécuter package:discover après avoir copié tout le code
RUN php artisan package:discover --ansi || true  # sécurité si certains packages manquent

# ===============================
# CMD dynamique selon l'environnement
# ===============================
# Expose dynamiquement le port
ARG APP_ENV=local
ENV APP_ENV=${APP_ENV}

# Exposer 9000 si production
EXPOSE 9000

CMD ["sh", "-c", "\
  if [ \"$APP_ENV\" = \"production\" ]; then \
    php-fpm; \
  else \
    php artisan serve --host=0.0.0.0 --port=8081; \
  fi \
"]

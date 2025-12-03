FROM php:8.2-fpm

# 1) Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# 2) Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql intl zip

# 3) Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4) Installer Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# 5) Créer le dossier de travail
WORKDIR /var/www/html

# 6) Permissions
RUN usermod -u 1000 www-data
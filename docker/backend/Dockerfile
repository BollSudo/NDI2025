FROM php:8.2-cli-alpine

WORKDIR /app/backend

# Installation des dépendances nécessaires pour Symfony
RUN apk add --no-cache \
    git \
    zip \
    unzip \
    curl \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    bash \
    npm \
    && docker-php-ext-install pdo pdo_mysql zip intl opcache bcmath

# Installer Symfony CLI et le déplacer dans /usr/local/bin
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN export PATH="$HOME/.symfony/bin:$PATH"

# Installation de composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

EXPOSE 90

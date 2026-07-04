FROM php:8.5-fpm

ARG PHP_MEMORY_LIMIT=1024M

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libnss3 \
    libatk-bridge2.0-0 \
    libgtk-3-0 \
    libxss1 \
    libasound2 \
    libgbm-dev \
    libx11-xcb1 \
    libxcb-dri3-0 \
    libdrm2 \
    libxdamage1 \
    libxrandr2 \
    libxcomposite1 \
    libxcursor1 \
    libxi6 \
    libxtst6 \
    git \
    curl \
    zip \
    unzip \
    gnupg \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libmagickwand-dev \
    fontconfig \
    fonts-dejavu \
    fonts-liberation \
    xfonts-75dpi \
    xfonts-base \
    && rm -rf /var/lib/apt/lists/*

# Configurar GD correctamente
RUN docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/include/

# Instalar extensiones PHP necesarias para Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    intl \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Instalar extensión Imagick
RUN pecl install imagick && docker-php-ext-enable imagick

# Configurar limite de memoria PHP
RUN echo "memory_limit=${PHP_MEMORY_LIMIT}" > /usr/local/etc/php/conf.d/memory-limit.ini

# Instalar Node para Vite/Laravel frontend tooling
RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD ["php-fpm"]

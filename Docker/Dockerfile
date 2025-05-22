FROM php:8.3-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    netcat-openbsd \
    zip \
    unzip \
    jpegoptim optipng pngquant gifsicle \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка и настройка PHP расширений
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Установка Redis
RUN pecl install redis && docker-php-ext-enable redis

# Копирование кастомного php.ini
COPY Docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создаём пользователя и группу
RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www

# Настройка рабочей директории
WORKDIR /var/www/html

# Фикс для Git безопасности
RUN git config --global --add safe.directory /var/www/html

# Копирование только необходимого для установки зависимостей
COPY --chown=www:www composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader

# Копирование всех файлов проекта
COPY --chown=www:www . .

# Оптимизация автозагрузчика и прав
RUN composer dump-autoload --optimize \
    && chown -R www:www /var/www/html/storage \
    && chown -R www:www /var/www/html/bootstrap/cache

# Переключаемся на пользователя www
USER www

# Открываем порт и запускаем php-fpm
EXPOSE 9000
CMD ["php-fpm"]

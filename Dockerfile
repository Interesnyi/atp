FROM php:8.1-apache

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    default-mysql-client

# Устанавливаем PHP расширения
RUN docker-php-ext-install pdo pdo_mysql mysqli zip

# Включение mod_rewrite для Apache
RUN a2enmod rewrite

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Создание директории для сессий и установка прав
RUN mkdir -p /tmp/sessions \
    && chown -R www-data:www-data /tmp/sessions \
    && chmod 1733 /tmp/sessions

# Копирование файлов проекта
COPY . /var/www/html/

# Установка зависимостей через composer
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

# Настройка Apache для использования public директории
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Установка прав доступа
RUN chown -R www-data:www-data /var/www/html

# Настройка Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80 
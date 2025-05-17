FROM php:8.1-apache

# Установка необходимых расширений PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Включение mod_rewrite для Apache
RUN a2enmod rewrite

# Копирование файлов проекта
COPY . /var/www/html/

# Настройка Apache для использования public директории
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Установка прав доступа
RUN chown -R www-data:www-data /var/www/html

# Настройка Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80 
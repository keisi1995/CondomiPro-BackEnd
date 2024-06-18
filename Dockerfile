FROM php:8.2-apache

WORKDIR /var/www/html

# Instala las dependencias necesarias
RUN apt-get update \
    && apt-get install -y libzip-dev \
    && docker-php-ext-install zip pdo_mysql \
    && a2enmod rewrite

# Copia los archivos del proyecto al contenedor
COPY . .

# Ajusta los permisos del directorio storage
RUN chown -R www-data:www-data storage

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala las dependencias de Composer
RUN composer install --no-scripts --no-interaction 

# Genera la clave de la aplicación
RUN php artisan key:generate

# Ejecuta las migraciones y los seeders (si es necesario)
# RUN php artisan migrate --force

EXPOSE 80

CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "80"]

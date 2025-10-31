# Usar imagen base de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite de Apache (útil para futuras mejoras)
RUN a2enmod rewrite

# Copiar archivos de la aplicación al directorio web de Apache
COPY . /var/www/html/

# Establecer permisos adecuados
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exponer el puerto 80
EXPOSE 80

# El comando por defecto ya está definido en la imagen base (apache2-foreground)

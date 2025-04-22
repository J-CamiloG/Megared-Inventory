FROM php:8.2-apache

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    zlib1g-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql mysqli gd zip \
    && a2enmod rewrite

# Configurar Apache para usar el puerto asignado por Railway
ENV PORT=80
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Copiar archivos de la aplicación
COPY . /var/www/html/

# Establecer permisos adecuados
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configurar el directorio de trabajo
WORKDIR /var/www/html

# Comando de inicio (formato correcto)
CMD ["apache2-foreground"]
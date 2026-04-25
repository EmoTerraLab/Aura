FROM dunglas/frankenphp:php8.2-alpine

# Configurar variables de entorno recomendadas
ENV SERVER_NAME=":80"
ENV COMPOSER_ALLOW_SUPERUSER=1

# Instalar extensiones necesarias de PHP (ej. PDO SQLite)
# La imagen base ya trae pdo_sqlite en PHP 8.2 Alpine
# RUN install-php-extensions pdo_sqlite

# Copiar el código fuente a la imagen
COPY . /app

# Asegurar que FrankenPHP sirva la carpeta public/
WORKDIR /app/public

# Exponer el puerto
EXPOSE 80

# El comando de inicio predeterminado de FrankenPHP ya sirve el directorio actual

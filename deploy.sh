#!/bin/bash
# Script de despliegue - ejecutar en el servidor después de subir los archivos

set -e

echo "=== Iniciando despliegue de Buhooh ==="

# 1. Instalar dependencias PHP (sin dev)
echo "Instalando dependencias composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# 2. Copiar .env de producción
if [ ! -f .env ]; then
    cp .env.production .env
    echo "Archivo .env creado desde .env.production"
fi

# 3. Generar clave si no existe
php artisan key:generate --no-interaction

# 4. Correr migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force --no-interaction

# 5. Optimizar para producción
echo "Optimizando..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Permisos de storage y cache
echo "Configurando permisos..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# 7. Crear symlink de storage
php artisan storage:link

echo "=== Despliegue completado ==="

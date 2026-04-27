#!/bin/bash
SSH_USER="root"
SSH_HOST="94.143.140.112"
REMOTE_PATH="/var/www/app.aura.emoterralab.com"

echo "🚀 Iniciando despliegue completo en $SSH_HOST..."

# 1. Sincronizar archivos usando rsync (excluyendo lo innecesario)
echo "📤 Sincronizando archivos vía rsync..."
rsync -avz --progress \
  --exclude='.git' \
  --exclude='deploy.sh' \
  --exclude='database/*.sqlite*' \
  --exclude='database/*.sqlite-shm' \
  --exclude='database/*.sqlite-wal' \
  --exclude='storage/logs/*' \
  --exclude='vendor' \
  ./ root@$SSH_HOST:$REMOTE_PATH/

# 2. Ejecutar comandos remotos: Composer, Migraciones y Permisos
echo "⚙️  Ejecutando tareas post-despliegue en el servidor..."
ssh $SSH_USER@$SSH_HOST << EOF
  cd $REMOTE_PATH
  
  echo "📦 Instalando dependencias de Composer..."
  # Forzar uso de root para composer si es necesario, aunque no es recomendado
  export COMPOSER_ALLOW_SUPERUSER=1
  composer install --no-dev --optimize-autoloader
  
  echo "🗄️  Ejecutando migraciones de base de datos..."
  php migrate.php run
  
  echo "🔐 Asegurando permisos de escritura..."
  chown -R www-data:www-data $REMOTE_PATH/database $REMOTE_PATH/storage
  chmod -R 775 $REMOTE_PATH/database $REMOTE_PATH/storage
  
  # Si el archivo sqlite existe, asegurar permisos específicos
  if [ -f "$REMOTE_PATH/database/aura.sqlite" ]; then
    chown www-data:www-data $REMOTE_PATH/database/aura.sqlite
    chmod 664 $REMOTE_PATH/database/aura.sqlite
  fi

  echo "✅ Servidor actualizado y base de datos sincronizada."
EOF

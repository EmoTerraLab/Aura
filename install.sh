#!/bin/bash

# =============================================================================
# Aura - Unified Installer for Ubuntu/Debian (Apache + PHP + SQLite)
# =============================================================================

set -e

# Colores para la salida
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}🛡️  Iniciando instalación de Aura: Santuario Digital...${NC}"

# 1. Comprobar privilegios de root
if [ "$EUID" -ne 0 ]; then 
  echo -e "${RED}Error: Por favor, ejecuta el script con sudo o como root.${NC}"
  exit 1
fi

# 2. Actualizar sistema e instalar dependencias básicas
echo -e "${GREEN}📦 Actualizando repositorios e instalando dependencias...${NC}"
apt-get update
apt-get install -y apache2 php libapache2-mod-php php-sqlite3 php-mbstring php-curl php-xml php-zip sqlite3 git curl

# 3. Habilitar módulo Rewrite de Apache (Crítico para el MVC)
echo -e "${GREEN}⚙️  Configurando módulos de Apache...${NC}"
a2enmod rewrite

# 4. Configurar Directorio de la Aplicación
APP_PATH=$(pwd)
echo -e "${GREEN}📂 Configurando permisos en: $APP_PATH...${NC}"

# Asegurar que Apache pueda escribir en las carpetas de datos
chown -R www-data:www-data "$APP_PATH/database"
chown -R www-data:www-data "$APP_PATH/storage"
chmod -R 775 "$APP_PATH/database"
chmod -R 775 "$APP_PATH/storage"

# Si el archivo de base de datos no existe, crear un placeholder para que PHP pueda escribir en él
if [ ! -f "$APP_PATH/database/aura.sqlite" ]; then
    touch "$APP_PATH/database/aura.sqlite"
    chown www-data:www-data "$APP_PATH/database/aura.sqlite"
    chmod 664 "$APP_PATH/database/aura.sqlite"
fi

# 5. Crear archivo .htaccess en public/ si no existe (Redirección MVC)
if [ ! -f "$APP_PATH/public/.htaccess" ]; then
    echo -e "${GREEN}📝 Creando configuración de rutas (.htaccess)...${NC}"
    cat <<EOF > "$APP_PATH/public/.htaccess"
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [L,QSA]
</IfModule>
EOF
    chown www-data:www-data "$APP_PATH/public/.htaccess"
fi

# 6. Sugerencia de VirtualHost
echo -e "${BLUE}-----------------------------------------------------------${NC}"
echo -e "${BLUE}🚀 INSTALACIÓN BÁSICA COMPLETADA${NC}"
echo -e "${BLUE}-----------------------------------------------------------${NC}"
echo -e "Para que las rutas funcionen, asegúrate de que tu VirtualHost de Apache"
echo -e "apunte a la carpeta: ${GREEN}$APP_PATH/public${NC}"
echo -e ""
echo -e "Y que tenga activado el ${GREEN}AllowOverride All${NC}. Ejemplo:"
echo -e "<Directory $APP_PATH/public>"
echo -e "    AllowOverride All"
echo -e "    Require all granted"
echo -e "</Directory>"
echo -e "${BLUE}-----------------------------------------------------------${NC}"

# 7. Reiniciar Apache para aplicar cambios
echo -e "${GREEN}🔄 Reiniciando Apache...${NC}"
systemctl restart apache2

echo -e "${GREEN}✅ ¡Todo listo! Aura debería estar funcionando.${NC}"

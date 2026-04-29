#!/bin/bash
SSH_USER="root"
SSH_HOST="94.143.140.112"
REMOTE_PATH="/var/www/app.aura.emoterralab.com"

echo "🔓 Desactivando modo mantenimiento en $SSH_HOST..."
ssh $SSH_USER@$SSH_HOST "cd $REMOTE_PATH && php migrate.php maintenance:off"
echo "✅ Modo mantenimiento desactivado."

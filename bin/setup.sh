#!/bin/bash

sleep 2

# Verificar se WordPress já está instalado
if docker exec wordpress wp core is-installed --allow-root 2>/dev/null; then
    echo "> WordPress já está instalado"
else
    echo "> Instalando WordPress..."
    
    # Instalar WordPress
    docker exec wordpress wp core install \
        --url="http://localhost:8080" \
        --title="WP Associates" \
        --admin_user="admin" \
        --admin_password="admin" \
        --admin_email="admin@email.com" \
        --allow-root 2>/dev/null
fi

echo "> Removendo plugins padrão..."

# Remover plugins padrão
docker exec wordpress wp plugin delete hello akismet --allow-root 2>/dev/null

echo "> Instalando Elementor..."

# Instalar e ativar Elementor
docker exec wordpress wp plugin install elementor --activate --allow-root 2>/dev/null

echo "> Instalando Elementor Pro..."

# Instalar e ativar Elementor Pro
docker exec wordpress wp plugin install https://github.com/proelements/proelements/releases/download/v3.31.3/pro-elements.zip --activate --allow-root 2>/dev/null

echo "> Ativando plugin WP Associates..."

# Ativar plugin WP Associates
docker exec wordpress wp plugin activate wp-associates --allow-root 2>/dev/null

echo "> Instalando tema Hello Elementor..."

# Instalar e ativar tema Hello Elementor
docker exec wordpress wp theme install hello-elementor --activate --allow-root 2>/dev/null

# Deletar temas padrão não utilizados
docker exec wordpress wp theme delete twentytwentyone twentytwentytwo twentytwentythree twentytwentyfour --allow-root 2>/dev/null

echo ""
echo "Configuração concluída!"
echo ""
echo "URL:     http://localhost:8080"
echo "Admin:   http://localhost:8080/wp-admin"
echo ""
echo "Usuário: admin"
echo "Senha:   admin"
echo ""

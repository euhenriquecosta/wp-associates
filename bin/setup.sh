#!/bin/bash

sleep 2

echo "> Resetando banco de dados..."
docker exec wordpress wp db reset --yes --allow-root 2>/dev/null

echo "> Limpando uploads antigos..."
docker exec wordpress rm -rf /var/www/html/wp-content/uploads/* --allow-root 2>/dev/null
docker exec wordpress mkdir -p /var/www/html/wp-content/uploads --allow-root 2>/dev/null
docker exec wordpress chown -R www-data:www-data /var/www/html/wp-content/uploads --allow-root 2>/dev/null
docker exec wordpress chmod -R 755 /var/www/html/wp-content/uploads --allow-root 2>/dev/null

echo "> Verificando instalação do WordPress..."
if docker exec wordpress wp core is-installed --allow-root 2>/dev/null; then
    echo "> WordPress já está instalado"
else
    echo "> Instalando WordPress..."
    docker exec wordpress wp language core install pt_BR --activate --allow-root 2>/dev/null
    docker exec wordpress wp core install \
        --url="http://localhost:8080" \
        --title="WP Associates" \
        --admin_user="admin" \
        --admin_password="admin" \
        --admin_email="admin@email.com" \
        --allow-root 2>/dev/null
    docker exec wordpress wp site switch-language pt_BR --allow-root 2>/dev/null
fi

echo "> Ativando WP_DEBUG..."
docker exec wordpress wp config set WP_DEBUG true --raw --allow-root 2>/dev/null
docker exec wordpress wp config set WP_DEBUG_LOG true --raw --allow-root 2>/dev/null
docker exec wordpress wp config set WP_DEBUG_DISPLAY true --raw --allow-root 2>/dev/null

echo "> Limpando plugins e posts padrão..."
docker exec wordpress wp plugin delete hello akismet --allow-root 2>/dev/null
docker exec wordpress wp post delete $(docker exec wordpress wp post list --post_type=page --format=ids --allow-root 2>/dev/null) --force --allow-root 2>/dev/null
docker exec wordpress wp post delete $(docker exec wordpress wp post list --post_type=post --format=ids --allow-root 2>/dev/null) --force --allow-root 2>/dev/null

echo "> Instalando Elementor..."
docker exec wordpress wp plugin install elementor --activate --allow-root 2>/dev/null
docker exec wordpress wp option update elementor_onboarded 1 --allow-root 2>/dev/null
docker exec wordpress wp plugin install https://github.com/proelements/proelements/releases/download/v3.31.3/pro-elements.zip --activate --allow-root 2>/dev/null
docker exec wordpress wp option update elementor_pro_license_data '{"license_key":"activated"}' --format=json --allow-root 2>/dev/null

echo "> Instalando All-in-One WP Migration..."
docker exec wordpress wp plugin install all-in-one-wp-migration --activate --allow-root 2>/dev/null
docker exec wordpress mkdir -p /var/www/html/wp-content/ai1wm-backups --allow-root 2>/dev/null
docker exec wordpress mkdir -p /var/www/html/wp-content/plugins/all-in-one-wp-migration/storage --allow-root 2>/dev/null

echo "> Configurando permissões do All-in-One WP Migration..."
docker exec wordpress chown -R www-data:www-data /var/www/html/wp-content/ai1wm-backups --allow-root 2>/dev/null
docker exec wordpress chown -R www-data:www-data /var/www/html/wp-content/plugins/all-in-one-wp-migration --allow-root 2>/dev/null
docker exec wordpress chmod -R 777 /var/www/html/wp-content/ai1wm-backups --allow-root 2>/dev/null
docker exec wordpress chmod -R 777 /var/www/html/wp-content/plugins/all-in-one-wp-migration --allow-root 2>/dev/null

echo "> Configurando opções do All-in-One WP Migration..."
docker exec wordpress wp option update ai1wm_max_file_size 536870912 --allow-root 2>/dev/null
docker exec wordpress wp option update ai1wm_max_execution_time 300 --allow-root 2>/dev/null

echo "> Instalando File Manager..."
docker exec wordpress wp plugin install wp-file-manager --activate --allow-root 2>/dev/null

echo "> Instalando plugins dev (Query Monitor, User Switching)..."
docker exec wordpress wp plugin install query-monitor user-switching --activate --allow-root 2>/dev/null

echo "> Ativando plugin WP Associates..."
docker exec wordpress wp plugin activate wp-associates --allow-root 2>/dev/null

echo "> Instalando tema Hello Elementor..."
docker exec wordpress wp theme install hello-elementor --activate --allow-root 2>/dev/null
docker exec wordpress wp theme delete twentytwentyone twentytwentytwo twentytwentythree twentytwentyfour --allow-root 2>/dev/null

echo "> Configurando dashboard..."
docker exec wordpress wp user meta update 1 metaboxhidden_dashboard '["dashboard_site_health","dashboard_right_now","dashboard_activity","dashboard_quick_press","dashboard_primary","e-dashboard-overview","rpress_dashboard_sales"]' --format=json --allow-root 2>/dev/null
docker exec wordpress wp user meta update 1 show_welcome_panel 0 --allow-root 2>/dev/null
docker exec wordpress wp user meta update 1 admin_color midnight --allow-root 2>/dev/null

echo ""
echo "🚀 Configuração DEV concluída!"
echo "URL:     http://localhost:8080"
echo "Admin:   http://localhost:8080/wp-admin"
echo ""
echo "Usuário: admin"
echo "Senha:   admin"
echo ""
#!/bin/bash

sleep 2

echo "> Resetando banco de dados..."
docker exec wordpress wp db reset --yes --allow-root 2>/dev/null

echo "> Limpando e configurando uploads..."
docker exec wordpress rm -rf /var/www/html/wp-content/uploads/* 2>/dev/null
docker exec wordpress mkdir -p /var/www/html/wp-content/uploads/2024/01 2>/dev/null
docker exec wordpress mkdir -p /var/www/html/wp-content/uploads/2024/02 2>/dev/null
docker exec wordpress mkdir -p /var/www/html/wp-content/uploads/2024/03 2>/dev/null
docker exec wordpress chown -R www-data:www-data /var/www/html/wp-content/uploads 2>/dev/null
docker exec wordpress chmod -R 755 /var/www/html/wp-content/uploads 2>/dev/null

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

echo "> Reconfigurando permissões finais..."
docker exec wordpress chown -R www-data:www-data /var/www/html/wp-content 2>/dev/null
docker exec wordpress chmod -R 755 /var/www/html/wp-content 2>/dev/null

echo "> Configurando limites de upload do WordPress..."
docker exec wordpress wp config set WP_MEMORY_LIMIT 256M --allow-root 2>/dev/null
docker exec wordpress wp config set WP_MAX_MEMORY_LIMIT 512M --allow-root 2>/dev/null
docker exec wordpress wp config set UPLOAD_MAX_FILESIZE 64M --allow-root 2>/dev/null
docker exec wordpress wp config set POST_MAX_SIZE 64M --allow-root 2>/dev/null
docker exec wordpress wp config set MAX_EXECUTION_TIME 300 --allow-root 2>/dev/null

echo "> Configurando opções do All-in-One WP Migration..."
docker exec wordpress wp option update ai1wm_max_file_size 536870912 --allow-root 2>/dev/null
docker exec wordpress wp option update ai1wm_max_execution_time 300 --allow-root 2>/dev/null

echo "> Instalando Filester File Manager..."
docker exec wordpress wp plugin install filester --activate --allow-root 2>/dev/null

echo "> Instalando plugins dev (Query Monitor, User Switching)..."
docker exec wordpress wp plugin install query-monitor user-switching --activate --allow-root 2>/dev/null

echo "> Ativando plugin WP Associates..."
docker exec wordpress wp plugin activate wp-associates --allow-root 2>/dev/null

echo "> Ativando Advanced Custom Fields..."
docker exec wordpress wp plugin activate advanced-custom-fields --allow-root 2>/dev/null

echo "> Instalando tema Hello Elementor..."
docker exec wordpress wp theme install hello-elementor --activate --allow-root 2>/dev/null
docker exec wordpress wp theme delete twentytwentyone twentytwentytwo twentytwentythree twentytwentyfour --allow-root 2>/dev/null

echo "> Aplicando permissões corretas aos plugins..."
docker exec wordpress find /var/www/html/wp-content/plugins -type d -exec chmod 755 {} \; 2>/dev/null
docker exec wordpress find /var/www/html/wp-content/plugins -type f -exec chmod 644 {} \; 2>/dev/null
docker exec wordpress chown -R www-data:www-data /var/www/html/wp-content/plugins 2>/dev/null

echo "> Reaplicando permissões específicas do All-in-One WP Migration..."
docker exec wordpress chown -R www-data:www-data /var/www/html/wp-content/ai1wm-backups 2>/dev/null
docker exec wordpress chown -R www-data:www-data /var/www/html/wp-content/plugins/all-in-one-wp-migration 2>/dev/null
docker exec wordpress chmod -R 0777 /var/www/html/wp-content/ai1wm-backups 2>/dev/null
docker exec wordpress chmod -R 0777 /var/www/html/wp-content/plugins/all-in-one-wp-migration 2>/dev/null

echo "> Configurando dashboard..."
docker exec wordpress wp user meta update 1 metaboxhidden_dashboard '["dashboard_site_health","dashboard_right_now","dashboard_activity","dashboard_quick_press","dashboard_primary","e-dashboard-overview","rpress_dashboard_sales", "dashboard_widget"]' --format=json --allow-root 2>/dev/null
docker exec wordpress wp user meta update 1 show_welcome_panel 0 --allow-root 2>/dev/null
docker exec wordpress wp user meta update 1 admin_color midnight --allow-root 2>/dev/null

echo "> Configurando permissões do usuário admin..."
docker exec wordpress wp user add-role 1 administrator --allow-root 2>/dev/null
docker exec wordpress wp user add-cap 1 upload_files --allow-root 2>/dev/null
docker exec wordpress wp user add-cap 1 edit_posts --allow-root 2>/dev/null
docker exec wordpress wp user add-cap 1 edit_pages --allow-root 2>/dev/null
docker exec wordpress wp user add-cap 1 edit_others_posts --allow-root 2>/dev/null
docker exec wordpress wp user add-cap 1 edit_others_pages --allow-root 2>/dev/null
docker exec wordpress wp user add-cap 1 publish_posts --allow-root 2>/dev/null
docker exec wordpress wp user add-cap 1 publish_pages --allow-root 2>/dev/null
docker exec wordpress wp user add-cap 1 manage_options --allow-root 2>/dev/null

echo "> Configurando opções de mídia..."
docker exec wordpress wp option update uploads_use_yearmonth_folders 0 --allow-root 2>/dev/null
docker exec wordpress wp option update thumbnail_size_w 150 --allow-root 2>/dev/null
docker exec wordpress wp option update thumbnail_size_h 150 --allow-root 2>/dev/null
docker exec wordpress wp option update medium_size_w 300 --allow-root 2>/dev/null
docker exec wordpress wp option update medium_size_h 300 --allow-root 2>/dev/null
docker exec wordpress wp option update large_size_w 1024 --allow-root 2>/dev/null
docker exec wordpress wp option update large_size_h 1024 --allow-root 2>/dev/null

echo "> Testando upload de arquivo..."
docker exec wordpress touch /var/www/html/wp-content/uploads/test-upload.txt 2>/dev/null
if [ $? -eq 0 ]; then
    echo "✅ Upload testado com sucesso!"
    docker exec wordpress rm /var/www/html/wp-content/uploads/test-upload.txt 2>/dev/null
else
    echo "❌ Erro no teste de upload - verificando permissões..."
    docker exec wordpress ls -la /var/www/html/wp-content/uploads/ 2>/dev/null
fi

echo "> Verificando configurações PHP..."
docker exec wordpress php -r "echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . PHP_EOL; echo 'post_max_size: ' . ini_get('post_max_size') . PHP_EOL; echo 'memory_limit: ' . ini_get('memory_limit') . PHP_EOL;" 2>/dev/null

echo "> Verificando permissões do usuário admin..."
docker exec wordpress wp user get 1 --field=roles 2>/dev/null
docker exec wordpress wp user list-caps 1 2>/dev/null | grep -E "(upload_files|edit_posts|manage_options)" || echo "Capacidades não encontradas"

echo ""
echo "🚀 Configuração DEV concluída!"
echo "URL:     http://localhost:8080"
echo "Admin:   http://localhost:8080/wp-admin"
echo ""
echo "Usuário: admin"
echo "Senha:   admin"
echo ""
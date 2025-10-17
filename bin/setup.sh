#!/bin/bash

sleep 2

echo "> Buildando plugin..."
bin/build.sh

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

echo "> Ativando upload de templates no Elementor..."
docker exec wordpress wp option update elementor_allow_svg 1 --allow-root 2>/dev/null
docker exec wordpress wp option update elementor_unfiltered_files_upload 1 --allow-root 2>/dev/null
docker exec wordpress wp option update elementor_upload_file_types '["json","xml","zip"]' --format=json --allow-root 2>/dev/null
docker exec wordpress wp option update elementor_editor_upgrade_notice 0 --allow-root 2>/dev/null
docker exec wordpress wp option update elementor_editor_break_lines 1 --allow-root 2>/dev/null
docker exec wordpress wp option update elementor_disable_color_schemes 1 --allow-root 2>/dev/null
docker exec wordpress wp option update elementor_disable_typography_schemes 1 --allow-root 2>/dev/null

echo "> Desabilitando avisos de segurança do Elementor..."
docker exec wordpress wp option update elementor_disable_json_upload_warning 1 --allow-root 2>/dev/null
docker exec wordpress wp option update elementor_disable_json_upload_restriction 1 --allow-root 2>/dev/null
docker exec wordpress wp option update elementor_json_upload_warning_dismissed 1 --allow-root 2>/dev/null
docker exec wordpress wp option update elementor_safe_mode 0 --allow-root 2>/dev/null

echo "> Criando associados de teste..."
# Criar associado 1
ASSOCIATE1_ID=$(docker exec wordpress wp post create --post_type=associate --post_title="João Silva" --post_status=publish --allow-root --porcelain 2>/dev/null)
if [ ! -z "$ASSOCIATE1_ID" ]; then
    echo "> Associado 1 criado com ID: $ASSOCIATE1_ID"
    docker exec wordpress wp post meta set $ASSOCIATE1_ID _wpa_description "Produtor de queijo artesanal com mais de 20 anos de experiência na região de Acajutiba." --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE1_ID _wpa_municipality "Acajutiba" --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE1_ID _wpa_latitude "-11.6575" --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE1_ID _wpa_longitude "-38.0078" --allow-root 2>/dev/null
fi

# Criar associado 2
ASSOCIATE2_ID=$(docker exec wordpress wp post create --post_type=associate --post_title="Maria Santos" --post_status=publish --allow-root --porcelain 2>/dev/null)
if [ ! -z "$ASSOCIATE2_ID" ]; then
    echo "> Associado 2 criado com ID: $ASSOCIATE2_ID"
    docker exec wordpress wp post meta set $ASSOCIATE2_ID _wpa_description "Especialista em queijo coalho e derivados lácteos tradicionais da Bahia." --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE2_ID _wpa_municipality "Inhambupe" --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE2_ID _wpa_latitude "-11.7844" --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE2_ID _wpa_longitude "-37.1867" --allow-root 2>/dev/null
fi

# Criar associado 3
ASSOCIATE3_ID=$(docker exec wordpress wp post create --post_type=associate --post_title="Pedro Oliveira" --post_status=publish --allow-root --porcelain 2>/dev/null)
if [ ! -z "$ASSOCIATE3_ID" ]; then
    echo "> Associado 3 criado com ID: $ASSOCIATE3_ID"
    docker exec wordpress wp post meta set $ASSOCIATE3_ID _wpa_description "Produtor familiar de queijo de coalho e manteiga artesanal." --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE3_ID _wpa_municipality "Esplanada" --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE3_ID _wpa_latitude "-11.7961" --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE3_ID _wpa_longitude "-37.9450" --allow-root 2>/dev/null
fi

# Criar associado 4
ASSOCIATE4_ID=$(docker exec wordpress wp post create --post_type=associate --post_title="Ana Costa" --post_status=publish --allow-root --porcelain 2>/dev/null)
if [ ! -z "$ASSOCIATE4_ID" ]; then
    echo "> Associado 4 criado com ID: $ASSOCIATE4_ID"
    docker exec wordpress wp post meta set $ASSOCIATE4_ID _wpa_description "Cooperativa de produtores de queijo artesanal da região de Entre Rios." --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE4_ID _wpa_municipality "Entre Rios" --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE4_ID _wpa_latitude "-11.9419" --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE4_ID _wpa_longitude "-38.0819" --allow-root 2>/dev/null
fi

# Criar associado 5
ASSOCIATE5_ID=$(docker exec wordpress wp post create --post_type=associate --post_title="Carlos Mendes" --post_status=publish --allow-root --porcelain 2>/dev/null)
if [ ! -z "$ASSOCIATE5_ID" ]; then
    echo "> Associado 5 criado com ID: $ASSOCIATE5_ID"
    docker exec wordpress wp post meta set $ASSOCIATE5_ID _wpa_description "Produtor de queijo de coalho com certificação de qualidade e tradição familiar." --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE5_ID _wpa_municipality "Aporá" --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE5_ID _wpa_latitude "-11.6575" --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $ASSOCIATE5_ID _wpa_longitude "-38.0078" --allow-root 2>/dev/null
fi

echo "> Criando categorias de associados..."
# Criar categorias
CATEGORIA1_ID=$(docker exec wordpress wp term create associate_category "Produtores Artesanais" --description="Produtores de queijo artesanal tradicional" --allow-root --porcelain 2>/dev/null)
CATEGORIA2_ID=$(docker exec wordpress wp term create associate_category "Cooperativas" --description="Cooperativas de produtores" --allow-root --porcelain 2>/dev/null)
CATEGORIA3_ID=$(docker exec wordpress wp term create associate_category "Família" --description="Produtores familiares" --allow-root --porcelain 2>/dev/null)

# Associar categorias aos associados
if [ ! -z "$ASSOCIATE1_ID" ] && [ ! -z "$CATEGORIA1_ID" ]; then
    docker exec wordpress wp post term set $ASSOCIATE1_ID associate_category $CATEGORIA1_ID --allow-root 2>/dev/null
fi
if [ ! -z "$ASSOCIATE2_ID" ] && [ ! -z "$CATEGORIA1_ID" ]; then
    docker exec wordpress wp post term set $ASSOCIATE2_ID associate_category $CATEGORIA1_ID --allow-root 2>/dev/null
fi
if [ ! -z "$ASSOCIATE3_ID" ] && [ ! -z "$CATEGORIA3_ID" ]; then
    docker exec wordpress wp post term set $ASSOCIATE3_ID associate_category $CATEGORIA3_ID --allow-root 2>/dev/null
fi
if [ ! -z "$ASSOCIATE4_ID" ] && [ ! -z "$CATEGORIA2_ID" ]; then
    docker exec wordpress wp post term set $ASSOCIATE4_ID associate_category $CATEGORIA2_ID --allow-root 2>/dev/null
fi
if [ ! -z "$ASSOCIATE5_ID" ] && [ ! -z "$CATEGORIA1_ID" ]; then
    docker exec wordpress wp post term set $ASSOCIATE5_ID associate_category $CATEGORIA1_ID --allow-root 2>/dev/null
fi

echo "> Criando página de teste com shortcode..."
# Criar página de teste
TEST_PAGE_ID=$(docker exec wordpress wp post create --post_type=page --post_title="Associados - Teste" --post_name="associados-teste" --post_status=publish --post_content="[wp-associates]" --allow-root --porcelain 2>/dev/null)
if [ ! -z "$TEST_PAGE_ID" ]; then
    echo "> Página de teste criada com ID: $TEST_PAGE_ID"
    echo "> URL da página: http://localhost:8080/associados-teste"
fi

echo "> Ativando Advanced Custom Fields..."
docker exec wordpress wp plugin activate advanced-custom-fields --allow-root 2>/dev/null

echo "> Instalando tema Hello Elementor..."
docker exec wordpress wp theme install hello-elementor --activate --allow-root 2>/dev/null
docker exec wordpress wp theme delete twentytwentyone twentytwentytwo twentytwentythree twentytwentyfour --allow-root 2>/dev/null

echo "> Criando Landing Page..."
# Criar a página
PAGE_ID=$(docker exec wordpress wp post create --post_type=page --post_title="Associação Queijo Baiano" --post_name="associacao-queijo-baiano" --post_status=publish --post_content="" --allow-root --porcelain 2>/dev/null)

if [ ! -z "$PAGE_ID" ]; then
    echo "> Página criada com ID: $PAGE_ID"
    
    # Aguardar Elementor carregar completamente
    sleep 5
    
    echo "> Configurando página como Elementor Canvas..."
    # Configurar como Elementor Canvas
    docker exec wordpress wp post meta set $PAGE_ID _elementor_template_type page --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $PAGE_ID _elementor_edit_mode builder --allow-root 2>/dev/null
    docker exec wordpress wp post meta set $PAGE_ID _elementor_template_type page --allow-root 2>/dev/null
    
    echo "> Importando template da Landing Page..."
    # Verificar se o arquivo de template existe
    if docker exec wordpress test -f /var/www/html/wp-content/plugins/wp-associates/templates/lp.json; then
        echo "   ✅ Arquivo lp.json encontrado"
        # Tentar importar template via CLI
        docker exec wordpress wp elementor import /var/www/html/wp-content/plugins/wp-associates/templates/lp.json --allow-root 2>/dev/null
        if [ $? -eq 0 ]; then
            echo "   ✅ Template importado com sucesso"
            
            # Aguardar um pouco para o template ser processado
            sleep 3
            
            # Tentar aplicar o template automaticamente
            echo "> Aplicando template à página automaticamente..."
            # Buscar o ID do template importado
            TEMPLATE_ID=$(docker exec wordpress wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=page --format=ids --orderby=date --order=DESC --posts_per_page=1 --allow-root 2>/dev/null | head -1)
            
            if [ ! -z "$TEMPLATE_ID" ]; then
                echo "   ✅ Template encontrado com ID: $TEMPLATE_ID"
                # Aplicar template à página
                docker exec wordpress wp post meta set $PAGE_ID _elementor_template_id $TEMPLATE_ID --allow-root 2>/dev/null
                docker exec wordpress wp post meta set $PAGE_ID _elementor_edit_mode builder --allow-root 2>/dev/null
                docker exec wordpress wp post meta set $PAGE_ID _elementor_template_type page --allow-root 2>/dev/null
                docker exec wordpress wp post meta set $PAGE_ID _elementor_data '[]' --format=json --allow-root 2>/dev/null
                echo "   ✅ Template aplicado à página automaticamente"
                echo "   🌐 Página pronta: http://localhost:8080/associacao-queijo-baiano"
            else
                echo "   ⚠️  Template não encontrado - será necessário aplicar manualmente"
            fi
        else
            echo "   ⚠️  Importação automática falhou - será necessário importar manualmente"
        fi
    else
        echo "   ⚠️  Arquivo lp.json não encontrado"
    fi
    
    echo "> Instruções para aplicação manual (se necessário):"
    echo "   1. Acesse: http://localhost:8080/wp-admin/post.php?post=$PAGE_ID&action=elementor"
    echo "   2. Clique em 'Import Template'"
    echo "   3. Selecione o arquivo lp.json"
    echo "   4. Clique em 'Insert' para aplicar"
else
    echo "❌ Erro ao criar a página"
fi

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
echo "🔍 Verificando configuração final..."

# Verificar plugin
PLUGIN_STATUS=$(docker exec wordpress wp plugin list --allow-root 2>/dev/null | grep wp-associates)
if echo "$PLUGIN_STATUS" | grep -q "active"; then
    echo "✅ Plugin WP Associates ativo"
else
    echo "❌ Plugin não está ativo"
fi

# Verificar associados
ASSOCIATES_COUNT=$(docker exec wordpress wp post list --post_type=associate --format=count --allow-root 2>/dev/null)
echo "✅ $ASSOCIATES_COUNT associados criados"

# Verificar página de teste
TEST_PAGE=$(docker exec wordpress wp post list --post_type=page --name=associados-teste --format=count --allow-root 2>/dev/null)
if [ "$TEST_PAGE" -gt 0 ]; then
    echo "✅ Página de teste criada"
else
    echo "⚠️  Página de teste não encontrada"
fi

echo ""
echo "🚀 Configuração DEV concluída!"
echo "URL:     http://localhost:8080"
echo "Admin:   http://localhost:8080/wp-admin"
echo "Teste:   http://localhost:8080/associados-teste"
echo ""
echo "👤 Usuário: admin"
echo "🔑 Senha:   admin"
echo ""
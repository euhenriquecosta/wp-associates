<?php
/**
 * Plugin Name: Associados Interativo
 * Description: Plugin para registrar associados com nome, localização, imagem e filtros interativos com mapa.
 * Version: 2.1
 * Author: Seu Nome
 */

if (!defined('ABSPATH')) exit;

/**
 * 1) Registrar post type 'associado'
 */
function ai_register_post_type() {
    register_post_type('associado', array(
        'labels' => array(
            'name' => 'Associados',
            'singular_name' => 'Associado',
            'add_new' => 'Adicionar Novo',
            'add_new_item' => 'Adicionar Novo Associado',
            'edit_item' => 'Editar Associado',
            'new_item' => 'Novo Associado',
            'view_item' => 'Ver Associado',
            'search_items' => 'Buscar Associados',
            'not_found' => 'Nenhum associado encontrado',
        ),
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-groups',
        'supports' => array('title', 'thumbnail'),
        'show_in_rest' => true,
        'rewrite' => false,
        'publicly_queryable' => false,
    ));
}
add_action('init', 'ai_register_post_type', 0);
/**
 * 2) Registrar taxonomy 'associado_categoria' e criar termos padrão (se não existirem)
 */
function ai_register_taxonomy_and_terms() {
    register_taxonomy('associado_categoria', 'associado', array(
        'labels' => array(
            'name' => 'Categorias de Associado',
            'singular_name' => 'Categoria de Associado',
        ),
        'hierarchical' => false,
        'show_ui' => true,
        'show_in_rest' => true,
    ));

    $terms = array(
        'Amante de queijo','Chef de cozinha','Consultor','Cooperativa','Curador',
        'Leite Cru','Leite de Búfala','Leite de Cabra','Leite de Ovelha','Leite de Vaca',
        'Leite Pasteurizado','Pesquisador','Produtor','Queijista','Técnico em Laticínios',
        'Todos os Associados','Todos os Tipos de Ator','Todos os Tipos de Leite',
        'Todos os Tipos de Queijo','Todos os Tratamentos Térmicos'
    );

    foreach ($terms as $t) {
        if (!term_exists($t, 'associado_categoria')) {
            wp_insert_term($t, 'associado_categoria');
        }
    }
}
add_action('init', 'ai_register_taxonomy_and_terms', 5);

/**
 * 3) Metabox para infos: função, endereço, estado, latitude, longitude
 */
function ai_add_metabox() {
    add_meta_box('ai_info', 'Informações do Associado', 'ai_metabox_callback', 'associado', 'normal', 'default');
}
add_action('add_meta_boxes', 'ai_add_metabox');

function ai_metabox_callback($post) {
    $funcao = get_post_meta($post->ID, '_ai_funcao', true);
    $local = get_post_meta($post->ID, '_ai_localizacao', true);
    $numero = get_post_meta($post->ID, '_ai_numero', true); // novo campo
    $estado = get_post_meta($post->ID, '_ai_estado', true);
    $lat = get_post_meta($post->ID, '_ai_latitude', true);
    $lng = get_post_meta($post->ID, '_ai_longitude', true);

    wp_nonce_field('ai_save_meta', 'ai_nonce');

    echo '<p><label><strong>Função</strong></label><br/><input type="text" name="ai_funcao" value="'.esc_attr($funcao).'" style="width:100%"></p>';

    echo '<p><label><strong>Buscar Localização</strong></label><br/>
        <input type="text" id="ai_search_place" placeholder="Digite para buscar..." style="width:100%" value="'.esc_attr($local).'">
        <small>Digite o nome do lugar e selecione uma sugestão</small>
    </p>';

    echo '<p><label><strong>Número</strong></label><br/>
        <input type="text" name="ai_numero" value="'.esc_attr($numero).'" style="width:100%">
    </p>';

    echo '<input type="hidden" name="ai_local" id="ai_local" value="'.esc_attr($local).'">';
    echo '<input type="hidden" name="ai_lat" id="ai_lat" value="'.esc_attr($lat).'">';
    echo '<input type="hidden" name="ai_lng" id="ai_lng" value="'.esc_attr($lng).'">';

    // Select de estados
    $estados = array(
        ''=>'-- Selecione o Estado --',
        'AC'=>'Acre','AL'=>'Alagoas','AP'=>'Amapá','AM'=>'Amazonas','BA'=>'Bahia','CE'=>'Ceará','DF'=>'Distrito Federal','ES'=>'Espírito Santo',
        'GO'=>'Goiás','MA'=>'Maranhão','MG'=>'Minas Gerais','MS'=>'Mato Grosso do Sul','MT'=>'Mato Grosso','PA'=>'Pará','PB'=>'Paraíba',
        'PE'=>'Pernambuco','PI'=>'Piauí','PR'=>'Paraná','RJ'=>'Rio de Janeiro','RN'=>'Rio Grande do Norte','RO'=>'Rondônia','RR'=>'Roraima',
        'RS'=>'Rio Grande do Sul','SC'=>'Santa Catarina','SE'=>'Sergipe','SP'=>'São Paulo','TO'=>'Tocantins'
    );

    echo '<p><label><strong>Estado</strong></label><br/><select name="ai_estado" style="width:100%">';
    foreach ($estados as $sigla => $nome) {
        $sel = ($estado === $sigla) ? 'selected' : '';
        echo '<option value="'.esc_attr($sigla).'" '.$sel.'>'.esc_html($nome).'</option>';
    }
    echo '</select></p>';
}


function ai_save_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['ai_nonce']) || !wp_verify_nonce($_POST['ai_nonce'], 'ai_save_meta')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['ai_funcao'])) update_post_meta($post_id, '_ai_funcao', sanitize_text_field($_POST['ai_funcao']));
    if (isset($_POST['ai_local'])) update_post_meta($post_id, '_ai_localizacao', sanitize_text_field($_POST['ai_local']));
    if (isset($_POST['ai_numero'])) update_post_meta($post_id, '_ai_numero', sanitize_text_field($_POST['ai_numero'])); // salvar número
    if (isset($_POST['ai_estado'])) update_post_meta($post_id, '_ai_estado', sanitize_text_field($_POST['ai_estado']));
    if (isset($_POST['ai_lat'])) update_post_meta($post_id, '_ai_latitude', sanitize_text_field($_POST['ai_lat']));
    if (isset($_POST['ai_lng'])) update_post_meta($post_id, '_ai_longitude', sanitize_text_field($_POST['ai_lng']));
}
add_action('save_post', 'ai_save_meta');
/**
 * 4) Enfileirar scripts e estilos (Leaflet + CSS do plugin)
 */
function ai_enqueue_scripts() {
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);

    // Usar filemtime para versionar e evitar cache
    $css_version = filemtime(plugin_dir_path(__FILE__) . 'styles.css');
    wp_register_style('associados-css', plugin_dir_url(__FILE__) . 'styles.css', array(), $css_version);
    wp_enqueue_style('associados-css');
}
add_action('wp_enqueue_scripts', 'ai_enqueue_scripts');

/**
 * 
 * 6) Autocomplete para busca de localização
 */
function ai_admin_enqueue($hook) {
    global $post;
    if (($hook == 'post-new.php' || $hook == 'post.php') && get_post_type($post) === 'associado') {
        // Usar filemtime para versionar e evitar cache
        $js_version = filemtime(plugin_dir_path(__FILE__) . 'script.js');
        wp_enqueue_script('ai-autocomplete', plugin_dir_url(__FILE__).'script.js', array('jquery'), $js_version, true);
    }
}
add_action('admin_enqueue_scripts', 'ai_admin_enqueue');

/**
 * 
 * 6) Shortcode [associados_interativo]
 */
function ai_associados_shortcode($atts) {
    ob_start();

    // Query: todos os associados
    $query = new WP_Query(array('post_type' => 'associado','posts_per_page' => -1));

    // Buscamos as categorias disponíveis (taxonomy)
    $terms = get_terms(array('taxonomy' => 'associado_categoria', 'hide_empty' => false));

    ?>
    <div class="ai-wrapper">
        <div class="associados-filtros">
            <input type="text" id="ai-busca-associado" placeholder="Buscar por nome ou função">

            <select id="ai-estado-associado">
                <option value="">Todos os Estados</option>
                <option value="AC">Acre</option><option value="AL">Alagoas</option><option value="AP">Amapá</option><option value="AM">Amazonas</option>
                <option value="BA">Bahia</option><option value="CE">Ceará</option><option value="DF">Distrito Federal</option><option value="ES">Espírito Santo</option>
                <option value="GO">Goiás</option><option value="MA">Maranhão</option><option value="MG">Minas Gerais</option><option value="MS">Mato Grosso do Sul</option>
                <option value="MT">Mato Grosso</option><option value="PA">Pará</option><option value="PB">Paraíba</option><option value="PE">Pernambuco</option>
                <option value="PI">Piauí</option><option value="PR">Paraná</option><option value="RJ">Rio de Janeiro</option><option value="RN">Rio Grande do Norte</option>
                <option value="RO">Rondônia</option><option value="RR">Roraima</option><option value="RS">Rio Grande do Sul</option><option value="SC">Santa Catarina</option>
                <option value="SE">Sergipe</option><option value="SP">São Paulo</option><option value="TO">Tocantins</option>
            </select>

            <select id="ai-categoria-associado">
                <option value="">Todas as Categorias</option>
                <?php
                    if (!is_wp_error($terms) && !empty($terms)) {
                        foreach ($terms as $t) {
                            echo '<option value="'.esc_attr($t->term_id).'">'.esc_html($t->name).'</option>';
                        }
                    }
                ?>
            </select>

            <button id="ai-filtrar-associados">Filtrar</button>
        </div>

        <div class="ai-div-pai">
            <div id="associados-container" class="associados-list">
                <?php while ($query->have_posts()) : $query->the_post();
                    $local = get_post_meta(get_the_ID(), '_ai_localizacao', true);
                    $numero = get_post_meta(get_the_ID(), '_ai_numero', true); // pega o número
                    $estado = get_post_meta(get_the_ID(), '_ai_estado', true);
                    $lat = get_post_meta(get_the_ID(), '_ai_latitude', true);
                    $lng = get_post_meta(get_the_ID(), '_ai_longitude', true);
                    $funcao = get_post_meta(get_the_ID(), '_ai_funcao', true);
                    $image = get_the_post_thumbnail(get_the_ID(), 'medium');
                    $terms_assoc = wp_get_post_terms(get_the_ID(), 'associado_categoria', array('fields'=>'ids'));
                    $terms_assoc_json = esc_attr(json_encode($terms_assoc));

                    // Monta endereço formatado
                    $endereco = trim($local . ($numero ? ', '.$numero : '') . ($estado ? ' - '.$estado : ''));
                ?>
                <div class="associado" data-lat="<?php echo esc_attr($lat); ?>" data-lng="<?php echo esc_attr($lng); ?>"
                     data-nome="<?php echo esc_attr(get_the_title()); ?>" data-funcao="<?php echo esc_attr($funcao); ?>"
                     data-local="<?php echo esc_attr($local); ?>" data-numero="<?php echo esc_attr($numero); ?>"
                     data-estado="<?php echo esc_attr($estado); ?>" data-cats='<?php echo $terms_assoc_json; ?>'>
                    <div class="associado-thumb">
                        <?php
                            if ($image) echo $image;
                            else echo '<img src="'.esc_url(plugins_url('placeholder.png', __FILE__)).'" alt="sem imagem" />';
                        ?>
                    </div>
                    <h3><?php the_title(); ?></h3>
                    <p class="ai-funcao"><?php echo esc_html($funcao); ?></p>
                    <p class="ai-local"><?php echo esc_html($endereco); ?></p>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <div id="map" ></div>
        </div>
    </div>

    <script>
    (function(){
        // roda quando DOM pronto
        document.addEventListener('DOMContentLoaded', function(){
            // init map - centraliza no Brasil
            var map = L.map('map').setView([-14.2, -51.9], 4);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a>'
            }).addTo(map);

            var markers = [];
            var markerGroup = L.layerGroup().addTo(map);

            function createDivIconFromImage(imgHtml){
                var html = '<div class="ai-marker-icon">'+ imgHtml +'</div>';
                return L.divIcon({
                    html: html,
                    className: 'ai-marker-wrapper',
                    iconSize: [56,56],
                    iconAnchor: [28,56],
                    popupAnchor: [0,-56]
                });
            }

     

            document.querySelectorAll('.associado').forEach(function(el){
                var lat = parseFloat(el.dataset.lat);
                var lng = parseFloat(el.dataset.lng);
                var nome = el.dataset.nome || '';
                var funcao = el.dataset.funcao || '';
                var local = el.dataset.local || '';
                var numero = el.dataset.numero || '';
                var estado = el.dataset.estado || '';
                var cats = [];
                try { cats = JSON.parse(el.dataset.cats); } catch(e){ cats = []; }

                // Monta endereço formatado
                var endereco = local;
                if (numero) endereco += ', Nº' + numero;
                if (estado) endereco += ' - ' + estado;

                var img = el.querySelector('img');
                var imgOuter = img ? img.outerHTML : '<div class="ai-noimg">?</div>';

                if (!isNaN(lat) && !isNaN(lng)) {
                    var icon = createDivIconFromImage(imgOuter);

                    var marker = L.marker([lat,lng], {icon: icon});
                    var popupHtml = '<div class="ai-popup">'+
                        (img ? imgOuter : '') +
                        '<h4 style="margin:8px 0 4px;">'+ nome +'</h4>' +
                        '<div class="ai-popup-funcao">'+ funcao +'</div>' +
                        '<div class="ai-popup-local">'+ endereco +'</div>' +
                        '</div>';
                    marker.bindPopup(popupHtml);
                    marker.addTo(markerGroup);

                    markers.push({el: el, marker: marker, lat: lat, lng: lng, cats: cats, estado: estado, nome: nome, funcao: funcao});
                } else {
                    markers.push({el: el, marker: null, lat: null, lng: null, cats: cats, estado: estado, nome: nome, funcao: funcao});
                }
            });

            // funcoes utilitárias
            function deg2rad(deg){ return deg * (Math.PI/180); }
            function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2){
                var R=6371;
                var dLat=deg2rad(lat2-lat1);
                var dLon=deg2rad(lon2-lon1);
                var a=Math.sin(dLat/2)*Math.sin(dLat/2)+Math.cos(deg2rad(lat1))*Math.cos(deg2rad(lat2))*Math.sin(dLon/2)*Math.sin(dLon/2);
                var c=2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
                return R*c;
            }

            // filtrar - aplica filtros visuais e no mapa
            function applyFilters(){
                var busca = (document.getElementById('ai-busca-associado').value || '').toLowerCase();
                var estado = document.getElementById('ai-estado-associado').value;
                var categoria = document.getElementById('ai-categoria-associado').value; // term_id ou ''
                var raio = null; // não usamos raio por enquanto (pediu estado select)

                // limpar group
                markerGroup.clearLayers();

                markers.forEach(function(m){
                    var show = true;

                    // busca por nome + funcao
                    var hay = (m.nome + ' ' + (m.funcao||'')).toLowerCase();
                    if (busca && hay.indexOf(busca) === -1) show = false;

                    // estado
                    if (estado && m.estado !== estado) show = false;

                    // categoria (m.cats é array de term_ids)
                    if (categoria) {
                        var catNum = parseInt(categoria,10);
                        if (!m.cats || m.cats.indexOf(catNum) === -1) show = false;
                    }

                    // aplicar visibilidade na lista
                    if (show) {
                        m.el.style.display = 'block';
                        if (m.marker) markerGroup.addLayer(m.marker);
                    } else {
                        m.el.style.display = 'none';
                        // marker não adicionado ao group => sumirá do mapa
                    }
                });

                // Se houver marcadores no mapa, ajusta bounds
                var allMarkers = [];
                markerGroup.eachLayer(function(l){ allMarkers.push(l.getLatLng()); });
                if (allMarkers.length > 0) {
                    var bounds = L.latLngBounds(allMarkers);
                    map.fitBounds(bounds.pad(0.25));
                } else {
                    // se nenhum marker visível, centraliza no Brasil
                    map.setView([-14.2, -51.9], 4);
                }
            }

            // eventos: em tempo real (change) e botão
            document.getElementById('ai-busca-associado').addEventListener('input', applyFilters);
            document.getElementById('ai-estado-associado').addEventListener('change', applyFilters);
            document.getElementById('ai-categoria-associado').addEventListener('change', applyFilters);
            document.getElementById('ai-filtrar-associados').addEventListener('click', applyFilters);

            // aplicar inicialmente (mostra todos)
            applyFilters();

            // clicar no card centraliza/puxa popup
            document.querySelectorAll('.associado').forEach(function(card){
                card.addEventListener('click', function(){
                    // encontrar marker associado
                    var nome = card.dataset.nome;
                    var found = markers.find(function(m){ return m.nome === nome && m.marker; });
                    if (found && found.marker) {
                        map.setView(found.marker.getLatLng(), 12);
                        found.marker.openPopup();
                    }
                });
            });

        });
    })();
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('associados_interativo', 'ai_associados_shortcode');

<?php
/**
 * Plugin Name: WP Associates
 * Description: Plugin para registrar associados com nome, localização, imagem e filtros interativos com mapa.
 * Version: 2.3
 * Author: Henrique Costa
 */

if (!defined('ABSPATH')) exit;

/**
 * 1) Registrar post type 'associate'
 */
function associates_register_post_type() {
    register_post_type('associate', array(
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
add_action('init', 'associates_register_post_type', 0);
/**
 * 2) Registrar taxonomy 'associado_categoria' e criar termos padrão (se não existirem)
 */
function associates_register_taxonomy_and_terms() {
    register_taxonomy('associate_category', 'associate', array(
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
        if (!term_exists($t, 'associate_category')) {
            wp_insert_term($t, 'associate_category');
        }
    }
}
add_action('init', 'associates_register_taxonomy_and_terms', 5);

/**
 * 3) Metabox para infos: função, endereço, latitude, longitude (estado fixo como Bahia)
 */
function associates_add_metabox() {
    add_meta_box('associates_info', 'Informações do Associado', 'associates_metabox_callback', 'associate', 'normal', 'default');
}
add_action('add_meta_boxes', 'associates_add_metabox');

function associates_metabox_callback($post) {
    $description = get_post_meta($post->ID, '_wpa_description', true);
    $location = get_post_meta($post->ID, '_wpa_location', true);
    $number = get_post_meta($post->ID, '_wpa_number', true); // novo campo
    $state = 'BA'; 
    $lat = get_post_meta($post->ID, '_wpa_latitude', true);
    $lng = get_post_meta($post->ID, '_wpa_longitude', true);

    wp_nonce_field('associates_save_meta', 'associates_nonce');

    echo '<p><label><strong>Descrição</strong></label><br/><input type="text" name="associates_description" value="'.esc_attr($description).'" style="width:100%"></p>';

    echo '<p><label><strong>Buscar Localização</strong></label><br/>
        <input type="text" id="associates_search_place" placeholder="Digite para buscar..." style="width:100%" value="'.esc_attr($location).'">
        <small>Digite o nome do lugar e selecione uma sugestão</small>
    </p>';

    echo '<p><label><strong>Número</strong></label><br/>
        <input type="text" name="associates_number" value="'.esc_attr($number).'" style="width:100%">
    </p>';

    echo '<input type="hidden" name="associates_location" id="associates_location" value="'.esc_attr($location).'">';
    echo '<input type="hidden" name="associates_lat" id="associates_lat" value="'.esc_attr($lat).'">';
    echo '<input type="hidden" name="associates_lng" id="associates_lng" value="'.esc_attr($lng).'">';

    // Estado fixo como Bahia
    echo '<p><label><strong>Estado</strong></label><br/><input type="text" value="Bahia" readonly style="width:100%; background-color:#f0f0f0; color:#666;"></p>';
    echo '<input type="hidden" name="associates_state" value="BA">';
}


function associates_save_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['associates_nonce']) || !wp_verify_nonce($_POST['associates_nonce'], 'associates_save_meta')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['associates_description'])) update_post_meta($post_id, '_wpa_description', sanitize_text_field($_POST['associates_description']));
    if (isset($_POST['associates_location'])) update_post_meta($post_id, '_wpa_location', sanitize_text_field($_POST['associates_location']));
    if (isset($_POST['associates_number'])) update_post_meta($post_id, '_wpa_number', sanitize_text_field($_POST['associates_number'])); // salvar número
    if (isset($_POST['associates_state'])) update_post_meta($post_id, '_wpa_state', sanitize_text_field($_POST['associates_state']));
    if (isset($_POST['associates_lat'])) update_post_meta($post_id, '_wpa_latitude', sanitize_text_field($_POST['associates_lat']));
    if (isset($_POST['associates_lng'])) update_post_meta($post_id, '_wpa_longitude', sanitize_text_field($_POST['associates_lng']));
}
add_action('save_post', 'associates_save_meta');
/**
 * 4) Enfileirar scripts e estilos (Leaflet + CSS do plugin)
 */
function associates_enqueue_scripts() {
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);

    // Usar filemtime para versionar e evitar cache
    $css_version = filemtime(plugin_dir_path(__FILE__) . 'styles.css');
    wp_register_style('associates-css', plugin_dir_url(__FILE__) . 'styles.css', array(), $css_version);
    wp_enqueue_style('associates-css');
}
add_action('wp_enqueue_scripts', 'associates_enqueue_scripts');

/**
 * 
 * 6) Autocomplete para busca de localização
 */
function associates_admin_enqueue($hook) {
    global $post;
    if (($hook == 'post-new.php' || $hook == 'post.php') && get_post_type($post) === 'associate') {
        // Usar filemtime para versionar e evitar cache
        $js_version = filemtime(plugin_dir_path(__FILE__) . 'script.js');
        wp_enqueue_script('associates-autocomplete', plugin_dir_url(__FILE__).'script.js', array('jquery'), $js_version, true);
    }
}
add_action('admin_enqueue_scripts', 'associates_admin_enqueue');

/**
 * 
 * 6) Shortcode [associados_interativo]
 */
function associates_shortcode($atts) {
    ob_start();

    // Query: todos os associados
    $query = new WP_Query(array('post_type' => 'associate','posts_per_page' => -1));

    // Buscamos as categorias disponíveis (taxonomy)
    $terms = get_terms(array('taxonomy' => 'associate_category', 'hide_empty' => false));

    ?>
    <div class="associates-wrapper">
        <div class="associates-filters">
            <input type="text" id="associates-search-associate" placeholder="Buscar por nome ou descrição">


            <select id="associates-category-associate">
                <option value="">Todas as Categorias</option>
                <?php
                    if (!is_wp_error($terms) && !empty($terms)) {
                        foreach ($terms as $t) {
                            echo '<option value="'.esc_attr($t->term_id).'">'.esc_html($t->name).'</option>';
                        }
                    }
                ?>
            </select>

            <button id="associates-filter-associates" style="width: 200px;">Filtrar Associados</button>
        </div>

        <div class="associates-parent-div">
            <div id="associates-container" class="associates-list">
                <?php while ($query->have_posts()) : $query->the_post();
                    $location = get_post_meta(get_the_ID(), '_wpa_location', true);
                    $number = get_post_meta(get_the_ID(), '_wpa_number', true); // pega o número
                    $state = get_post_meta(get_the_ID(), '_wpa_state', true);
                    $lat = get_post_meta(get_the_ID(), '_wpa_latitude', true);
                    $lng = get_post_meta(get_the_ID(), '_wpa_longitude', true);
                    $description = get_post_meta(get_the_ID(), '_wpa_description', true);
                    $image = get_the_post_thumbnail(get_the_ID(), 'medium');
                    $terms_assoc = wp_get_post_terms(get_the_ID(), 'associate_category', array('fields'=>'ids'));
                    $terms_assoc_json = esc_attr(json_encode($terms_assoc));

                    // Monta endereço formatado
                    $endereco = trim($location . ($number ? ', '.$number : '') . ($state ? ' - '.$state : ''));
                ?>
                <div class="associate" data-lat="<?php echo esc_attr($lat); ?>" data-lng="<?php echo esc_attr($lng); ?>"
                     data-name="<?php echo esc_attr(get_the_title()); ?>" data-description="<?php echo esc_attr($description); ?>"
                     data-location="<?php echo esc_attr($location); ?>" data-number="<?php echo esc_attr($number); ?>"
                     data-state="<?php echo esc_attr($state); ?>" data-cats='<?php echo $terms_assoc_json; ?>'>
                    <div class="associate-thumb">
                        <?php
                            if ($image) echo $image;
                            else echo '<img src="'.esc_url(plugins_url('placeholder.png', __FILE__)).'" alt="sem imagem" />';
                        ?>
                    </div>
                    <h3><?php the_title(); ?></h3>
                    <p class="associates-description"><?php echo esc_html($description); ?></p>
                    <p class="associates-local"><?php echo esc_html($endereco); ?></p>
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
            // init map - centraliza na Bahia
            var map = L.map('map').setView([-12.5797, -41.7007], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a>'
            }).addTo(map);

            var markers = [];
            var markerGroup = L.layerGroup().addTo(map);

            function createDivIconFromImage(imgHtml){
                var html = '<div class="associates-marker-icon">'+ imgHtml +'</div>';
                return L.divIcon({
                    html: html,
                    className: 'ai-marker-wrapper',
                    iconSize: [56,56],
                    iconAnchor: [28,56],
                    popupAnchor: [0,-56]
                });
            }

            document.querySelectorAll('.associate').forEach(function(el){
                var lat = parseFloat(el.dataset.lat);
                var lng = parseFloat(el.dataset.lng);
                var name = el.dataset.name || '';
                var description = el.dataset.description || '';
                var location = el.dataset.location || '';
                var number = el.dataset.number || '';
                var state = el.dataset.state || '';
                var cats = [];
                try { cats = JSON.parse(el.dataset.cats); } catch(e){ cats = []; }

                // Monta endereço formatado
                var endereco = location;
                if (number) endereco += ', Nº' + number;
                if (state) endereco += ' - ' + state;

                var img = el.querySelector('img');
                var imgOuter = img ? img.outerHTML : '<div class="associates-noimg">?</div>';

                if (!isNaN(lat) && !isNaN(lng)) {
                    var icon = createDivIconFromImage(imgOuter);

                    var marker = L.marker([lat,lng], {icon: icon});
                    var popupHtml = '<div class="associates-popup">'+
                        (img ? imgOuter : '') +
                        '<h4 style="margin:8px 0 4px;">'+ name +'</h4>' +
                        '<div class="associates-popup-description">'+ description +'</div>' +
                        '<div class="associates-popup-local">'+ endereco +'</div>' +
                        '</div>';
                    marker.bindPopup(popupHtml);
                    marker.addTo(markerGroup);

                    markers.push({el: el, marker: marker, lat: lat, lng: lng, cats: cats, state: state, name: name, description: description});
                } else {
                    markers.push({el: el, marker: null, lat: null, lng: null, cats: cats, state: state, name: name, description: description});
                }
            });

            // funções utilitárias
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
                var search = (document.getElementById('associates-search-associate').value || '').toLowerCase();
                var state = 'BA'; // Estado fixo como Bahia
                var category = document.getElementById('associates-category-associate').value; // term_id ou ''
                var raio = null; // não usamos raio por enquanto

                // limpar group
                markerGroup.clearLayers();

                markers.forEach(function(m){
                    var show = true;

                    // busca por nome + descrição
                    var hay = (m.name + ' ' + (m.description||'')).toLowerCase();
                    if (search && hay.indexOf(search) === -1) show = false;

                    // estado
                    if (state && m.state !== state) show = false;

                    // categoria (m.cats é array de term_ids)
                    if (category) {
                        var catNum = parseInt(category,10);
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
            document.getElementById('associates-search-associate').addEventListener('input', applyFilters);
            document.getElementById('associates-category-associate').addEventListener('change', applyFilters);
            document.getElementById('associates-filter-associates').addEventListener('click', applyFilters);

            // aplicar inicialmente (mostra todos)
            applyFilters();

            // clicar no card centraliza/puxa popup
            document.querySelectorAll('.associate').forEach(function(card){
                card.addEventListener('click', function(){
                    // encontrar marker associado
                    var name = card.dataset.name;
                    var found = markers.find(function(m){ return m.name === name && m.marker; });
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
add_shortcode('wp-associates', 'associates_shortcode');

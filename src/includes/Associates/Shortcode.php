<?php
namespace Associates\Associates;

use Associates\Plugin;
use Associates\Associates\Taxonomy;
use Associates\Municipalities;

/**
 * Classe para gerenciar o Shortcode dos Associados
 *
 * @package Associates
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe Shortcode
 */
class Shortcode {
    
    /**
     * Instância única da classe
     */
    private static $instance = null;
    
    /**
     * Nome do shortcode
     */
    private $shortcode_name = 'wp-associates';
    
    /**
     * Instância do plugin principal
     */
    private $plugin;
    
    /**
     * Construtor privado para implementar Singleton
     */
    private function __construct() {
        $this->plugin = Plugin::get_instance();
        $this->init_hooks();
    }
    
    /**
     * Retorna a instância única da classe
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Inicializa os hooks do WordPress
     */
    private function init_hooks() {
        add_shortcode($this->shortcode_name, array($this, 'render_shortcode'));
    }
    
    /**
     * Renderiza o shortcode
     */
    public function render_shortcode($atts) {
        // Carregar CSS e JS necessários
        $this->enqueue_required_assets();
        
        ob_start();
        
        // Query: todos os associados
        $query = new \WP_Query(array(
            'post_type' => 'associate',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        // Buscar as categorias disponíveis
        $taxonomy = Taxonomy::get_instance();
        $terms = $taxonomy->get_terms();
        
        $this->render_filters($terms);
        $this->render_associates_list($query);
        $this->render_map();
        $this->render_modal();
        $this->render_scripts($query);
        
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    
    /**
     * Enfileira os assets necessários para o shortcode
     */
    private function enqueue_required_assets() {
        // Leaflet CSS e JS
        wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
        wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
        
        // CSS do plugin - usar caminho correto
        wp_enqueue_style(
            'wp-associates-css',
            WPA_PLUGIN_URL . '/styles.css',
            array(),
            '2.7'
        );
    }
    
    /**
     * Renderiza os filtros
     */
    private function render_filters($terms) {
        $municipalities = Municipalities::get_instance()->get_municipalities();
        ?>
        <div class="associates-wrapper">
            <div class="associates-filters">
                <input type="text" id="associates-search-associate" placeholder="<?php _e('Buscar por nome ou descrição', 'wp-associates'); ?>">

                <select id="associates-municipality-filter">
                    <option value=""><?php _e('Todos os Municípios', 'wp-associates'); ?></option>
                    <?php
                    foreach ($municipalities as $mun_name => $coords) {
                        echo '<option value="' . esc_attr($mun_name) . '">' . esc_html($mun_name) . '</option>';
                    }
                    ?>
                </select>

                <select id="associates-category-associate">
                    <option value=""><?php _e('Todas as Categorias', 'wp-associates'); ?></option>
                    <?php
                    if (!is_wp_error($terms) && !empty($terms)) {
                        foreach ($terms as $term) {
                            echo '<option value="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</option>';
                        }
                    }
                    ?>
                </select>

                <button id="associates-filter-associates" style="width: 200px;"><?php _e('Filtrar Associados', 'wp-associates'); ?></button>
            </div>
        <?php
    }
    
    /**
     * Renderiza a lista de associados
     */
    private function render_associates_list($query) {
        ?>
        <div class="associates-parent-div">
            <div id="associates-container" class="associates-list">
                <?php 
                while ($query->have_posts()) : $query->the_post();
                    $this->render_associate_card();
                endwhile; 
                ?>
            </div>
        <?php
    }
    
    /**
     * Renderiza um card de associado
     */
    private function render_associate_card() {
        $post_id = get_the_ID();
        $municipality = get_post_meta($post_id, '_wpa_municipality', true);
        $lat = get_post_meta($post_id, '_wpa_latitude', true);
        $lng = get_post_meta($post_id, '_wpa_longitude', true);
        $description = get_post_meta($post_id, '_wpa_description', true);
        $image = get_the_post_thumbnail($post_id, 'medium');
        
        // Buscar categorias
        $taxonomy = Taxonomy::get_instance();
        $terms_assoc = $taxonomy->get_post_terms($post_id);
        $terms_assoc_json = '';
        if (!empty($terms_assoc) && is_array($terms_assoc)) {
            $terms_assoc_json = esc_attr(json_encode($terms_assoc));
        }
        
        // Buscar fotos
        $photos = get_post_meta($post_id, '_wpa_photos', true);
        $photos_json = '';
        if (is_array($photos) && !empty($photos)) {
            $photos_data = array();
            foreach ($photos as $photo_id) {
                $photo_url = wp_get_attachment_image_url($photo_id, 'large');
                $photo_thumb = wp_get_attachment_image_url($photo_id, 'thumbnail');
                if ($photo_url && $photo_thumb) {
                    $photos_data[] = array(
                        'id' => $photo_id,
                        'url' => $photo_url,
                        'thumb' => $photo_thumb
                    );
                }
            }
            $photos_json = esc_attr(json_encode($photos_data));
        }
        
        $avatar_url = $this->plugin->get_plugin_url() . 'assets/avatar.png';
        ?>
        <div class="associate" 
             data-lat="<?php echo esc_attr($lat); ?>" 
             data-lng="<?php echo esc_attr($lng); ?>"
             data-name="<?php echo esc_attr(get_the_title()); ?>" 
             data-description="<?php echo esc_attr($description); ?>"
             data-municipality="<?php echo esc_attr($municipality); ?>" 
             data-cats='<?php echo $terms_assoc_json; ?>'
             data-photos='<?php echo $photos_json; ?>'>
            
            <div class="associate-thumb">
                <?php
                if ($image) {
                    echo $image;
                } else {
                    echo '<img src="' . esc_url($avatar_url) . '" alt="' . __('sem imagem', 'wp-associates') . '" />';
                }
                ?>
            </div>
            
            <h3><?php the_title(); ?></h3>
            
            <div class="associates-description-container">
                <?php 
                $description_length = strlen($description);
                if ($description_length > 150) {
                    $short_desc = substr($description, 0, 150) . '...';
                    echo '<p class="associates-description-short">' . esc_html($short_desc) . '</p>';
                    echo '<p class="associates-description-full" style="display:none;">' . esc_html($description) . '</p>';
                    echo '<button class="associates-read-more" onclick="toggleDescription(this, event)">' . __('Leia mais...', 'wp-associates') . '</button>';
                } else {
                    echo '<p class="associates-description">' . esc_html($description) . '</p>';
                }
                ?>
            </div>
            
            <p class="associates-local"><?php echo esc_html($municipality); ?> - BA</p>
        </div>
        <?php
    }
    
    /**
     * Renderiza o mapa
     */
    private function render_map() {
        ?>
            <div id="map"></div>
        </div>
        <?php
    }
    
    /**
     * Renderiza o modal
     */
    private function render_modal() {
        ?>
        <!-- Modal para exibir informações do associado -->
        <div id="associates-modal" class="associates-modal" style="display: none;">
            <div class="associates-modal-content">
                <span class="associates-modal-close">&times;</span>
                <div id="associates-modal-body"></div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renderiza os scripts JavaScript
     */
    private function render_scripts($query) {
        $avatar_url = $this->plugin->get_plugin_url() . 'assets/avatar.png';
        ?>
        <script>
        // Função para alternar descrição
        function toggleDescription(button, event) {
            event.stopPropagation();
            var container = button.parentNode;
            var shortDesc = container.querySelector('.associates-description-short');
            var fullDesc = container.querySelector('.associates-description-full');
            
            if (fullDesc.style.display === 'none') {
                shortDesc.style.display = 'none';
                fullDesc.style.display = 'block';
                button.textContent = '<?php _e('Leia menos...', 'wp-associates'); ?>';
            } else {
                shortDesc.style.display = 'block';
                fullDesc.style.display = 'none';
                button.textContent = '<?php _e('Leia mais...', 'wp-associates'); ?>';
            }
        }

        // Função para mostrar modal do associado
        function showAssociateModal(name, description, municipality, imgOuter, photos) {
            var modal = document.getElementById('associates-modal');
            var modalBody = document.getElementById('associates-modal-body');
            
            var photosHtml = '';
            if (photos && photos.length > 0) {
                photosHtml = '<div class="associates-modal-photos">' +
                    '<h4><?php _e('Fotos', 'wp-associates'); ?></h4>' +
                    '<div class="associates-carousel-container">' +
                        '<div class="associates-carousel-wrapper">' +
                            '<button class="associates-carousel-prev" onclick="changeCarouselSlide(-1)">‹</button>' +
                            '<div class="associates-carousel-slides">';
                
                photos.forEach(function(photo, index) {
                    photosHtml += '<div class="associates-carousel-slide' + (index === 0 ? ' active' : '') + '">' +
                        '<img src="' + photo.url + '" alt="<?php _e('Foto', 'wp-associates'); ?>">' +
                        '</div>';
                });
                
                photosHtml += '</div>' +
                            '<button class="associates-carousel-next" onclick="changeCarouselSlide(1)">›</button>' +
                        '</div>' +
                        '<div class="associates-carousel-dots">';
                
                photos.forEach(function(photo, index) {
                    photosHtml += '<span class="associates-carousel-dot' + (index === 0 ? ' active' : '') + '" onclick="goToSlide(' + index + ')"></span>';
                });
                
                photosHtml += '</div>' +
                    '</div>' +
                '</div>';
            }
            
            var modalContent = '<div class="associates-modal-header">' +
                '<div class="associates-modal-image">' + imgOuter + '</div>' +
                '<div class="associates-modal-info">' +
                    '<h3>' + name + '</h3>' +
                    '<p class="associates-modal-location">' + municipality + ' - BA</p>' +
                '</div>' +
            '</div>' +
            '<div class="associates-modal-description">' + description + '</div>' +
            photosHtml;
            
            modalBody.innerHTML = modalContent;
            modal.classList.add('show');
        }

        // Função para fechar modal
        function closeAssociateModal() {
            var modal = document.getElementById('associates-modal');
            modal.classList.remove('show');
        }

        // Variáveis globais para o carrossel
        var currentSlide = 0;
        var totalSlides = 0;

        // Função para mudar slide do carrossel
        function changeCarouselSlide(direction) {
            var slides = document.querySelectorAll('.associates-carousel-slide');
            var dots = document.querySelectorAll('.associates-carousel-dot');
            
            if (slides.length === 0) return;
            
            totalSlides = slides.length;
            currentSlide += direction;
            
            if (currentSlide >= totalSlides) {
                currentSlide = 0;
            } else if (currentSlide < 0) {
                currentSlide = totalSlides - 1;
            }
            
            slides.forEach(function(slide, index) {
                slide.classList.toggle('active', index === currentSlide);
            });
            
            dots.forEach(function(dot, index) {
                dot.classList.toggle('active', index === currentSlide);
            });
        }

        // Função para ir para um slide específico
        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            var slides = document.querySelectorAll('.associates-carousel-slide');
            var dots = document.querySelectorAll('.associates-carousel-dot');
            
            slides.forEach(function(slide, index) {
                slide.classList.toggle('active', index === slideIndex);
            });
            
            dots.forEach(function(dot, index) {
                dot.classList.toggle('active', index === slideIndex);
            });
        }

        (function(){
            document.addEventListener('DOMContentLoaded', function(){
                // Inicializar mapa
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
                        iconSize: [36,36],
                        iconAnchor: [18,36],
                        popupAnchor: [0,-36]
                    });
                }

                document.querySelectorAll('.associate').forEach(function(el){
                    var lat = parseFloat(el.dataset.lat);
                    var lng = parseFloat(el.dataset.lng);
                    var name = el.dataset.name || '';
                    var description = el.dataset.description || '';
                    var municipality = el.dataset.municipality || '';
                    var cats = [];
                    var photos = [];
                    try { cats = JSON.parse(el.dataset.cats); } catch(e){ cats = []; }
                    try { photos = JSON.parse(el.dataset.photos); } catch(e){ photos = []; }

                    var img = el.querySelector('img');
                    var imgOuter = img ? img.outerHTML : '<img src="<?php echo esc_url($avatar_url); ?>" alt="<?php _e('sem imagem', 'wp-associates'); ?>" />';

                    if (!isNaN(lat) && !isNaN(lng)) {
                        var icon = createDivIconFromImage(imgOuter);
                        var marker = L.marker([lat,lng], {icon: icon});
                        
                        marker.on('click', function() {
                            showAssociateModal(name, description, municipality, imgOuter, photos);
                        });
                        
                        marker.addTo(markerGroup);
                        markers.push({el: el, marker: marker, lat: lat, lng: lng, cats: cats, municipality: municipality, name: name, description: description, photos: photos});
                    } else {
                        markers.push({el: el, marker: null, lat: null, lng: null, cats: cats, municipality: municipality, name: name, description: description, photos: photos});
                    }
                });

                // Funções utilitárias
                function deg2rad(deg){ return deg * (Math.PI/180); }
                function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2){
                    var R=6371;
                    var dLat=deg2rad(lat2-lat1);
                    var dLon=deg2rad(lon2-lon1);
                    var a=Math.sin(dLat/2)*Math.sin(dLat/2)+Math.cos(deg2rad(lat1))*Math.cos(deg2rad(lat2))*Math.sin(dLon/2)*Math.sin(dLon/2);
                    var c=2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
                    return R*c;
                }

                // Filtrar - aplica filtros visuais e no mapa
                function applyFilters(){
                    var search = (document.getElementById('associates-search-associate').value || '').toLowerCase();
                    var municipality = document.getElementById('associates-municipality-filter').value;
                    var category = document.getElementById('associates-category-associate').value;

                    markerGroup.clearLayers();

                    markers.forEach(function(m){
                        var show = true;

                        var hay = (m.name + ' ' + (m.description||'')).toLowerCase();
                        if (search && hay.indexOf(search) === -1) show = false;

                        if (municipality && m.municipality !== municipality) show = false;

                        if (category) {
                            var catNum = parseInt(category,10);
                            if (!m.cats || m.cats.indexOf(catNum) === -1) show = false;
                        }

                        if (show) {
                            m.el.style.display = 'block';
                            if (m.marker) markerGroup.addLayer(m.marker);
                        } else {
                            m.el.style.display = 'none';
                        }
                    });

                    var allMarkers = [];
                    markerGroup.eachLayer(function(l){ allMarkers.push(l.getLatLng()); });
                    if (allMarkers.length > 0) {
                        var bounds = L.latLngBounds(allMarkers);
                        map.fitBounds(bounds.pad(0.25));
                    } else {
                        map.setView([-12.5797, -41.7007], 6);
                    }
                }

                // Eventos
                document.getElementById('associates-search-associate').addEventListener('input', applyFilters);
                document.getElementById('associates-municipality-filter').addEventListener('change', applyFilters);
                document.getElementById('associates-category-associate').addEventListener('change', applyFilters);
                document.getElementById('associates-filter-associates').addEventListener('click', applyFilters);

                applyFilters();

                // Event listeners para fechar modal
                document.querySelector('.associates-modal-close').addEventListener('click', closeAssociateModal);
                document.getElementById('associates-modal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeAssociateModal();
                    }
                });

                // Clicar no card centraliza e abre modal
                document.querySelectorAll('.associate').forEach(function(card){
                    card.addEventListener('click', function(){
                        var name = card.dataset.name;
                        var description = card.dataset.description;
                        var municipality = card.dataset.municipality;
                        var photos = [];
                        try { photos = JSON.parse(card.dataset.photos); } catch(e){ photos = []; }
                        var img = card.querySelector('img');
                        var imgOuter = img ? img.outerHTML : '<img src="<?php echo esc_url($avatar_url); ?>" alt="<?php _e('sem imagem', 'wp-associates'); ?>" />';
                        
                        var found = markers.find(function(m){ return m.name === name && m.marker; });
                        if (found && found.marker) {
                            map.setView(found.marker.getLatLng(), 12);
                            showAssociateModal(name, description, municipality, imgOuter, photos);
                        }
                    });
                });

            });
        })();
        </script>
        <?php
    }
    
    /**
     * Retorna o nome do shortcode
     */
    public function get_shortcode_name() {
        return $this->shortcode_name;
    }
}

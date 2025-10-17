<?php
namespace Associates\Events;

/**
 * Classe para gerenciar os Metaboxes dos Eventos
 *
 * @package Associates
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe Metabox
 */
class Metabox {
    
    /**
     * Instância única da classe
     */
    private static $instance = null;
    
    /**
     * Post type associado
     */
    private $post_type = 'event';
    
    /**
     * Construtor privado para implementar Singleton
     */
    private function __construct() {
        add_action('add_meta_boxes', array($this, 'add_metabox'));
        add_action('save_post', array($this, 'save_meta'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_filter('enter_title_here', array($this, 'change_title_placeholder'));
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
     * Adiciona os metaboxes
     */
    public function add_metabox() {
        add_meta_box(
            'event_details',
            __('Informações do Evento', 'wp-associates'),
            array($this, 'details_metabox_callback'),
            $this->post_type,
            'normal',
            'default'
        );
    }
    
    
    /**
     * Callback do metabox de detalhes
     */
    public function details_metabox_callback($post) {
        wp_nonce_field('event_gallery_meta', 'event_gallery_meta_nonce');
        
        $gallery_images = get_post_meta($post->ID, '_event_gallery_images', true);
        $gallery_array = !empty($gallery_images) ? explode(',', $gallery_images) : array();
        
        echo '<div class="event-gallery-container">';
        echo '<p class="description">' . __('Adicione múltiplas imagens para criar uma galeria do evento.', 'wp-associates') . '</p>';
        echo '<button type="button" id="event-add-gallery-images" class="button button-primary">';
        echo __('Adicionar Imagens à Galeria', 'wp-associates');
        echo '</button>';
        
        echo '<input type="hidden" id="event-gallery-images" name="event_gallery_images" value="' . esc_attr($gallery_images) . '">';
        
        echo '<div id="event-gallery-preview" class="event-gallery-preview">';
        
        if (!empty($gallery_array) && $gallery_array[0] !== '') {
            foreach ($gallery_array as $image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                if ($image_url) {
                    echo '<div class="event-gallery-item" data-image-id="' . esc_attr($image_id) . '">';
                    echo '<img src="' . esc_url($image_url) . '" alt="' . __('Imagem da galeria', 'wp-associates') . '">';
                    echo '<button type="button" class="event-remove-image" data-image-id="' . esc_attr($image_id) . '">×</button>';
                    echo '</div>';
                }
            }
        }
        
        echo '</div>';
        echo '</div>';
        
        // CSS e JavaScript inline para o metabox da galeria
        echo '<style>
        .event-gallery-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        .event-gallery-item {
            position: relative;
            display: inline-block;
        }
        .event-gallery-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .event-remove-image {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3232;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            font-size: 12px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .event-remove-image:hover {
            background: #a00;
        }
        </style>';
        
        echo '<script>
        jQuery(document).ready(function($) {
            let galleryFrame;
            
            // Abrir seletor de mídia
            $("#event-add-gallery-images").on("click", function(e) {
                e.preventDefault();
                
                if (galleryFrame) {
                    galleryFrame.open();
                    return;
                }
                
                galleryFrame = wp.media({
                    title: "' . __('Selecionar Imagens da Galeria', 'wp-associates') . '",
                    button: {
                        text: "' . __('Adicionar à Galeria', 'wp-associates') . '"
                    },
                    multiple: true,
                    library: {
                        type: "image"
                    }
                });
                
                galleryFrame.on("select", function() {
                    const attachments = galleryFrame.state().get("selection").toJSON();
                    const currentImages = $("#event-gallery-images").val().split(",").filter(id => id !== "");
                    
                    attachments.forEach(function(attachment) {
                        if (!currentImages.includes(attachment.id.toString())) {
                            currentImages.push(attachment.id);
                            addImageToGallery(attachment);
                        }
                    });
                    
                    $("#event-gallery-images").val(currentImages.join(","));
                });
                
                galleryFrame.open();
            });
            
            // Remover imagem da galeria
            $(document).on("click", ".event-remove-image", function() {
                const imageId = $(this).data("image-id");
                const currentImages = $("#event-gallery-images").val().split(",").filter(id => id !== "" && id !== imageId);
                
                $("#event-gallery-images").val(currentImages.join(","));
                $(this).parent().remove();
            });
            
            function addImageToGallery(attachment) {
                const imageUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                const galleryItem = $("<div>")
                    .addClass("event-gallery-item")
                    .attr("data-image-id", attachment.id)
                    .html("<img src=\\"" + imageUrl + "\\" alt=\\"" + attachment.alt + "\\"><button type=\\"button\\" class=\\"event-remove-image\\" data-image-id=\\"" + attachment.id + "\\">×</button>");
                
                $("#event-gallery-preview").append(galleryItem);
            }
        });
        </script>';
    }
    
    /**
     * Enfileira os assets do admin para eventos
     */
    public function enqueue_admin_assets($hook) {
        global $post_type;
        
        // Verificar se estamos na página de edição de eventos
        if ($post_type === 'event' && ($hook === 'post.php' || $hook === 'post-new.php')) {
            // Enfileirar mídia e scripts necessários
            wp_enqueue_media();
            wp_enqueue_script('jquery');
            wp_enqueue_script('wp-util');
            
            // CSS do plugin para admin
            $plugin = \Associates\Plugin::get_instance();
            $css_file = $plugin->get_plugin_path() . 'styles.css';
            if (file_exists($css_file)) {
                wp_enqueue_style(
                    'wp-associates-admin-css',
                    $plugin->get_plugin_url() . 'styles.css',
                    array(),
                    filemtime($css_file)
                );
            }
        }
    }
    
    /**
     * Salva os dados dos metaboxes
     */
    public function save_meta($post_id) {
        // Verificar nonce e permissões
        if (!isset($_POST['event_gallery_meta_nonce']) || !wp_verify_nonce($_POST['event_gallery_meta_nonce'], 'event_gallery_meta')) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (get_post_type($post_id) !== $this->post_type) {
            return;
        }
        
        // Salvar galeria de imagens
        if (isset($_POST['event_gallery_meta_nonce']) && wp_verify_nonce($_POST['event_gallery_meta_nonce'], 'event_gallery_meta')) {
            if (isset($_POST['event_gallery_images'])) {
                $gallery_images = sanitize_text_field($_POST['event_gallery_images']);
                update_post_meta($post_id, '_event_gallery_images', $gallery_images);
            }
        }
    }
    
    /**
     * Altera o placeholder do título
     */
    public function change_title_placeholder($title) {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === $this->post_type) {
            $title = __('Adicionar título do Evento', 'wp-associates');
        }
        return $title;
    }
}

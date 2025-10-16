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
            'event_gallery',
            __('Galeria de Imagens', 'wp-associates'),
            array($this, 'gallery_metabox_callback'),
            $this->post_type,
            'normal',
            'default'
        );
        
        add_meta_box(
            'event_details',
            __('Detalhes do Evento', 'wp-associates'),
            array($this, 'details_metabox_callback'),
            $this->post_type,
            'side',
            'default'
        );
    }
    
    /**
     * Callback do metabox da galeria
     */
    public function gallery_metabox_callback($post) {
        wp_nonce_field('event_gallery_meta', 'event_gallery_meta_nonce');
        
        $gallery_images = get_post_meta($post->ID, '_event_gallery_images', true);
        $gallery_images = is_array($gallery_images) ? $gallery_images : array();
        
        ?>
        <div id="event-gallery-metabox">
            <p>
                <a href="#" id="event-add-gallery-images" class="button button-primary">
                    <?php _e('Adicionar Imagens à Galeria', 'wp-associates'); ?>
                </a>
            </p>
            
            <div id="event-gallery-preview" class="event-gallery-grid">
                <?php if (!empty($gallery_images)): ?>
                    <?php foreach ($gallery_images as $image_id): ?>
                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                        <?php if ($image_url): ?>
                            <div class="event-gallery-item" data-image-id="<?php echo esc_attr($image_id); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php _e('Imagem do evento', 'wp-associates'); ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                                <button type="button" class="event-remove-image" data-image-id="<?php echo esc_attr($image_id); ?>">
                                    <?php _e('Remover', 'wp-associates'); ?>
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <input type="hidden" id="event-gallery-images" name="event_gallery_images" value="<?php echo esc_attr(implode(',', $gallery_images)); ?>">
            
            <p class="description">
                <?php _e('Adicione múltiplas imagens para criar uma galeria do evento.', 'wp-associates'); ?>
            </p>
        </div>
        
        <style>
        .event-gallery-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .event-gallery-item {
            position: relative;
            display: inline-block;
        }
        .event-gallery-item img {
            border: 2px solid #ddd;
            border-radius: 4px;
        }
        .event-gallery-item:hover img {
            border-color: #0073aa;
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
        }
        .event-remove-image:hover {
            background: #a00;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            let galleryFrame;
            
            // Abrir seletor de mídia
            $('#event-add-gallery-images').on('click', function(e) {
                e.preventDefault();
                
                if (galleryFrame) {
                    galleryFrame.open();
                    return;
                }
                
                galleryFrame = wp.media({
                    title: '<?php _e('Selecionar Imagens da Galeria', 'wp-associates'); ?>',
                    button: {
                        text: '<?php _e('Adicionar à Galeria', 'wp-associates'); ?>'
                    },
                    multiple: true,
                    library: {
                        type: 'image'
                    }
                });
                
                galleryFrame.on('select', function() {
                    const attachments = galleryFrame.state().get('selection').toJSON();
                    const currentImages = $('#event-gallery-images').val().split(',').filter(id => id !== '');
                    
                    attachments.forEach(function(attachment) {
                        if (!currentImages.includes(attachment.id.toString())) {
                            currentImages.push(attachment.id);
                            addImageToGallery(attachment);
                        }
                    });
                    
                    $('#event-gallery-images').val(currentImages.join(','));
                });
                
                galleryFrame.open();
            });
            
            // Remover imagem da galeria
            $(document).on('click', '.event-remove-image', function() {
                const imageId = $(this).data('image-id');
                const currentImages = $('#event-gallery-images').val().split(',').filter(id => id !== '' && id !== imageId);
                
                $('#event-gallery-images').val(currentImages.join(','));
                $(this).parent().remove();
            });
            
            function addImageToGallery(attachment) {
                const imageHtml = `
                    <div class="event-gallery-item" data-image-id="${attachment.id}">
                        <img src="${attachment.sizes.thumbnail.url}" alt="<?php _e('Imagem do evento', 'wp-associates'); ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                        <button type="button" class="event-remove-image" data-image-id="${attachment.id}">
                            <?php _e('Remover', 'wp-associates'); ?>
                        </button>
                    </div>
                `;
                $('#event-gallery-preview').append(imageHtml);
            }
        });
        </script>
        <?php
    }
    
    /**
     * Callback do metabox de detalhes
     */
    public function details_metabox_callback($post) {
        wp_nonce_field('event_details_meta', 'event_details_meta_nonce');
        
        $event_date = get_post_meta($post->ID, '_event_date', true);
        $event_location = get_post_meta($post->ID, '_event_location', true);
        $event_price = get_post_meta($post->ID, '_event_price', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="event_date"><?php _e('Data do Evento', 'wp-associates'); ?></label>
                </th>
                <td>
                    <input type="date" id="event_date" name="event_date" value="<?php echo esc_attr($event_date); ?>" style="width: 100%;">
                    <p class="description"><?php _e('Data em que o evento acontecerá.', 'wp-associates'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="event_location"><?php _e('Local do Evento', 'wp-associates'); ?></label>
                </th>
                <td>
                    <input type="text" id="event_location" name="event_location" value="<?php echo esc_attr($event_location); ?>" style="width: 100%;">
                    <p class="description"><?php _e('Local onde o evento acontecerá.', 'wp-associates'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="event_price"><?php _e('Preço do Evento', 'wp-associates'); ?></label>
                </th>
                <td>
                    <input type="text" id="event_price" name="event_price" value="<?php echo esc_attr($event_price); ?>" style="width: 100%;" placeholder="Ex: R$ 50,00 ou Gratuito">
                    <p class="description"><?php _e('Preço do evento ou "Gratuito".', 'wp-associates'); ?></p>
                </td>
            </tr>
        </table>
        <?php
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
        if (isset($_POST['event_gallery_images'])) {
            $gallery_images = sanitize_text_field($_POST['event_gallery_images']);
            $gallery_array = !empty($gallery_images) ? explode(',', $gallery_images) : array();
            $gallery_array = array_filter($gallery_array); // Remove valores vazios
            
            update_post_meta($post_id, '_event_gallery_images', $gallery_array);
        }
        
        // Salvar detalhes do evento
        if (isset($_POST['event_details_meta_nonce']) && wp_verify_nonce($_POST['event_details_meta_nonce'], 'event_details_meta')) {
            if (isset($_POST['event_date'])) {
                $event_date = sanitize_text_field($_POST['event_date']);
                update_post_meta($post_id, '_event_date', $event_date);
            }
            
            if (isset($_POST['event_location'])) {
                $event_location = sanitize_text_field($_POST['event_location']);
                update_post_meta($post_id, '_event_location', $event_location);
            }
            
            if (isset($_POST['event_price'])) {
                $event_price = sanitize_text_field($_POST['event_price']);
                update_post_meta($post_id, '_event_price', $event_price);
            }
        }
    }
}

<?php
namespace Associates;

use Associates\Municipalities;

/**
 * Classe para gerenciar os Metaboxes dos Associados
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
     * Nome do post type
     */
    private $post_type = 'associate';
    
    /**
     * Construtor privado para implementar Singleton
     */
    private function __construct() {
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
        add_action('add_meta_boxes', array($this, 'add_metaboxes'));
        add_action('save_post', array($this, 'save_meta'));
    }
    
    /**
     * Adiciona os metaboxes
     */
    public function add_metaboxes() {
        add_meta_box(
            'associates_info',
            __('Informações do Associado', 'wp-associates'),
            array($this, 'info_metabox_callback'),
            $this->post_type,
            'normal',
            'default'
        );
        
        add_meta_box(
            'associates_photos',
            __('Fotos', 'wp-associates'),
            array($this, 'photos_metabox_callback'),
            $this->post_type,
            'side',
            'default'
        );
    }
    
    /**
     * Callback para o metabox de informações
     */
    public function info_metabox_callback($post) {
        $description = get_post_meta($post->ID, '_wpa_description', true);
        $municipality = get_post_meta($post->ID, '_wpa_municipality', true);
        
        wp_nonce_field('associates_save_meta', 'associates_nonce');
        
        echo '<table class="form-table">';
        
        // Campo de descrição
        echo '<tr>';
        echo '<th scope="row"><label for="associates_description">' . __('Descrição', 'wp-associates') . '</label></th>';
        echo '<td>';
        echo '<textarea name="associates_description" id="associates_description" rows="4" style="width:100%; resize:vertical;">' . esc_textarea($description) . '</textarea>';
        echo '<p class="description">' . __('Descreva o associado e suas atividades.', 'wp-associates') . '</p>';
        echo '</td>';
        echo '</tr>';
        
        // Campo de município
        echo '<tr>';
        echo '<th scope="row"><label for="associates_municipality">' . __('Município', 'wp-associates') . '</label></th>';
        echo '<td>';
        echo '<select name="associates_municipality" id="associates_municipality" style="width:100%;">';
        echo '<option value="">' . __('Selecione um município', 'wp-associates') . '</option>';
        
        $municipalities = Municipalities::get_instance()->get_municipalities();
        foreach ($municipalities as $name => $coords) {
            $selected = ($municipality === $name) ? 'selected' : '';
            echo '<option value="' . esc_attr($name) . '" ' . $selected . '>' . esc_html($name) . '</option>';
        }
        
        echo '</select>';
        echo '<p class="description">' . __('Selecione o município onde o associado está localizado.', 'wp-associates') . '</p>';
        echo '</td>';
        echo '</tr>';
        
        echo '</table>';
    }
    
    /**
     * Callback para o metabox de fotos
     */
    public function photos_metabox_callback($post) {
        // Verificar se o usuário tem permissão para fazer upload
        if (!current_user_can('upload_files')) {
            echo '<p><em>' . __('Você não tem permissão para fazer upload de arquivos.', 'wp-associates') . '</em></p>';
            return;
        }
        
        $photos = get_post_meta($post->ID, '_wpa_photos', true);
        if (!is_array($photos)) {
            $photos = array();
        }
        
        wp_nonce_field('associates_save_photos', 'associates_photos_nonce');
        
        echo '<div id="associates-photos-container">';
        echo '<p><a href="#" id="associates-add-photos" class="button">' . __('Adicionar fotos', 'wp-associates') . '</a></p>';
        echo '<div id="associates-photos-preview">';
        
        if (!empty($photos)) {
            foreach ($photos as $photo_id) {
                $photo_url = wp_get_attachment_image_url($photo_id, 'thumbnail');
                if ($photo_url) {
                    echo '<div class="associates-photo-item" data-photo-id="' . esc_attr($photo_id) . '">';
                    echo '<img src="' . esc_url($photo_url) . '" alt="' . __('Foto', 'wp-associates') . '" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; margin: 2px;">';
                    echo '<button type="button" class="associates-remove-photo" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px;">×</button>';
                    echo '</div>';
                }
            }
        }
        
        echo '</div>';
        echo '<input type="hidden" id="associates-photos-input" name="associates_photos" value="' . esc_attr(implode(',', $photos)) . '">';
        echo '<p><small>' . __('Clique em "Adicionar fotos" para selecionar imagens da biblioteca de mídia ou fazer upload de novas imagens.', 'wp-associates') . '</small></p>';
        echo '</div>';
    }
    
    /**
     * Salva os dados dos metaboxes
     */
    public function save_meta($post_id) {
        // Verificações de segurança
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!isset($_POST['associates_nonce']) || !wp_verify_nonce($_POST['associates_nonce'], 'associates_save_meta')) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Salvar descrição
        if (isset($_POST['associates_description'])) {
            update_post_meta($post_id, '_wpa_description', sanitize_textarea_field($_POST['associates_description']));
        }
        
        // Salvar município e suas coordenadas
        if (isset($_POST['associates_municipality'])) {
            $municipality = sanitize_text_field($_POST['associates_municipality']);
            update_post_meta($post_id, '_wpa_municipality', $municipality);
            
            // Buscar coordenadas do município selecionado
            if (!empty($municipality)) {
                $municipalities = Municipalities::get_instance();
                $coordinates = $municipalities->get_municipality_coordinates($municipality);
                
                if ($coordinates) {
                    update_post_meta($post_id, '_wpa_latitude', $coordinates['lat']);
                    update_post_meta($post_id, '_wpa_longitude', $coordinates['lng']);
                }
            }
        }
        
        // Salvar fotos
        if (isset($_POST['associates_photos']) && wp_verify_nonce($_POST['associates_photos_nonce'], 'associates_save_photos')) {
            $photos = sanitize_text_field($_POST['associates_photos']);
            $photo_ids = array();
            
            if (!empty($photos)) {
                $photo_ids = array_map('intval', explode(',', $photos));
                $photo_ids = array_filter($photo_ids); // Remove valores vazios
            }
            
            update_post_meta($post_id, '_wpa_photos', $photo_ids);
        }
    }
    
    /**
     * Retorna o nome do post type
     */
    public function get_post_type() {
        return $this->post_type;
    }
}

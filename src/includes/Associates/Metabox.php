<?php
namespace Associates\Associates;

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
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
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
     * Enfileira os assets do admin
     */
    public function enqueue_admin_assets($hook) {
        global $post_type;
        
        // Verificar se estamos na página de edição de associados
        if ($post_type === 'associate' && ($hook === 'post.php' || $hook === 'post-new.php')) {
            // Enfileirar mídia e scripts necessários
            wp_enqueue_media();
            wp_enqueue_script('jquery');
            wp_enqueue_script('wp-util');
            
            // JS do plugin para admin
            $plugin = \Associates\Plugin::get_instance();
            $js_file = $plugin->get_plugin_path() . 'script.js';
            if (file_exists($js_file)) {
                wp_enqueue_script(
                    'wp-associates-admin-js',
                    $plugin->get_plugin_url() . 'script.js',
                    array('jquery', 'wp-util'),
                    filemtime($js_file),
                    true
                );
            }
            
            // CSS do plugin para admin
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
        
    }
    
    /**
     * Retorna o nome do post type
     */
    public function get_post_type() {
        return $this->post_type;
    }
}

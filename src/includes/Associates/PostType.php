<?php
namespace Associates\Associates;

/**
 * Classe para gerenciar o Post Type de Associados
 *
 * @package Associates
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe PostType
 */
class PostType {
    
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
        add_action('init', array($this, 'register_post_type'), 0);
        add_filter('enter_title_here', array($this, 'change_title_placeholder'));
        add_filter('admin_post_thumbnail_html', array($this, 'change_featured_image_text'), 10, 2);
    }
    
    /**
     * Registra o post type 'associate'
     */
    public function register_post_type() {
        $labels = array(
            'name' => __('Associados', 'wp-associates'),
            'singular_name' => __('Associado', 'wp-associates'),
            'add_new' => __('Adicionar Novo', 'wp-associates'),
            'add_new_item' => __('Adicionar Novo Associado', 'wp-associates'),
            'edit_item' => __('Editar Associado', 'wp-associates'),
            'new_item' => __('Novo Associado', 'wp-associates'),
            'view_item' => __('Ver Associado', 'wp-associates'),
            'search_items' => __('Buscar Associados', 'wp-associates'),
            'not_found' => __('Nenhum associado encontrado', 'wp-associates'),
            'not_found_in_trash' => __('Nenhum associado encontrado na lixeira', 'wp-associates'),
            'all_items' => __('Todos os Associados', 'wp-associates'),
            'archives' => __('Arquivo de Associados', 'wp-associates'),
            'attributes' => __('Atributos do Associado', 'wp-associates'),
            'insert_into_item' => __('Inserir no associado', 'wp-associates'),
            'uploaded_to_this_item' => __('Enviado para este associado', 'wp-associates'),
            'featured_image' => __('Foto do Associado', 'wp-associates'),
            'set_featured_image' => __('Definir foto do associado', 'wp-associates'),
            'remove_featured_image' => __('Remover foto do associado', 'wp-associates'),
            'use_featured_image' => __('Usar como foto do associado', 'wp-associates'),
            'menu_name' => __('Associados', 'wp-associates'),
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-groups',
            'menu_position' => 6,
            'supports' => array('title', 'thumbnail'),
            'show_in_rest' => true,
            'rewrite' => false,
            'publicly_queryable' => false,
        );
        
        register_post_type($this->post_type, $args);
    }
    
    /**
     * Altera o placeholder do título para associados
     */
    public function change_title_placeholder($title) {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === $this->post_type) {
            $title = __('Adicionar título do Associado', 'wp-associates');
        }
        return $title;
    }
    
    /**
     * Altera o texto do Featured Image para associados
     */
    public function change_featured_image_text($content, $post_id) {
        $post = get_post($post_id);
        if ($post && $post->post_type === $this->post_type) {
            $content = str_replace('Featured image', __('Foto do Associado', 'wp-associates'), $content);
            $content = str_replace('Set featured image', __('Definir foto do associado', 'wp-associates'), $content);
            $content = str_replace('Remove featured image', __('Remover foto do associado', 'wp-associates'), $content);
            $content = str_replace('Replace featured image', __('Substituir foto do associado', 'wp-associates'), $content);
        }
        return $content;
    }
    
    /**
     * Retorna o nome do post type
     */
    public function get_post_type() {
        return $this->post_type;
    }
}

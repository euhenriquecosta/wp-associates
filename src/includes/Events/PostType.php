<?php
namespace Associates\Events;

/**
 * Classe para gerenciar o Post Type de Eventos
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
     * Slug do post type
     */
    private $post_type_slug = 'event';
    
    /**
     * Construtor privado para implementar Singleton
     */
    private function __construct() {
        add_action('init', array($this, 'register_post_type'), 0);
        add_filter('admin_post_thumbnail_html', array($this, 'change_featured_image_text'), 10, 2);
        add_action('admin_head', array($this, 'add_admin_styles'));
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
     * Registra o post type
     */
    public function register_post_type() {
        $labels = array(
            'name' => __('Eventos', 'wp-associates'),
            'singular_name' => __('Evento', 'wp-associates'),
            'add_new' => __('Adicionar Novo', 'wp-associates'),
            'add_new_item' => __('Adicionar Novo Evento', 'wp-associates'),
            'edit_item' => __('Editar Evento', 'wp-associates'),
            'new_item' => __('Novo Evento', 'wp-associates'),
            'view_item' => __('Ver Evento', 'wp-associates'),
            'search_items' => __('Buscar Eventos', 'wp-associates'),
            'not_found' => __('Nenhum evento encontrado', 'wp-associates'),
            'not_found_in_trash' => __('Nenhum evento encontrado na lixeira', 'wp-associates'),
            'all_items' => __('Todos os Eventos', 'wp-associates'),
            'archives' => __('Arquivo de Eventos', 'wp-associates'),
            'attributes' => __('Atributos do Evento', 'wp-associates'),
            'insert_into_item' => __('Inserir no evento', 'wp-associates'),
            'uploaded_to_this_item' => __('Enviado para este evento', 'wp-associates'),
            'featured_image' => __('Imagem do Evento', 'wp-associates'),
            'set_featured_image' => __('Definir imagem do evento', 'wp-associates'),
            'remove_featured_image' => __('Remover imagem do evento', 'wp-associates'),
            'use_featured_image' => __('Usar como imagem do evento', 'wp-associates'),
            'menu_name' => __('Eventos', 'wp-associates'),
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-calendar-alt',
            'menu_position' => 5,
            'supports' => array('title', 'thumbnail'),
            'show_in_rest' => true,
            'rewrite' => false,
            'publicly_queryable' => false,
        );
        
        register_post_type($this->post_type_slug, $args);
    }
    
    /**
     * Altera o texto da imagem destacada
     */
    public function change_featured_image_text($content, $post_id) {
        if (get_post_type($post_id) === $this->post_type_slug) {
            $content = str_replace('Featured image', __('Imagem do Evento', 'wp-associates'), $content);
            $content = str_replace('Set featured image', __('Definir imagem do evento', 'wp-associates'), $content);
            $content = str_replace('Remove featured image', __('Remover imagem do evento', 'wp-associates'), $content);
            $content = str_replace('Replace featured image', __('Substituir imagem do evento', 'wp-associates'), $content);
        }
        return $content;
    }
    
    /**
     * Adiciona estilos CSS no admin para forçar aparência de post type
     */
    public function add_admin_styles() {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === $this->post_type_slug) {
            echo '<style>
                .post-type-event .editor-post-title__input::placeholder {
                    content: "Adicionar título do Evento";
                }
                .post-type-event .editor-post-title__input::placeholder::before {
                    content: "Adicionar título do Evento";
                }
                .post-type-event .editor-post-title__input {
                    font-size: 2.5rem;
                    font-weight: 600;
                    line-height: 1.2;
                    color: #1e1e1e;
                    border: none;
                    outline: none;
                    box-shadow: none;
                }
            </style>';
        }
    }
    
    /**
     * Obtém o slug do post type
     */
    public function get_post_type_slug() {
        return $this->post_type_slug;
    }
}

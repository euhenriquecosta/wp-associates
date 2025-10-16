<?php
namespace Associates;

/**
 * Classe para gerenciar a Taxonomia de Categorias de Associados
 *
 * @package Associates
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe Taxonomy
 */
class Taxonomy {
    
    /**
     * Instância única da classe
     */
    private static $instance = null;
    
    /**
     * Slug da taxonomia
     */
    private $taxonomy = 'associate_category';
    
    /**
     * Post type associado
     */
    private $post_type = 'associate';
    
    /**
     * Construtor privado para implementar Singleton
     */
    private function __construct() {
        add_action('init', array($this, 'register_taxonomy_and_terms'), 5);
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
     * Registra a taxonomia e seus termos
     */
    public function register_taxonomy_and_terms() {
        // Registrar taxonomia
        register_taxonomy($this->taxonomy, $this->post_type, array(
            'hierarchical' => true,
            'labels' => array(
                'name' => __('Categorias de Associados', 'wp-associates'),
                'singular_name' => __('Categoria de Associado', 'wp-associates'),
                'search_items' => __('Buscar Categorias', 'wp-associates'),
                'all_items' => __('Todas as Categorias', 'wp-associates'),
                'parent_item' => __('Categoria Pai', 'wp-associates'),
                'parent_item_colon' => __('Categoria Pai:', 'wp-associates'),
                'edit_item' => __('Editar Categoria', 'wp-associates'),
                'update_item' => __('Atualizar Categoria', 'wp-associates'),
                'add_new_item' => __('Adicionar Nova Categoria', 'wp-associates'),
                'new_item_name' => __('Nome da Nova Categoria', 'wp-associates'),
                'menu_name' => __('Categorias', 'wp-associates'),
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'associate-category'),
            'show_in_rest' => true,
        ));
        
        // Termos padrão
        $default_terms = array(
            'Associação Comercial',
            'Sindicato',
            'Cooperativa',
            'ONG',
            'Instituto',
            'Fundação',
            'Outros'
        );
        
        // Inserir termos se não existirem
        foreach ($default_terms as $term_name) {
            if (!term_exists($term_name, $this->taxonomy)) {
                wp_insert_term($term_name, $this->taxonomy);
            }
        }
    }
    
    /**
     * Obtém todos os termos da taxonomia
     */
    public function get_terms() {
        return get_terms(array(
            'taxonomy' => $this->taxonomy,
            'hide_empty' => false,
        ));
    }
    
    /**
     * Obtém os termos de um post específico
     */
    public function get_post_terms($post_id) {
        $terms = wp_get_post_terms($post_id, $this->taxonomy, array('fields' => 'ids'));
        return is_wp_error($terms) ? array() : $terms;
    }
    
    /**
     * Obtém o slug da taxonomia
     */
    public function get_taxonomy_slug() {
        return $this->taxonomy;
    }
    
    /**
     * Obtém o post type associado
     */
    public function get_post_type() {
        return $this->post_type;
    }
}
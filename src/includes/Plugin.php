<?php
namespace Associates;

use Associates\Associates\PostType as AssociatesPostType;
use Associates\Associates\Taxonomy as AssociatesTaxonomy;
use Associates\Associates\Metabox as AssociatesMetabox;
use Associates\Associates\Shortcode as AssociatesShortcode;
use Associates\Events\PostType as EventPostType;
use Associates\Events\Metabox as EventMetabox;
use Associates\Events\Shortcode as EventShortcode;
use Associates\Elementor\DynamicTags;
use Associates\Elementor\ElementorSupport;

/**
 * Plugin Name: WP Associates
 * Description: Plugin para registrar associados com nome, localização, imagem e filtros interativos com mapa.
 * Version: 2.7
 * Author: Henrique Costa
 * Text Domain: wp-associates
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principal do plugin WP Associates
 */
class Plugin {
    
    /**
     * Versão do plugin
     */
    const VERSION = '2.7';
    
    /**
     * Instância única do plugin
     */
    private static $instance = null;
    
    /**
     * Caminho do plugin
     */
    private $plugin_path;
    
    /**
     * URL do plugin
     */
    private $plugin_url;
    
    /**
     * Nome do plugin
     */
    private $plugin_name;
    
    /**
     * Construtor privado para implementar Singleton
     */
    private function __construct() {
        $this->plugin_path = defined('WPA_PLUGIN_PATH') ? WPA_PLUGIN_PATH . '/src/' : plugin_dir_path(__FILE__);
        $this->plugin_url = defined('WPA_PLUGIN_URL') ? WPA_PLUGIN_URL . '/src/' : plugin_dir_url(__FILE__);
        $this->plugin_name = 'wp-associates';
        
        $this->init_hooks();
    }
    
    /**
     * Retorna a instância única do plugin
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
        // Hooks específicos podem ser adicionados aqui
    }
    
    /**
     * Inicializa o plugin
     */
    public function init() {
        $this->init_components();
    }
    
    /**
     * Inicializa os componentes do plugin
     */
    private function init_components() {
        // Inicializar componentes de Associados
        AssociatesPostType::get_instance();
        AssociatesTaxonomy::get_instance();
        AssociatesMetabox::get_instance();
        AssociatesShortcode::get_instance();
        
        // Inicializar componentes de Eventos
        EventPostType::get_instance();
        EventMetabox::get_instance();
        EventShortcode::get_instance();
        
        // Inicializar integração com Elementor
        if (did_action('elementor/loaded')) {
            DynamicTags::get_instance();
            ElementorSupport::get_instance();
        }
    }
    
    
    /**
     * Ativação do plugin
     */
    public function activate() {
        // Registrar post types e taxonomias
        AssociatesPostType::get_instance()->register_post_type();
        AssociatesTaxonomy::get_instance()->register_taxonomy_and_terms();
        EventPostType::get_instance()->register_post_type();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Desativação do plugin
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Retorna o caminho do plugin
     */
    public function get_plugin_path() {
        return $this->plugin_path;
    }
    
    /**
     * Retorna a URL do plugin
     */
    public function get_plugin_url() {
        return $this->plugin_url;
    }
    
    /**
     * Retorna o nome do plugin
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }
    
    /**
     * Retorna a versão do plugin
     */
    public function get_version() {
        return self::VERSION;
    }
}


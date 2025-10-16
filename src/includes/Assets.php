<?php
namespace Associates;

use Associates\Plugin;

/**
 * Classe para gerenciar Scripts e Estilos
 *
 * @package Associates
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe Assets
 */
class Assets {
    
    /**
     * Instância única da classe
     */
    private static $instance = null;
    
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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Enfileira os assets do frontend
     */
    public function enqueue_frontend_assets() {
        // Leaflet CSS
        wp_enqueue_style(
            'leaflet-css',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            array(),
            '1.9.4'
        );
        
        // Leaflet JS
        wp_enqueue_script(
            'leaflet-js',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            array(),
            '1.9.4',
            true
        );
        
        // CSS do plugin
        $css_file = $this->plugin->get_plugin_path() . 'styles.css';
        if (file_exists($css_file)) {
            $css_version = filemtime($css_file);
            wp_enqueue_style(
                'wp-associates-css',
                $this->plugin->get_plugin_url() . 'styles.css',
                array(),
                $css_version
            );
        }
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
            $js_file = $this->plugin->get_plugin_path() . 'script.js';
            if (file_exists($js_file)) {
                $js_version = filemtime($js_file);
                wp_enqueue_script(
                    'wp-associates-admin-js',
                    $this->plugin->get_plugin_url() . 'script.js',
                    array('jquery', 'wp-util'),
                    $js_version,
                    true
                );
            }
            
            // CSS do plugin para admin
            $css_file = $this->plugin->get_plugin_path() . 'styles.css';
            if (file_exists($css_file)) {
                $css_version = filemtime($css_file);
                wp_enqueue_style(
                    'wp-associates-admin-css',
                    $this->plugin->get_plugin_url() . 'styles.css',
                    array(),
                    $css_version
                );
            }
            
            // Adicionar variáveis JavaScript para o admin
            wp_localize_script('wp-associates-admin-js', 'wpAssociatesAdmin', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wp_associates_admin_nonce'),
                'strings' => array(
                    'selectImages' => __('Selecionar Fotos', 'wp-associates'),
                    'addImages' => __('Adicionar Fotos', 'wp-associates'),
                    'removeImage' => __('Remover foto', 'wp-associates'),
                ),
            ));
        }
    }
    
    /**
     * Retorna a URL de um asset
     */
    public function get_asset_url($asset_path) {
        return $this->plugin->get_plugin_url() . $asset_path;
    }
    
    /**
     * Retorna o caminho de um asset
     */
    public function get_asset_path($asset_path) {
        return $this->plugin->get_plugin_path() . $asset_path;
    }
    
    /**
     * Verifica se um arquivo de asset existe
     */
    public function asset_exists($asset_path) {
        return file_exists($this->get_asset_path($asset_path));
    }
    
    /**
     * Retorna a versão de um asset baseada no filemtime
     */
    public function get_asset_version($asset_path) {
        $full_path = $this->get_asset_path($asset_path);
        if (file_exists($full_path)) {
            return filemtime($full_path);
        }
        return $this->plugin->get_version();
    }
}

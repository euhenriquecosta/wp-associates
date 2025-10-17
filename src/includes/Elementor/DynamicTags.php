<?php
namespace Associates\Elementor;

if (!defined('ABSPATH')) {
    exit;
}

use Associates\Elementor\ElementorIntegration;
use Associates\Elementor\ElementorSupport;

/**
 * Classe para registrar Dynamic Tags customizadas do Elementor
 */
class DynamicTags {
    
    /**
     * Instância única da classe
     */
    private static $instance = null;
    
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
     * Construtor privado para implementar Singleton
     */
    private function __construct() {
        add_action('elementor/dynamic_tags/register', array($this, 'register_tags'));
        
        // Inicializar integração com Elementor
        ElementorIntegration::get_instance();
        ElementorSupport::get_instance();
    }
    
    /**
     * Registra as Dynamic Tags customizadas
     */
    public function register_tags($dynamic_tags_manager) {
        // Verificar se Elementor está ativo
        if (!did_action('elementor/loaded')) {
            return;
        }
        
        // Registrar grupo customizado
        \Elementor\Plugin::$instance->dynamic_tags->register_group(
            'wp-associates',
            ['title' => 'WP Associates']
        );
        
        // Registrar a tag da galeria
        $dynamic_tags_manager->register(new EventGalleryTag());
    }
}

<?php
namespace Associates\Elementor;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Adiciona suporte para CPTs customizados no Elementor
 */
class ElementorSupport {
    
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
        add_action('elementor/init', array($this, 'init_elementor_support'));
    }
    
    /**
     * Inicializa o suporte ao Elementor
     */
    public function init_elementor_support() {
        // Adicionar suporte para CPTs no Elementor
        add_action('init', array($this, 'add_elementor_support'), 20);
    }
    
    /**
     * Adiciona suporte do Elementor aos CPTs customizados
     */
    public function add_elementor_support() {
        
    }
    
    
}

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
        // Inicialização simplificada - apenas Dynamic Tags
    }
    
    
}

<?php
namespace Associates\Elementor;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Integração com Elementor para suporte a CPTs customizados
 */
class ElementorIntegration {
    
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
        add_action('elementor/init', array($this, 'init_elementor_integration'));
    }
    
    /**
     * Inicializa a integração com o Elementor
     */
    public function init_elementor_integration() {
        // Adicionar suporte para CPTs customizados no Loop Builder
        add_filter('elementor/theme/conditions_groups', array($this, 'add_custom_post_type_groups'));
        add_filter('elementor/theme/conditions_types', array($this, 'add_custom_post_type_conditions'));
    }
    
    /**
     * Adiciona grupos de condições para CPTs customizados
     */
    public function add_custom_post_type_groups($groups) {
        $groups['wp_associates'] = array(
            'label' => 'WP Associates',
        );
        
        return $groups;
    }
    
    /**
     * Adiciona tipos de condições para CPTs customizados
     */
    public function add_custom_post_type_conditions($types) {
        $types['associate'] = array(
            'label' => 'Associado',
            'group' => 'wp_associates',
        );
        
        $types['event'] = array(
            'label' => 'Evento',
            'group' => 'wp_associates',
        );
        
        return $types;
    }
}

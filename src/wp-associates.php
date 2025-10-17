<?php
/**
 * Plugin Name:       WP Associates
 * Plugin URI:        https://github.com/henriquecosta/wp-associates
 * Description:       Plugin para registrar associados com nome, localização, imagem e filtros interativos com mapa.
 * Version:           2.7
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            Henrique Costa
 * Author URI:        https://github.com/henriquecosta
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package         WP_Associates
 */

defined('ABSPATH') || exit;

// Definir constantes do plugin
define('WPA_PLUGIN_FILE', __FILE__);
define('WPA_PLUGIN_PATH', untrailingslashit(plugin_dir_path(WPA_PLUGIN_FILE)));
define('WPA_PLUGIN_URL', untrailingslashit(plugins_url('/', WPA_PLUGIN_FILE)));

// Carregar autoloader do Composer
$autoload_paths = array(
    WPA_PLUGIN_PATH . '/vendor/autoload.php',  // Para distribuição
    dirname(WPA_PLUGIN_PATH) . '/vendor/autoload.php',  // Para desenvolvimento
    dirname(dirname(WPA_PLUGIN_PATH)) . '/vendor/autoload.php'  // Para Docker
);

$autoloader_loaded = false;
foreach ($autoload_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoloader_loaded = true;
        break;
    }
}

// Se o autoloader não foi carregado, usar autoloader simples
if (!$autoloader_loaded) {
    spl_autoload_register(function ($class) {
        // Namespace Associates
        if (strpos($class, 'Associates\\') === 0) {
            $class_file = str_replace('Associates\\', '', $class);
            $class_file = str_replace('\\', '/', $class_file);
            $file_path = WPA_PLUGIN_PATH . '/includes/' . $class_file . '.php';
            
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    });
}

// Incluir o arquivo principal do plugin
require_once WPA_PLUGIN_PATH . '/includes/Plugin.php';

// Verificar se a classe existe e inicializar
if (class_exists('Associates\Plugin')) {
    
    /**
     * Função helper para acessar a instância do plugin
     */
    function WPA() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
        return \Associates\Plugin::get_instance();
    }
    
    // Inicializar o plugin
    add_action('plugins_loaded', array(WPA(), 'init'));
    
    // Hooks de ativação e desativação
    register_activation_hook(WPA_PLUGIN_FILE, array(WPA(), 'activate'));
    register_deactivation_hook(WPA_PLUGIN_FILE, array(WPA(), 'deactivate'));
}


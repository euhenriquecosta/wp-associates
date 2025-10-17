<?php
namespace Associates\Elementor;

if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

/**
 * Dynamic Tag para galeria de imagens dos eventos
 */
class EventGalleryTag extends Data_Tag {
    
    /**
     * Nome da tag
     */
    public function get_name() {
        return 'event-gallery';
    }
    
    /**
     * Título da tag
     */
    public function get_title() {
        return 'Galeria do Evento';
    }
    
    /**
     * Grupo da tag
     */
    public function get_group() {
        return 'wp-associates';
    }
    
    /**
     * Categorias da tag
     */
    public function get_categories() {
        return [TagsModule::GALLERY_CATEGORY];
    }
    
    /**
     * Retorna os dados da galeria
     */
    public function get_value(array $options = []) {
        $post_id = get_the_ID();
        
        // Verificar se é um post do tipo 'event'
        if (get_post_type($post_id) !== 'event') {
            return [];
        }
        
        $gallery_images = get_post_meta($post_id, '_event_gallery_images', true);
        
        if (empty($gallery_images)) {
            return [];
        }
        
        $images_data = [];
        $image_ids = explode(',', $gallery_images);
        
        foreach ($image_ids as $image_id) {
            $image_id = trim($image_id);
            if (empty($image_id)) {
                continue;
            }
            
            // Verificar se a imagem existe
            if (wp_get_attachment_image_url($image_id)) {
                $images_data[] = [
                    'id' => intval($image_id),
                ];
            }
        }
        
        return $images_data;
    }
}

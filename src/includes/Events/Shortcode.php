<?php
namespace Associates\Events;

if (!defined('ABSPATH')) {
    exit;
}

use Associates\Plugin;

/**
 * Shortcode para exibir eventos
 */
class Shortcode {
    
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
        add_action('init', array($this, 'register_shortcode'));
    }
    
    /**
     * Registra o shortcode
     */
    public function register_shortcode() {
        add_shortcode('eventos', array($this, 'eventos_shortcode'));
    }
    
    /**
     * Callback do shortcode [eventos]
     */
    public function eventos_shortcode($atts) {
        $atts = shortcode_atts(array(
            'posts_per_page' => 6,
            'columns' => 3,
            'show_gallery' => 'true',
            'show_title' => 'true',
            'show_featured_image' => 'true',
            'carousel' => 'false',
            'autoplay' => 'true',
            'speed' => '3000',
        ), $atts);
        
        $args = array(
            'post_type' => 'event',
            'posts_per_page' => intval($atts['posts_per_page']),
            'post_status' => 'publish',
            'meta_key' => '_event_gallery_images',
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        $events_query = new \WP_Query($args);
        
        if (!$events_query->have_posts()) {
            return '<p>' . __('Nenhum evento encontrado.', 'wp-associates') . '</p>';
        }
        
        ob_start();
        
        if ($atts['carousel'] === 'true') {
            $this->render_carousel($events_query, $atts);
        } else {
            echo '<div class="eventos-container" style="display: grid; grid-template-columns: repeat(' . intval($atts['columns']) . ', 1fr); gap: 20px;">';
            
            while ($events_query->have_posts()) {
                $events_query->the_post();
                $this->render_event_card($atts);
            }
            
            echo '</div>';
        }
        
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    /**
     * Renderiza o carrossel com efeito oval
     */
    private function render_carousel($events_query, $atts) {
        $unique_id = 'eventos-carousel-' . uniqid();
        $speed = intval($atts['speed']);
        $autoplay = $atts['autoplay'] === 'true' ? 'true' : 'false';
        
        echo '<div class="eventos-carousel-wrapper" id="' . $unique_id . '" style="position: relative; margin: 40px 0;">';
        
        // Div oval superior
        echo '<div class="carousel-oval-top" style="
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 40px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 50%;
            z-index: 10;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        "></div>';
        
        // Container do carrossel
        echo '<div class="carousel-container" style="
            position: relative;
            overflow: hidden;
            height: 300px;
            background: linear-gradient(45deg, #f8f9fa, #ffffff);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 20px 0;
        ">';
        
        // Carrossel interno
        echo '<div class="carousel-track" style="
            display: flex;
            transition: transform 0.5s ease-in-out;
            height: 100%;
            align-items: center;
        ">';
        
        // Duplicar posts para loop infinito
        $posts_array = array();
        while ($events_query->have_posts()) {
            $events_query->the_post();
            $posts_array[] = get_post();
        }
        
        // Renderizar posts duplicados
        for ($i = 0; $i < 3; $i++) {
            foreach ($posts_array as $post) {
                setup_postdata($post);
                echo '<div class="carousel-slide" style="
                    min-width: 280px;
                    margin: 0 15px;
                    flex-shrink: 0;
                ">';
                $this->render_carousel_card($atts);
                echo '</div>';
            }
        }
        
        echo '</div>'; // carousel-track
        
        // Div oval inferior
        echo '<div class="carousel-oval-bottom" style="
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 40px;
            background: linear-gradient(45deg, #e9ecef 0%, #f8f9fa 100%);
            border-radius: 50%;
            z-index: 10;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.1);
        "></div>';
        
        echo '</div>'; // carousel-container
        echo '</div>'; // eventos-carousel-wrapper
        
        // JavaScript para autoplay
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const carousel = document.querySelector("#' . $unique_id . ' .carousel-track");
            const slides = carousel.querySelectorAll(".carousel-slide");
            const slideWidth = slides[0].offsetWidth + 30; // width + margin
            let currentPosition = 0;
            let isAnimating = false;
            
            function moveCarousel() {
                if (isAnimating) return;
                isAnimating = true;
                
                currentPosition -= slideWidth;
                carousel.style.transform = "translateX(" + currentPosition + "px)";
                
                setTimeout(() => {
                    if (Math.abs(currentPosition) >= slideWidth * ' . count($posts_array) . ') {
                        currentPosition = 0;
                        carousel.style.transition = "none";
                        carousel.style.transform = "translateX(0px)";
                        setTimeout(() => {
                            carousel.style.transition = "transform 0.5s ease-in-out";
                        }, 10);
                    }
                    isAnimating = false;
                }, 500);
            }
            
            if (' . $autoplay . ') {
                setInterval(moveCarousel, ' . $speed . ');
            }
            
            // Pausar no hover
            carousel.addEventListener("mouseenter", function() {
                clearInterval(window.eventosCarouselInterval);
            });
        });
        </script>';
        
        // CSS adicional
        echo '<style>
        .eventos-carousel-wrapper .carousel-slide {
            transition: transform 0.3s ease;
        }
        .eventos-carousel-wrapper .carousel-slide:hover {
            transform: scale(1.05);
            z-index: 5;
        }
        </style>';
    }
    
    /**
     * Renderiza um card de evento para o carrossel
     */
    private function render_carousel_card($atts) {
        $gallery_images = get_post_meta(get_the_ID(), '_event_gallery_images', true);
        $gallery_array = !empty($gallery_images) ? explode(',', $gallery_images) : array();
        
        echo '<div class="evento-card-carousel" style="
            border: 2px solid #fff;
            border-radius: 15px;
            overflow: hidden;
            background: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        ">';
        
        // Imagem destacada
        if ($atts['show_featured_image'] === 'true' && has_post_thumbnail()) {
            echo '<div class="evento-image" style="position: relative;">';
            the_post_thumbnail('medium', array('style' => 'width: 100%; height: 180px; object-fit: cover;'));
            
            // Overlay com título
            if ($atts['show_title'] === 'true') {
                echo '<div style="
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    background: linear-gradient(transparent, rgba(0,0,0,0.7));
                    color: white;
                    padding: 20px 15px 15px;
                ">';
                echo '<h3 style="margin: 0; font-size: 16px; font-weight: 600;">';
                the_title();
                echo '</h3>';
                echo '</div>';
            }
            echo '</div>';
        } else if ($atts['show_title'] === 'true') {
            echo '<div class="evento-content" style="padding: 20px;">';
            echo '<h3 class="evento-title" style="margin: 0 0 15px 0; font-size: 18px; color: #333;">';
            the_title();
            echo '</h3>';
        }
        
        // Galeria de imagens
        if ($atts['show_gallery'] === 'true' && !empty($gallery_array)) {
            echo '<div class="evento-gallery" style="
                display: flex;
                gap: 5px;
                padding: 10px 15px;
                background: #f8f9fa;
                border-top: 1px solid #e9ecef;
            ">';
            $gallery_count = 0;
            foreach ($gallery_array as $image_id) {
                if ($gallery_count >= 6) break;
                $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                if ($image_url) {
                    echo '<img src="' . esc_url($image_url) . '" alt="' . __('Imagem do evento', 'wp-associates') . '" style="
                        width: 35px;
                        height: 35px;
                        object-fit: cover;
                        border-radius: 4px;
                        border: 1px solid #ddd;
                    ">';
                    $gallery_count++;
                }
            }
            if (count($gallery_array) > 6) {
                echo '<div style="
                    width: 35px;
                    height: 35px;
                    background: #6c757d;
                    border-radius: 4px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 10px;
                    color: white;
                    font-weight: bold;
                ">+' . (count($gallery_array) - 6) . '</div>';
            }
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Renderiza um card de evento
     */
    private function render_event_card($atts) {
        $gallery_images = get_post_meta(get_the_ID(), '_event_gallery_images', true);
        $gallery_array = !empty($gallery_images) ? explode(',', $gallery_images) : array();
        
        echo '<div class="evento-card" style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: white;">';
        
        // Imagem destacada
        if ($atts['show_featured_image'] === 'true' && has_post_thumbnail()) {
            echo '<div class="evento-image">';
            the_post_thumbnail('medium', array('style' => 'width: 100%; height: 200px; object-fit: cover;'));
            echo '</div>';
        }
        
        echo '<div class="evento-content" style="padding: 15px;">';
        
        // Título
        if ($atts['show_title'] === 'true') {
            echo '<h3 class="evento-title" style="margin: 0 0 10px 0; font-size: 18px;">';
            echo '<a href="' . get_permalink() . '" style="text-decoration: none; color: #333;">';
            the_title();
            echo '</a>';
            echo '</h3>';
        }
        
        // Galeria de imagens
        if ($atts['show_gallery'] === 'true' && !empty($gallery_array)) {
            echo '<div class="evento-gallery" style="display: flex; gap: 5px; margin-top: 10px;">';
            $gallery_count = 0;
            foreach ($gallery_array as $image_id) {
                if ($gallery_count >= 4) break; // Limitar a 4 imagens
                $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                if ($image_url) {
                    echo '<img src="' . esc_url($image_url) . '" alt="' . __('Imagem do evento', 'wp-associates') . '" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">';
                    $gallery_count++;
                }
            }
            if (count($gallery_array) > 4) {
                echo '<div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #666;">+' . (count($gallery_array) - 4) . '</div>';
            }
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
    }
}

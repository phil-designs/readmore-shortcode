<?php
/**
 * Plugin Name: Lightweight Readmore Shortcode
 * Plugin URI:  http://www.phildesigns.com
 * Description: Wrap any visual editor content in [readmore] to collapse/expand it with "Read More" / "Close" links. Supports multiple instances per page.
 * Version:     1.0.0
 * Author:      phil.designs | Phillip De Vita
 * Author URI:  http://www.phildesigns.com
 * License:     GPL-2.0+
 * Text Domain: readmore-shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Readmore_Shortcode {

    /** Collects per-instance JS init calls to output in the footer. */
    private static $init_scripts = array();

    /** Counter so every shortcode on the page gets a unique element ID. */
    private static $instance_counter = 0;

    public static function init() {
        add_shortcode( 'readmore', array( __CLASS__, 'render_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
        // Priority 99 ensures this runs after wp_print_footer_scripts (priority 20),
        // so readmore.js is guaranteed to be in the DOM before our init calls.
        add_action( 'wp_footer', array( __CLASS__, 'output_footer_scripts' ), 99 );
    }

    /**
     * Shortcode handler.
     *
     * Supported attributes:
     *   collapsed_height  – px height when collapsed          (default: 200)
     *   speed             – animation speed in ms             (default: 100)
     *   more_text         – label for the "expand" link       (default: Read More)
     *   less_text         – label for the "collapse" link     (default: Close)
     *   start_open        – true/false, start expanded        (default: false)
     *   height_margin     – extra px tolerance before hiding  (default: 16)
     */
    public static function render_shortcode( $atts, $content = '' ) {
        if ( empty( $content ) ) {
            return '';
        }

        $atts = shortcode_atts(
            array(
                'collapsed_height' => 80,
                'speed'            => 100,
                'more_text'        => 'Read More',
                'less_text'        => 'Close',
                'start_open'       => 'false',
                'height_margin'    => 0,
            ),
            $atts,
            'readmore'
        );

        self::$instance_counter++;
        $element_id = 'readmore-instance-' . self::$instance_counter;

        // Build JS options for this instance.
        // blockCSS is intentionally kept minimal — readmore.js default adds `width: 100%`
        // which causes layout issues when the shortcode is placed inside inline content.
        $js_options = array(
            'collapsedHeight' => (int) $atts['collapsed_height'],
            'speed'           => (int) $atts['speed'],
            'heightMargin'    => (int) $atts['height_margin'],
            'startOpen'       => ( $atts['start_open'] === 'true' ),
            'blockCSS'        => 'display: block;',
            'moreLink'        => '<a href="#">' . esc_html( $atts['more_text'] ) . '</a>',
            'lessLink'        => '<a href="#">' . esc_html( $atts['less_text'] ) . '</a>',
        );

        // Store for footer output so all inits fire after jQuery + readmore.js are loaded.
        self::$init_scripts[ $element_id ] = $js_options;

        // Apply wpautop so visual editor paragraph formatting is preserved inside the wrapper.
        $inner = wpautop( do_shortcode( $content ) );

        return sprintf(
            '<div class="readmore-shortcode-wrap" id="%s">%s</div>',
            esc_attr( $element_id ),
            $inner
        );
    }

    /** Output one jQuery init call per instance, safely in the footer. */
    public static function output_footer_scripts() {
        if ( empty( self::$init_scripts ) ) {
            return;
        }

        echo '<script>' . "\n";
        echo 'jQuery(function($){' . "\n";

        foreach ( self::$init_scripts as $id => $options ) {
            $json = wp_json_encode( $options );
            printf( '  $("#%s").readmore(%s);' . "\n", esc_js( $id ), $json );
        }

        echo '});' . "\n";
        echo '</script>' . "\n";
    }

    public static function enqueue_assets() {
        wp_enqueue_script( 'jquery' );

        wp_enqueue_script(
            'readmore-js',
            plugin_dir_url( __FILE__ ) . 'js/readmore.js',
            array( 'jquery' ),
            '2.2.0',
            true   // load in footer
        );

        wp_enqueue_style(
            'readmore-shortcode-css',
            plugin_dir_url( __FILE__ ) . 'css/readmore-shortcode.css',
            array(),
            '1.0.0'
        );
    }
}

Readmore_Shortcode::init();

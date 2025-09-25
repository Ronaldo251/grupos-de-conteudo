<?php
/**
 * Plugin Name:       Seções de Conteúdos Agrupados
 * Description:       Crie seções contendo grupos de páginas e exiba-as com um shortcode dinâmico.
 * Version:           2.2
 * Author:            Ronaldo Fraga - MPCE
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SCD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SCD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once SCD_PLUGIN_DIR . 'includes/post-type.php';
require_once SCD_PLUGIN_DIR . 'includes/meta-box.php';
require_once SCD_PLUGIN_DIR . 'includes/shortcode.php';

function scd_carregar_assets() {
    wp_register_style( 'scd-public-styles', SCD_PLUGIN_URL . 'css/public-styles.css' );
}
add_action( 'wp_enqueue_scripts', 'scd_carregar_assets' );

function scd_carregar_admin_assets( $hook ) {
    global $post;
    if ( ( 'post.php' == $hook || 'post-new.php' == $hook ) && isset($post) && 'secao_conteudo' === $post->post_type ) {
        wp_enqueue_style( 'scd-admin-styles', SCD_PLUGIN_URL . 'css/admin-styles.css' );
        wp_enqueue_script(
            'scd-admin-scripts',
            SCD_PLUGIN_URL . 'js/admin-scripts.js',
            array( 'jquery', 'jquery-ui-sortable' ),
            '1.1',
            true
        );
        wp_localize_script( 'scd-admin-scripts', 'scd_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce('scd_admin_nonce') 
        ));
    }
}
add_action( 'admin_enqueue_scripts', 'scd_carregar_admin_assets' );

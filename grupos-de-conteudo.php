<?php
/**
 * Plugin Name:       Grupos de Conteúdo Dinâmico
 * Description:       Crie grupos de conteúdo personalizados com busca AJAX e exiba-os com um shortcode.
 * Version:           1.0
 * Author:            Seu Nome
 */

// Previne o acesso direto
if ( ! defined( 'ABSPATH' ) ) exit;

// Define constantes para os caminhos do plugin
define( 'GCD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GCD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Inclui os arquivos necessários
require_once GCD_PLUGIN_DIR . 'includes/post-type.php';
require_once GCD_PLUGIN_DIR . 'includes/meta-box.php';
require_once GCD_PLUGIN_DIR . 'includes/shortcode.php';

// Função para carregar scripts e estilos
function gcd_carregar_assets() {
    // Estilos para a parte pública (shortcode)
    wp_register_style(
        'gcd-public-styles',
        GCD_PLUGIN_URL . 'css/public-styles.css'
    );
}
add_action( 'wp_enqueue_scripts', 'gcd_carregar_assets' );

// Função para carregar scripts e estilos do painel de administração
function gcd_carregar_admin_assets( $hook ) {
    // Carrega apenas na página de edição do nosso post type
    if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
        global $post;
        if ( 'grupos_conteudo' === $post->post_type ) {
            wp_enqueue_style(
                'gcd-admin-styles',
                GCD_PLUGIN_URL . 'css/admin-styles.css'
            );
            wp_enqueue_script(
                'gcd-admin-scripts',
                GCD_PLUGIN_URL . 'js/admin-scripts.js',
                array( 'jquery', 'jquery-ui-sortable' ), // Adiciona dependência para reordenar
                '1.0',
                true
            );
            // Passa a URL do AJAX para o JavaScript de forma segura
            wp_localize_script( 'gcd-admin-scripts', 'gcd_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        }
    }
}
add_action( 'admin_enqueue_scripts', 'gcd_carregar_admin_assets' );

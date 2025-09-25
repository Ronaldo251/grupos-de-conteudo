<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function gcd_registrar_post_type() {
    $labels = array(
        'name'               => 'Grupos de Conteúdo',
        'singular_name'      => 'Grupo de Conteúdo',
        'menu_name'          => 'Grupos de Conteúdo',
        'add_new_item'       => 'Adicionar Novo Grupo',
        'edit_item'          => 'Editar Grupo',
    );
    $args = array(
        'label'              => 'Grupo de Conteúdo',
        'labels'             => $labels,
        'public'             => false, // Não serão páginas públicas individuais
        'show_ui'            => true,  // Mostra no painel de admin
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-networking', // Ícone
        'supports'           => array( 'title' ), // Suporta apenas título
        'rewrite'            => false,
    );
    register_post_type( 'grupos_conteudo', $args );
}
add_action( 'init', 'gcd_registrar_post_type' );

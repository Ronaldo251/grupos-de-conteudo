<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function scd_registrar_post_type() {
    $labels = array(
        'name'               => 'Seções de Conteúdo',
        'singular_name'      => 'Seção de Conteúdo',
        'menu_name'          => 'Seções de Conteúdo',
        'add_new_item'       => 'Adicionar Nova Seção',
        'edit_item'          => 'Editar Seção',
    );
    $args = array(
        'label'              => 'Seção de Conteúdo',
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-layout',
        'supports'           => array( 'title' ),
        'rewrite'            => false,
    );
    register_post_type( 'secao_conteudo', $args );
}
add_action( 'init', 'scd_registrar_post_type' );

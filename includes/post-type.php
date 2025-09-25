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

/**
 * @param array $columns As colunas existentes.
 * @return array As colunas modificadas.
 */
function scd_adicionar_coluna_shortcode( $columns ) {

    $new_columns = array();
    foreach ( $columns as $key => $title ) {
        if ( $key == 'date' ) {
            $new_columns['shortcode'] = 'Shortcode';
        }
        $new_columns[$key] = $title;
    }
    return $new_columns;
}
add_filter( 'manage_secao_conteudo_posts_columns', 'scd_adicionar_coluna_shortcode' );

/**
 * @param string $column O nome da coluna atual.
 * @param int $post_id O ID do post (da seção).
 */
function scd_exibir_conteudo_coluna_shortcode( $column, $post_id ) {
    if ( 'shortcode' === $column ) {
        $shortcode = '[secao_conteudo id="' . $post_id . '"]';
        echo '<input type="text" value="' . esc_attr( $shortcode ) . '" readonly onfocus="this.select();" style="width:100%;">';
    }
}
add_action( 'manage_secao_conteudo_posts_custom_column', 'scd_exibir_conteudo_coluna_shortcode', 10, 2 );
function scd_ajustar_largura_coluna() {
    echo '<style type="text/css">';
    echo '.column-shortcode { width: 250px !important; }';
    echo '</style>';
}
add_action( 'admin_head', 'scd_ajustar_largura_coluna' );

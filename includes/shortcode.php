<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function scd_shortcode_callback( $atts ) {
    $atts = shortcode_atts( array( 'id' => 0 ), $atts, 'secao_conteudo' );
    $secao_id = intval( $atts['id'] );

    if ( !$secao_id || get_post_type($secao_id) !== 'secao_conteudo' ) {
        return '<p>ID da seção inválido ou não encontrado.</p>';
    }

    wp_enqueue_style('scd-public-styles');

    $grupos = get_post_meta( $secao_id, '_scd_grupos', true );

    if ( empty( $grupos ) ) {
        return '<p>Esta seção não possui grupos de conteúdo.</p>';
    }

    ob_start();
    echo '<div class="scd-grid">';

    foreach ( $grupos as $grupo ) {
        echo '<div class="scd-column">';
        echo '<h3>' . esc_html( $grupo['titulo'] ) . '</h3>';

        if ( ! empty( $grupo['paginas'] ) ) {
            echo '<ul>';
            foreach ( $grupo['paginas'] as $pagina ) {
                echo '<li><a href="' . get_permalink( $pagina['id'] ) . '">' . esc_html( $pagina['titulo'] ) . '</a></li>';
            }
            echo '</ul>';
        }
        echo '</div>';
    }

    echo '</div>';
    return ob_get_clean();
}
add_shortcode( 'secao_conteudo', 'scd_shortcode_callback' );

<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function gcd_shortcode_callback( $atts ) {
    // Carrega os estilos apenas quando o shortcode é usado
    wp_enqueue_style('gcd-public-styles');

    // Busca todos os "Grupos de Conteúdo" publicados
    $query = new WP_Query( array(
        'post_type' => 'grupos_conteudo',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ) );

    if ( ! $query->have_posts() ) {
        return '<p>Nenhum grupo de conteúdo encontrado.</p>';
    }

    ob_start();
    echo '<div class="gcd-grid">';

    while ( $query->have_posts() ) {
        $query->the_post();
        $group_id = get_the_ID();
        $selected_ids = get_post_meta( $group_id, '_gcd_selected_posts', true );

        echo '<div class="gcd-column">';
        echo '<h3>' . get_the_title() . '</h3>';

        if ( ! empty( $selected_ids ) ) {
            echo '<ul>';
            foreach ( $selected_ids as $item_id ) {
                echo '<li><a href="' . get_permalink( $item_id ) . '">' . get_the_title( $item_id ) . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Nenhum item neste grupo.</p>';
        }
        echo '</div>'; // .gcd-column
    }

    echo '</div>'; // .gcd-grid
    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode( 'grupos_de_conteudo', 'gcd_shortcode_callback' );

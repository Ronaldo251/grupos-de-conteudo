<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Adiciona a Meta Box
function gcd_adicionar_meta_box() {
    add_meta_box(
        'gcd_conteudo_selecionado',
        'Conteúdo do Grupo',
        'gcd_meta_box_callback',
        'grupos_conteudo', // Mostra no nosso CPT
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'gcd_adicionar_meta_box' );

// HTML da Meta Box
function gcd_meta_box_callback( $post ) {
    wp_nonce_field( 'gcd_salvar_meta_box_data', 'gcd_meta_box_nonce' );
    ?>
    <div class="gcd-container">
        <div class="gcd-search-wrapper">
            <label for="gcd-search-input"><strong>Pesquisar Páginas ou Posts:</strong></label>
            <input type="text" id="gcd-search-input" placeholder="Digite para pesquisar...">
            <div id="gcd-search-results"></div>
        </div>
        <div class="gcd-selected-wrapper">
            <strong>Páginas/Posts Selecionados (arraste para reordenar):</strong>
            <ul id="gcd-selected-items">
                <?php
                $selected_ids = get_post_meta( $post->ID, '_gcd_selected_posts', true );
                if ( ! empty( $selected_ids ) ) {
                    foreach ( $selected_ids as $id ) {
                        echo '<li data-id="' . esc_attr($id) . '">' . esc_html(get_the_title($id)) . ' (' . get_post_type($id) . ') <span class="remove-item">×</span></li>';
                    }
                }
                ?>
            </ul>
            <input type="hidden" id="gcd-selected-posts-input" name="gcd_selected_posts_input" value="<?php echo esc_attr( implode( ',', $selected_ids ) ); ?>">
        </div>
    </div>
    <?php
}

// Função AJAX para a busca
function gcd_ajax_search() {
    $search_query = sanitize_text_field( $_POST['query'] );
    $args = array(
        'post_type'      => array('page', 'post'), // Pesquisa em páginas e posts
        'posts_per_page' => 10,
        's'              => $search_query,
    );
    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            echo '<div class="result-item" data-id="' . get_the_ID() . '">' . get_the_title() . ' (' . get_post_type() . ')</div>';
        }
    } else {
        echo '<div class="no-results">Nenhum resultado encontrado.</div>';
    }
    wp_die(); // Termina a execução AJAX
}
add_action( 'wp_ajax_gcd_search_posts', 'gcd_ajax_search' );

// Salva os dados da Meta Box
function gcd_salvar_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['gcd_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['gcd_meta_box_nonce'], 'gcd_salvar_meta_box_data' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['gcd_selected_posts_input'] ) ) {
        $ids = explode( ',', sanitize_text_field( $_POST['gcd_selected_posts_input'] ) );
        $ids = array_filter( array_map( 'intval', $ids ) ); // Limpa e converte para inteiros
        update_post_meta( $post_id, '_gcd_selected_posts', $ids );
    } else {
        delete_post_meta( $post_id, '_gcd_selected_posts' );
    }
}
add_action( 'save_post', 'gcd_salvar_meta_box_data' );

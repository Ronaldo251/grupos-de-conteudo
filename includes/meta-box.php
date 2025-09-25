<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Adiciona a Meta Box principal e a do Shortcode
function scd_adicionar_meta_boxes() {
    add_meta_box(
        'scd_grupos_conteudo',
        'Grupos e Páginas da Seção',
        'scd_meta_box_callback',
        'secao_conteudo', 'normal', 'high'
    );
    add_meta_box(
        'scd_shortcode_display',
        'Shortcode da Seção',
        'scd_shortcode_meta_box_callback',
        'secao_conteudo', 'side', 'default'
    );
}
add_action( 'add_meta_boxes', 'scd_adicionar_meta_boxes' );

// Callback para a Meta Box do Shortcode
function scd_shortcode_meta_box_callback( $post ) {
    if ( $post->post_status != 'publish' ) {
        echo '<p>Publique a seção para gerar o shortcode.</p>';
        return;
    }
    echo '<p>Copie e cole este shortcode na página onde deseja exibir esta seção:</p>';
    echo '<input type="text" value="[secao_conteudo id=&quot;' . $post->ID . '&quot;]" readonly style="width:100%;">';
}

// Callback para a Meta Box principal
function scd_meta_box_callback( $post ) {
    wp_nonce_field( 'scd_salvar_dados', 'scd_nonce' );
    $grupos = get_post_meta( $post->ID, '_scd_grupos', true );
    ?>
    <div id="scd-container">
        <div id="scd-grupos-wrapper">
            <!-- Grupos existentes serão carregados aqui pelo JS -->
        </div>
        <button type="button" id="scd-add-grupo" class="button button-primary">Adicionar Novo Grupo</button>
        
        <!-- Campo de busca de páginas (será movido pelo JS para dentro do grupo ativo) -->
        <div id="scd-search-field-template" style="display: none;">
            <input type="text" class="scd-search-input" placeholder="Digite para pesquisar páginas...">
            <div class="scd-search-results"></div>
        </div>
    </div>
    
    <!-- Armazena os dados em formato JSON para salvar -->
    <textarea name="scd_grupos_data" id="scd_grupos_data" style="display:none;"><?php echo esc_textarea( json_encode( $grupos ? $grupos : [] ) ); ?></textarea>
    <?php
}

// Função AJAX para buscar APENAS páginas
function scd_ajax_search_pages() {
    check_ajax_referer('scd_admin_nonce', 'nonce'); // Verificação de segurança
    $search_query = sanitize_text_field( $_POST['query'] );
    $query = new WP_Query( array(
        'post_type'      => 'page', // ALTERADO: Apenas páginas
        'posts_per_page' => 10,
        's'              => $search_query,
    ) );

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            echo '<div class="result-item" data-id="' . get_the_ID() . '">' . get_the_title() . '</div>';
        }
    } else {
        echo '<div class="no-results">Nenhuma página encontrada.</div>';
    }
    wp_die();
}
add_action( 'wp_ajax_scd_search_pages', 'scd_ajax_search_pages' );

// Salva os dados da Meta Box
function scd_salvar_dados( $post_id ) {
    if ( !isset($_POST['scd_nonce']) || !wp_verify_nonce($_POST['scd_nonce'], 'scd_salvar_dados') ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( !current_user_can('edit_post', $post_id) ) return;

    if ( isset( $_POST['scd_grupos_data'] ) ) {
        $dados = json_decode( stripslashes( $_POST['scd_grupos_data'] ), true );
        update_post_meta( $post_id, '_scd_grupos', $dados );
    }
}
add_action( 'save_post_secao_conteudo', 'scd_salvar_dados' );

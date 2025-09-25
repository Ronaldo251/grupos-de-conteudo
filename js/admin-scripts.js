jQuery(document).ready(function($) {
    let searchTimeout;

    // Busca AJAX
    $('#gcd-search-input').on('keyup', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val();
        const resultsContainer = $('#gcd-search-results');

        if (query.length < 3) {
            resultsContainer.hide();
            return;
        }

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: gcd_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'gcd_search_posts',
                    query: query
                },
                success: function(response) {
                    resultsContainer.html(response).show();
                }
            });
        }, 500); // Espera 500ms antes de buscar
    });

    // Adiciona item à lista de selecionados
    $(document).on('click', '#gcd-search-results .result-item', function() {
        const id = $(this).data('id');
        const title = $(this).text();
        
        // Evita adicionar duplicados
        if ($('#gcd-selected-items li[data-id="' + id + '"]').length === 0) {
            $('#gcd-selected-items').append('<li data-id="' + id + '">' + title + ' <span class="remove-item">×</span></li>');
            updateHiddenInput();
        }
        $('#gcd-search-results').hide();
        $('#gcd-search-input').val('');
    });

    // Remove item da lista
    $(document).on('click', '#gcd-selected-items .remove-item', function() {
        $(this).parent('li').remove();
        updateHiddenInput();
    });

    // Torna a lista reordenável
    $('#gcd-selected-items').sortable({
        update: function() {
            updateHiddenInput();
        }
    }).disableSelection();

    // Atualiza o campo hidden que será salvo
    function updateHiddenInput() {
        const ids = $('#gcd-selected-items li').map(function() {
            return $(this).data('id');
        }).get();
        $('#gcd-selected-posts-input').val(ids.join(','));
    }

    // Esconde resultados ao clicar fora
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.gcd-search-wrapper').length) {
            $('#gcd-search-results').hide();
        }
    });
});

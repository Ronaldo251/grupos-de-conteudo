jQuery(document).ready(function($) {
    const container = $('#scd-grupos-wrapper');
    const dataTextarea = $('#scd_grupos_data');

    // Função para gerar um ID único para cada grupo
    const uniqueId = () => 'grupo_' + Math.random().toString(36).substr(2, 9);

    // Carrega os grupos salvos ao iniciar
    function carregarGrupos() {
        const dados = JSON.parse(dataTextarea.val() || '[]');
        dados.forEach(grupoData => criarGrupoNoDOM(grupoData));
        atualizarDados();
    }

    // Cria a representação visual de um grupo
    function criarGrupoNoDOM(grupoData) {
        const grupoId = grupoData.id || uniqueId();
        const grupoTitulo = grupoData.titulo || 'Novo Grupo';
        const paginas = grupoData.paginas || [];

        const grupoHTML = `
            <div class="scd-grupo" data-id="${grupoId}">
                <div class="grupo-header">
                    <span class="dashicons dashicons-move"></span>
                    <input type="text" class="grupo-titulo" value="${grupoTitulo}">
                    <button type="button" class="button scd-remove-grupo">Remover Grupo</button>
                </div>
                <div class="grupo-content">
                    <ul class="scd-selected-items">
                        ${paginas.map(p => `<li data-id="${p.id}">${p.titulo} <span class="remove-item">×</span></li>`).join('')}
                    </ul>
                    <div class="scd-search-wrapper">
                        <input type="text" class="scd-search-input" placeholder="Pesquisar e adicionar páginas...">
                        <div class="scd-search-results"></div>
                    </div>
                </div>
            </div>`;
        container.append(grupoHTML);
    }

    // Adiciona um novo grupo
    $('#scd-add-grupo').on('click', function() {
        criarGrupoNoDOM({});
        atualizarDados();
    });

    // Remove um grupo
    container.on('click', '.scd-remove-grupo', function() {
        $(this).closest('.scd-grupo').remove();
        atualizarDados();
    });

    // Busca AJAX de páginas
    let searchTimeout;
    container.on('keyup', '.scd-search-input', function() {
        clearTimeout(searchTimeout);
        const input = $(this);
        const query = input.val();
        const resultsContainer = input.next('.scd-search-results');

        if (query.length < 2) {
            resultsContainer.hide();
            return;
        }
        searchTimeout = setTimeout(() => {
            $.ajax({
                url: scd_ajax.ajax_url,
                type: 'POST',
                data: { action: 'scd_search_pages', query: query, nonce: scd_ajax.nonce },
                success: function(response) {
                    resultsContainer.html(response).show();
                }
            });
        }, 400);
    });

    // Adiciona página ao grupo
    container.on('click', '.result-item', function() {
        const id = $(this).data('id');
        const titulo = $(this).text();
        const lista = $(this).closest('.grupo-content').find('.scd-selected-items');
        
        if (lista.find(`li[data-id="${id}"]`).length === 0) {
            lista.append(`<li data-id="${id}">${titulo} <span class="remove-item">×</span></li>`);
            atualizarDados();
        }
        $(this).closest('.scd-search-results').hide();
        $(this).closest('.scd-search-wrapper').find('.scd-search-input').val('');
    });

    // Remove página do grupo
    container.on('click', '.remove-item', function() {
        $(this).parent('li').remove();
        atualizarDados();
    });

    // Atualiza o textarea com os dados em JSON
    function atualizarDados() {
        const dados = [];
        $('.scd-grupo').each(function() {
            const grupo = $(this);
            const paginas = [];
            grupo.find('.scd-selected-items li').each(function() {
                paginas.push({ id: $(this).data('id'), titulo: $(this).text().replace(' ×', '') });
            });
            dados.push({
                id: grupo.data('id'),
                titulo: grupo.find('.grupo-titulo').val(),
                paginas: paginas
            });
        });
        dataTextarea.val(JSON.stringify(dados));
    }
    
    // Evento para atualizar dados ao mudar o título do grupo
    container.on('change', '.grupo-titulo', atualizarDados);

    // Habilita a reordenação (Sortable)
    container.sortable({ // Reordenar GRUPOS
        handle: '.grupo-header',
        axis: 'y',
        update: atualizarDados
    });
    container.on('mousedown', '.scd-selected-items', function() {
        $(this).sortable({ // Reordenar PÁGINAS dentro de um grupo
            axis: 'y',
            update: atualizarDados
        });
    });

    // Esconde resultados da busca ao clicar fora
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.scd-search-wrapper').length) {
            $('.scd-search-results').hide();
        }
    });

    // Inicia o processo
    carregarGrupos();
});

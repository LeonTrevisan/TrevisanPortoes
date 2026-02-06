// Funções de navegação
function showPage(pageId, element) {
    const pages = document.querySelectorAll('.page');
    const menuItems = document.querySelectorAll('.menu-item');

    pages.forEach(page => page.classList.remove('active'));
    menuItems.forEach(item => item.classList.remove('active'));

    document.getElementById(pageId).classList.add('active');
    element.classList.add('active');

    // Carregar conteúdo da página se necessário
    loadPageContent(pageId);
}

function voltarParaDashboard(button) {
    const page = button.dataset.page;
    window.location.href = baseUrl + '/?page=' + page;
}

function loadPageContent(pageId) {
    const pageDiv = document.getElementById(pageId);
    if (pageId === 'clientes' && pageDiv.innerHTML.trim() === '') {
        fetch(baseUrl + '/clientes')
            .then(response => response.text())
            .then(html => {
                pageDiv.innerHTML = `
                    <div class="page-header">
                        <h2>Clientes</h2>
                        <p>Gerencie os clientes</p>
                    </div>
                    <button class="btn btn-primary" onclick="openModal('modalCliente')">Novo Cliente</button>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Telefone</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${html}
                        </tbody>
                    </table>
                `;
            });
    }
    // Adicionar para outras páginas se necessário
}

const fichaClienteCache = {};

function formatEnderecoCliente(cliente) {
    if (!cliente) {
        return '';
    }
    const ruaNumero = [cliente.rua, cliente.numero].filter(Boolean).join(', ');
    const partes = [];
    if (ruaNumero) {
        partes.push(ruaNumero);
    }
    if (cliente.bairro) {
        partes.push(cliente.bairro);
    }
    if (cliente.cidade) {
        partes.push(cliente.cidade);
    }
    if (cliente.complemento) {
        partes.push(cliente.complemento);
    }
    return partes.join(' - ');
}

function preencherFichaCliente(cliente) {
    const nomeEl = document.getElementById('ficha_cliente_nome');
    const enderecoEl = document.getElementById('ficha_cliente_endereco');
    if (nomeEl) {
        nomeEl.textContent = cliente ? (cliente.nome || '') : '';
    }
    if (enderecoEl) {
        enderecoEl.textContent = cliente ? formatEnderecoCliente(cliente) : '';
    }
}

function carregarClienteFicha(id) {
    if (!id) {
        preencherFichaCliente(null);
        return;
    }
    if (fichaClienteCache[id]) {
        preencherFichaCliente(fichaClienteCache[id]);
        return;
    }
    fetch(baseUrl + '/clientes/obter?id=' + encodeURIComponent(id))
        .then(response => response.ok ? response.json() : Promise.reject(response))
        .then(data => {
            fichaClienteCache[id] = data;
            preencherFichaCliente(data);
        })
        .catch(() => {
            preencherFichaCliente(null);
        });
}

function setFichaTipo(tipo) {
    const generica = document.getElementById('ficha-generica');
    const especifica = document.getElementById('ficha-especifica');
    const wrapper = document.getElementById('ficha-cliente-wrapper');
    const isEspecifica = tipo === 'especifica';

    if (generica) {
        generica.classList.toggle('active', !isEspecifica);
    }
    if (especifica) {
        especifica.classList.toggle('active', isEspecifica);
    }
    if (wrapper) {
        wrapper.style.display = isEspecifica ? 'block' : 'none';
    }
}

function setupFichaPage() {
    const radios = document.querySelectorAll('input[name="ficha_tipo"]');
    if (!radios.length) {
        return;
    }
    const checked = document.querySelector('input[name="ficha_tipo"]:checked');
    setFichaTipo(checked ? checked.value : 'generica');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            setFichaTipo(radio.value);
        });
    });

    const select = document.getElementById('ficha_cliente_select');
    if (select) {
        select.addEventListener('change', function() {
            carregarClienteFicha(select.value);
        });
        if (select.value) {
            carregarClienteFicha(select.value);
        }
    }
}

function imprimirFicha() {
    window.print();
}

// Funções de modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

function irParaPendentes() {
    window.location.href = baseUrl + '/?page=servicos&status_pagamento=' + encodeURIComponent('Pendente');
}

function irParaCompras30Dias() {
    window.location.href = baseUrl + '/?page=pecas&compra_filter_type=periodo&compra_filter_value=30';
}

function irParaServicos30Dias() {
    window.location.href = baseUrl + '/?page=servicos&servico_filter_type=periodo&servico_filter_value=30';
}

function normalizeSearchText(text) {
    return (text || '')
        .toString()
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

function filterTableRows(tbodyId, columns, term) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) {
        return;
    }
    const normalizedTerm = normalizeSearchText(term);
    const rows = Array.from(tbody.rows || []);

    rows.forEach(row => {
        if (!row.cells || row.cells.length === 0) {
            return;
        }
        if (row.cells.length === 1 && row.cells[0].colSpan > 1) {
            row.style.display = normalizedTerm ? 'none' : '';
            return;
        }
        if (!normalizedTerm) {
            row.style.display = '';
            return;
        }
        const match = columns.some(index => {
            const cell = row.cells[index];
            if (!cell) {
                return false;
            }
            const text = normalizeSearchText(cell.textContent || '');
            return text.includes(normalizedTerm);
        });
        row.style.display = match ? '' : 'none';
    });
}

function setupSearchInput(inputId, tbodyId, columns) {
    const input = document.getElementById(inputId);
    if (!input) {
        return;
    }
    const handler = function() {
        filterTableRows(tbodyId, columns, input.value);
    };
    input.addEventListener('input', handler);
    handler();
}

function parseDateDdMmYyyy(text) {
    const parts = (text || '').trim().split('/');
    if (parts.length !== 3) {
        return null;
    }
    const day = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10);
    const year = parseInt(parts[2], 10);
    if (!day || !month || !year) {
        return null;
    }
    return new Date(year, month - 1, day);
}

function getDateRange(type, value) {
    if (!type || !value) {
        return null;
    }
    const now = new Date();
    if (type === 'periodo') {
        const days = parseInt(value, 10);
        if (!days) {
            return null;
        }
        const end = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59, 999);
        const start = new Date(end);
        start.setDate(start.getDate() - (days - 1));
        start.setHours(0, 0, 0, 0);
        return { start, end };
    }
    if (type === 'ano') {
        const year = parseInt(value, 10);
        if (!year) {
            return null;
        }
        const start = new Date(year, 0, 1, 0, 0, 0, 0);
        const end = new Date(year, 11, 31, 23, 59, 59, 999);
        return { start, end };
    }
    return null;
}

function populatePeriodYearSelect(select, type) {
    if (!select) {
        return;
    }
    select.innerHTML = '<option value="">Selecione</option>';
    if (!type) {
        select.disabled = true;
        return;
    }
    if (type === 'periodo') {
        select.insertAdjacentHTML('beforeend',
            '<option value="7">7 dias</option>' +
            '<option value="15">15 dias</option>' +
            '<option value="30">30 dias</option>'
        );
    } else if (type === 'ano') {
        const currentYear = new Date().getFullYear();
        for (let year = currentYear; year >= currentYear - 4; year -= 1) {
            select.insertAdjacentHTML('beforeend', '<option value="' + year + '">' + year + '</option>');
        }
    }
    select.disabled = false;
}

function applyCompraFilters() {
    const tbody = document.getElementById('compraTable');
    if (!tbody) {
        return;
    }
    const searchInput = document.getElementById('search_compra');
    const filterType = document.getElementById('compra_filter_type');
    const filterValue = document.getElementById('compra_filter_value');
    const searchTerm = searchInput ? normalizeSearchText(searchInput.value) : '';
    const range = getDateRange(filterType ? filterType.value : '', filterValue ? filterValue.value : '');

    const rows = Array.from(tbody.rows || []);
    rows.forEach(row => {
        if (!row.cells || row.cells.length === 0) {
            return;
        }
        if (row.cells.length === 1 && row.cells[0].colSpan > 1) {
            row.style.display = (searchTerm || range) ? 'none' : '';
            return;
        }

        const materialText = normalizeSearchText((row.cells[1] && row.cells[1].textContent) || '');
        const distribuidoraText = normalizeSearchText((row.cells[5] && row.cells[5].textContent) || '');
        const matchesSearch = !searchTerm || materialText.includes(searchTerm) || distribuidoraText.includes(searchTerm);

        let matchesDate = true;
        if (range) {
            const dateText = (row.cells[0] && row.cells[0].textContent) || '';
            const compraDate = parseDateDdMmYyyy(dateText);
            matchesDate = compraDate ? (compraDate >= range.start && compraDate <= range.end) : false;
        }

        row.style.display = (matchesSearch && matchesDate) ? '' : 'none';
    });
}

function setupCompraFilters() {
    const searchInput = document.getElementById('search_compra');
    const filterType = document.getElementById('compra_filter_type');
    const filterValue = document.getElementById('compra_filter_value');

    if (searchInput) {
        searchInput.addEventListener('input', applyCompraFilters);
    }
    if (filterType) {
        filterType.addEventListener('change', function() {
            populatePeriodYearSelect(filterValue, filterType.value);
            applyCompraFilters();
        });
    }
    if (filterValue) {
        filterValue.addEventListener('change', applyCompraFilters);
    }

    populatePeriodYearSelect(filterValue, filterType ? filterType.value : '');
    applyCompraFilters();
}

function applyCompraFilterFromUrl() {
    const filterType = document.getElementById('compra_filter_type');
    const filterValue = document.getElementById('compra_filter_value');
    if (!filterType || !filterValue) {
        return;
    }
    const urlParams = new URLSearchParams(window.location.search);
    const typeParam = urlParams.get('compra_filter_type');
    const valueParam = urlParams.get('compra_filter_value');
    if (!typeParam || !valueParam) {
        return;
    }
    filterType.value = typeParam;
    populatePeriodYearSelect(filterValue, filterType.value);
    const optionExists = Array.from(filterValue.options || []).some(option => option.value === valueParam);
    if (optionExists) {
        filterValue.value = valueParam;
    }
    applyCompraFilters();
}

function applyServicoFilters() {
    const tbody = document.getElementById('servicoTable');
    const list = document.getElementById('servicoList');
    if (!tbody && !list) {
        return;
    }
    const searchInput = document.getElementById('search_servico');
    const tipoSelect = document.getElementById('servico_tipo_filter');
    const statusSelect = document.getElementById('servico_status_filter');
    const filterType = document.getElementById('servico_filter_type');
    const filterValue = document.getElementById('servico_filter_value');
    const searchTerm = searchInput ? normalizeSearchText(searchInput.value) : '';
    const tipoTerm = tipoSelect ? normalizeSearchText(tipoSelect.value) : '';
    const statusTerm = statusSelect ? normalizeSearchText(statusSelect.value) : '';
    const range = getDateRange(filterType ? filterType.value : '', filterValue ? filterValue.value : '');

    if (tbody) {
        const layout = (tbody.dataset && tbody.dataset.layout) ? tbody.dataset.layout : 'main';
        const dateIndex = layout === 'ficha' ? 0 : 2;
        const tipoIndex = 1;
        const clienteIndex = layout === 'ficha' ? null : 0;
        const rows = Array.from(tbody.rows || []);
        rows.forEach(row => {
            if (!row.cells || row.cells.length === 0) {
                return;
            }
            if (row.cells.length === 1 && row.cells[0].colSpan > 1) {
                row.style.display = (searchTerm || range) ? 'none' : '';
                return;
            }

            const clienteText = clienteIndex === null ? '' : normalizeSearchText((row.cells[clienteIndex] && row.cells[clienteIndex].textContent) || '');
            const tipoText = normalizeSearchText((row.cells[tipoIndex] && row.cells[tipoIndex].textContent) || '');
            const matchesSearch = !searchTerm || (clienteIndex !== null && clienteText.includes(searchTerm));
            const matchesTipo = !tipoTerm || tipoText === tipoTerm;
            const rowStatus = normalizeSearchText((row.dataset && row.dataset.status) ? row.dataset.status : '');
            const matchesStatus = !statusTerm || rowStatus === statusTerm;

            let matchesDate = true;
            if (range) {
                const dateText = (row.cells[dateIndex] && row.cells[dateIndex].textContent) || '';
                const servicoDate = parseDateDdMmYyyy(dateText);
                matchesDate = servicoDate ? (servicoDate >= range.start && servicoDate <= range.end) : false;
            }

            row.style.display = (matchesSearch && matchesTipo && matchesStatus && matchesDate) ? '' : 'none';
        });
        return;
    }

    const items = Array.from(list.querySelectorAll('li'));
    items.forEach(item => {
        const tipoText = normalizeSearchText(item.dataset.tipo || '');
        const rowStatus = normalizeSearchText(item.dataset.status || '');
        const dateText = item.dataset.date || '';
        const matchesSearch = !searchTerm;
        const matchesTipo = !tipoTerm || tipoText === tipoTerm;
        const matchesStatus = !statusTerm || rowStatus === statusTerm;

        let matchesDate = true;
        if (range) {
            const servicoDate = parseDateDdMmYyyy(dateText);
            matchesDate = servicoDate ? (servicoDate >= range.start && servicoDate <= range.end) : false;
        }

        item.style.display = (matchesSearch && matchesTipo && matchesStatus && matchesDate) ? '' : 'none';
    });
}

function setupServicoFilters() {
    const searchInput = document.getElementById('search_servico');
    const tipoSelect = document.getElementById('servico_tipo_filter');
    const statusSelect = document.getElementById('servico_status_filter');
    const filterType = document.getElementById('servico_filter_type');
    const filterValue = document.getElementById('servico_filter_value');

    if (searchInput) {
        searchInput.addEventListener('input', applyServicoFilters);
    }
    if (tipoSelect) {
        tipoSelect.addEventListener('change', applyServicoFilters);
    }
    if (statusSelect) {
        statusSelect.addEventListener('change', applyServicoFilters);
    }
    if (filterType) {
        filterType.addEventListener('change', function() {
            populatePeriodYearSelect(filterValue, filterType.value);
            applyServicoFilters();
        });
    }
    if (filterValue) {
        filterValue.addEventListener('change', applyServicoFilters);
    }

    populatePeriodYearSelect(filterValue, filterType ? filterType.value : '');
    applyServicoFilters();
}

function applyServicoStatusFromUrl() {
    const statusSelect = document.getElementById('servico_status_filter');
    if (!statusSelect) {
        return;
    }
    const urlParams = new URLSearchParams(window.location.search);
    const statusParam = urlParams.get('status_pagamento');
    if (!statusParam) {
        return;
    }
    const normalized = normalizeSearchText(statusParam);
    const options = Array.from(statusSelect.options || []);
    const match = options.find(option => normalizeSearchText(option.value) === normalized);
    if (match) {
        statusSelect.value = match.value;
        applyServicoFilters();
    }
}

function applyServicoFilterFromUrl() {
    const filterType = document.getElementById('servico_filter_type');
    const filterValue = document.getElementById('servico_filter_value');
    if (!filterType || !filterValue) {
        return;
    }
    const urlParams = new URLSearchParams(window.location.search);
    const typeParam = urlParams.get('servico_filter_type');
    const valueParam = urlParams.get('servico_filter_value');
    if (!typeParam || !valueParam) {
        return;
    }
    filterType.value = typeParam;
    populatePeriodYearSelect(filterValue, filterType.value);
    const optionExists = Array.from(filterValue.options || []).some(option => option.value === valueParam);
    if (optionExists) {
        filterValue.value = valueParam;
    }
    applyServicoFilters();
}

function updateClienteSearchStatus(select) {
    const status = document.getElementById('cliente_search_status');
    if (!status) {
        return;
    }
    const term = (select && select.dataset && select.dataset.searchTerm) ? select.dataset.searchTerm : '';
    if (term) {
        status.textContent = 'Filtro: "' + term + '"';
    } else {
        status.textContent = 'Digite para filtrar';
    }
}

function filterClienteSelect(select, term) {
    if (!select) {
        return;
    }
    const options = Array.from(select.options);
    const normalized = normalizeSearchText(term);

    options.forEach(option => {
        if (!option.dataset.search) {
            option.dataset.search = normalizeSearchText(option.textContent || '');
        }
        if (option.value === '') {
            option.hidden = false;
            return;
        }
        option.hidden = normalized !== '' && !option.dataset.search.includes(normalized);
    });

    const selected = select.options[select.selectedIndex];
    if (selected && selected.hidden) {
        select.value = '';
    }
    updateClienteSearchStatus(select);
}

function resetClienteSelectFilter(select) {
    if (!select) {
        return;
    }
    select.dataset.searchTerm = '';
    filterClienteSelect(select, '');
}

function setupClienteSelectSearch() {
    const select = document.getElementById('id_cliente_servico');
    if (!select) {
        return;
    }

    resetClienteSelectFilter(select);

    select.addEventListener('keydown', function(event) {
        const key = event.key;
        if (key === 'Backspace') {
            const term = (select.dataset.searchTerm || '').slice(0, -1);
            select.dataset.searchTerm = term;
            filterClienteSelect(select, term);
            event.preventDefault();
            return;
        }
        if (key === 'Escape' || key === 'Delete') {
            resetClienteSelectFilter(select);
            event.preventDefault();
            return;
        }
        if (key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
            const term = (select.dataset.searchTerm || '') + key;
            select.dataset.searchTerm = term;
            filterClienteSelect(select, term);
            event.preventDefault();
        }
    });

    select.addEventListener('blur', function() {
        resetClienteSelectFilter(select);
    });
}

function novoServico() {
    const formServico = document.getElementById('formServico');
    if (!formServico) {
        return;
    }
    formServico.reset();

    const idInput = document.getElementById('id_servico');
    if (idInput) {
        idInput.value = '';
    }
    const fotoExisting = document.getElementById('foto_existing');
    if (fotoExisting) {
        fotoExisting.value = '';
    }
    const comprovanteExisting = document.getElementById('comprovante_existing');
    if (comprovanteExisting) {
        comprovanteExisting.value = '';
    }
    const fotoCurrent = document.getElementById('foto_current');
    if (fotoCurrent) {
        fotoCurrent.innerHTML = '';
    }
    const comprovanteCurrent = document.getElementById('comprovante_current');
    if (comprovanteCurrent) {
        comprovanteCurrent.innerHTML = '';
    }
    const formaSelect = document.getElementById('id_forma_pagamento');
    if (formaSelect) {
        formaSelect.value = '';
    }
    const clienteSelect = document.getElementById('id_cliente_servico');
    resetClienteSelectFilter(clienteSelect);

    const titulo = document.getElementById('tituloModalServico');
    if (titulo) {
        titulo.textContent = 'Novo Serviço';
    }
    const btnSalvar = document.getElementById('btnSalvarServico');
    if (btnSalvar) {
        btnSalvar.textContent = 'Salvar';
    }
    formServico.action = baseUrl + '/servicos/store';
    toggleFormaPagamento();
    openModal('modalServico');
}

function novoAdmin() {
    const formAdm = document.getElementById('formAdm');
    if (!formAdm) {
        return;
    }
    formAdm.reset();

    const idInput = formAdm.querySelector('input[name="id"]');
    if (idInput) {
        idInput.value = '';
    }
    const titulo = document.getElementById('tituloModalAdm');
    if (titulo) {
        titulo.textContent = 'Novo Administrador';
    }
    const btnSalvar = document.getElementById('btnSalvarAdm');
    if (btnSalvar) {
        btnSalvar.textContent = 'Salvar';
    }
    formAdm.action = baseUrl + '/admin/store';
    openModal('modalAdm');
}

function novoCliente() {
    const formCliente = document.getElementById('formCliente');
    if (!formCliente) {
        return;
    }
    formCliente.reset();

    const idInput = formCliente.querySelector('input[name="id"]');
    if (idInput) {
        idInput.value = '';
    }
    const cnpjExisting = document.getElementById('cnpj_existing');
    if (cnpjExisting) {
        cnpjExisting.value = '';
    }
    const cnpjFile = formCliente.querySelector('input[type="file"][name="cnpj"]');
    if (cnpjFile) {
        cnpjFile.value = '';
    }
    const selectSindico = formCliente.querySelector('select[name="id_sindico"]');
    if (selectSindico) {
        selectSindico.value = '';
    }
    const selectAdmin = formCliente.querySelector('select[name="id_admin"]');
    if (selectAdmin) {
        selectAdmin.value = '';
    }
    const tipoSelect = formCliente.querySelector('select[name="id_tipo_cliente"]');
    if (tipoSelect) {
        tipoSelect.value = '1';
    }

    const titulo = document.getElementById('tituloModalCliente');
    if (titulo) {
        titulo.textContent = 'Novo Cliente';
    }
    const btnSalvar = document.getElementById('btnSalvarCliente');
    if (btnSalvar) {
        btnSalvar.textContent = 'Salvar';
    }
    formCliente.action = baseUrl + '/clientes/store';
    toggleCondominioFields();
    openModal('modalCliente');
}

function novoSindico() {
    const formSindico = document.getElementById('formSindico');
    if (!formSindico) {
        return;
    }
    formSindico.reset();

    const idInput = formSindico.querySelector('input[name="id"]');
    if (idInput) {
        idInput.value = '';
    }
    const titulo = document.getElementById('tituloModalSindico');
    if (titulo) {
        titulo.textContent = 'Novo Síndico';
    }
    const btnSalvar = document.getElementById('btnSalvarSindico');
    if (btnSalvar) {
        btnSalvar.textContent = 'Salvar';
    }
    formSindico.action = baseUrl + '/sindico/store';
    openModal('modalSindico');
}

function novaCompra() {
    const formCompra = document.getElementById('formCompra');
    if (!formCompra) {
        return;
    }
    formCompra.reset();

    const idInput = formCompra.querySelector('input[name="id"]');
    if (idInput) {
        idInput.value = '';
    }
    const titulo = document.getElementById('tituloModalCompra');
    if (titulo) {
        titulo.textContent = 'Nova Compra';
    }
    const btnSalvar = document.getElementById('btnSalvarCompra');
    if (btnSalvar) {
        btnSalvar.textContent = 'Salvar';
    }
    formCompra.action = baseUrl + '/compras/store';
    openModal('modalCompra');
}

// Função para mostrar/esconder campos de condomínio
function toggleCondominioFields() {
    const tipoCliente = document.getElementById('id_tipo_cliente').value;
    const condominioFields = document.getElementById('condominio-fields');

    if (tipoCliente == '2') { // Condomínio
        condominioFields.style.display = 'block';
    } else {
        condominioFields.style.display = 'none';
    }
}

// Função para mostrar/esconder forma de pagamento
function isStatusPagoSelecionado() {
    const statusPagamento = document.getElementById('statusPag');
    if (!statusPagamento) {
        return false;
    }
    const option = statusPagamento.options[statusPagamento.selectedIndex];
    return !!(option && option.dataset && option.dataset.isPaid === '1');
}

// Função para mostrar/esconder forma de pagamento
function toggleFormaPagamento() {
    const statusPagamento = document.getElementById('statusPag');
    const formaPagamento = document.getElementById('forma_pagamento');
    if (!statusPagamento || !formaPagamento) {
        return;
    }
    if (isStatusPagoSelecionado()) {
        formaPagamento.classList.add('active');
    } else {
        formaPagamento.classList.remove('active');
        const formaSelect = document.getElementById('id_forma_pagamento');
        if (formaSelect) {
            formaSelect.value = '';
        }
    }
}

// Funções de edição
function editarAdmin(id) {
    fetch(baseUrl + '/admin/obter?id=' + id)
        .then(r => r.json())
        .then(data => {
            document.getElementById('id_admin').value = data.id_admin;
            document.getElementById('nome_admin').value = data.nome;
            document.getElementById('telefone_admin').value = data.telefone;
            document.getElementById('email_admin').value = data.email;
            document.getElementById('tituloModalAdm').textContent = 'Editar Administrador';
            document.getElementById('btnSalvarAdm').textContent = 'Atualizar';
            document.getElementById('formAdm').action = baseUrl + '/admin/update';
            openModal('modalAdm');
        });
}

function editarCliente(id) {
    fetch(baseUrl + '/clientes/obter?id=' + id)
        .then(r => r.json())
        .then(data => {
            const formCliente = document.getElementById('formCliente');
            const idInput = formCliente ? formCliente.querySelector('input[name="id"]') : null;
            if (idInput) {
                idInput.value = data.id_cliente;
            }
            document.getElementById('nome_cliente').value = data.nome;
            document.getElementById('telefone_cliente').value = data.telefone;
            document.getElementById('email_cliente').value = data.email;
            document.getElementById('id_tipo_cliente').value = data.id_tipo_cliente;
            const cnpjInput = document.getElementById('cnpj_cliente');
            if (cnpjInput) {
                cnpjInput.value = '';
            }
            const cnpjExisting = document.getElementById('cnpj_existing');
            if (cnpjExisting) {
                cnpjExisting.value = data.cnpj || '';
            }
            document.getElementById('id_sindico').value = data.id_sindico || '';
            const adminSelect = formCliente ? formCliente.querySelector('[name="id_admin"]') : null;
            if (adminSelect) {
                adminSelect.value = data.id_admin || '';
            }
            document.getElementById('rua_cliente').value = data.rua || '';
            document.getElementById('numero_cliente').value = data.numero || '';
            document.getElementById('bairro_cliente').value = data.bairro || '';
            document.getElementById('cidade_cliente').value = data.cidade || '';
            document.getElementById('complemento_cliente').value = data.complemento || '';
            document.getElementById('tituloModalCliente').textContent = 'Editar Cliente';
            document.getElementById('btnSalvarCliente').textContent = 'Atualizar';
            document.getElementById('formCliente').action = baseUrl + '/clientes/update';
            toggleCondominioFields();
            openModal('modalCliente');
        });
}

function editarSindico(id) {
    fetch(baseUrl + '/sindico/obter?id=' + encodeURIComponent(id))
        .then(r => r.json())
        .then(data => {
            const idInput = document.querySelector('#formSindico input[name="id"]');
            if (idInput) {
                idInput.value = data.id_sindico;
            }
            document.getElementById('nome_sindico').value = data.nome;
            document.getElementById('telefone_sindico').value = data.telefone;
            document.getElementById('tituloModalSindico').textContent = 'Editar Síndico' + id;
            document.getElementById('btnSalvarSindico').textContent = 'Atualizar';
            document.getElementById('formSindico').action = baseUrl + '/sindico/update';
            openModal('modalSindico');
        });
}

function editarServico(id) {
    fetch(baseUrl + '/servicos/obter?id=' + id)
        .then(r => r.json())
        .then(data => {
            document.getElementById('id_servico').value = data.id_servico;
            document.getElementById('id_cliente_servico').value = data.id_cliente;
            document.getElementById('id_tipo_servico').value = data.id_tipo;
            document.getElementById('data_hora_servico').value = data.data_hora.replace(' ', 'T');
            const statusSelect = document.getElementById('statusPag');
            if (statusSelect) {
                statusSelect.value = data.id_status ? String(data.id_status) : '1';
            }
            const formaSelect = document.getElementById('id_forma_pagamento');
            if (formaSelect) {
                formaSelect.value = data.id_forma_pagamento || '';
            }
            const valorInput = document.getElementById('valor_servico');
            if (valorInput) {
                valorInput.value = data.valor || '';
            }
            toggleFormaPagamento();
            document.getElementById('descricao_servico').value = data.descricao || '';
            document.getElementById('observacao_servico').value = data.observacao || '';
            document.getElementById('foto_existing').value = data.foto || '';
            document.getElementById('comprovante_existing').value = data.comprovante || '';
            document.getElementById('foto_current').innerHTML = data.foto ? '<a href="' + baseUrl + '/' + data.foto + '" target="_blank">Ver Foto Atual</a>' : '';
            document.getElementById('comprovante_current').innerHTML = data.comprovante ? '<a href="' + baseUrl + '/' + data.comprovante + '" target="_blank">Ver Comprovante Atual</a>' : '';
            document.getElementById('tituloModalServico').textContent = 'Editar Serviço';
            document.getElementById('btnSalvarServico').textContent = 'Atualizar';
            document.getElementById('formServico').action = baseUrl + '/servicos/update';
            const clienteSelect = document.getElementById('id_cliente_servico');
            resetClienteSelectFilter(clienteSelect);
            openModal('modalServico');
        });
}

function editarCompra(id) {
    fetch(baseUrl + '/compras/obter?id=' + id)
        .then(r => r.json())
        .then(data => {
            document.getElementById('id_compra').value = data.id_compra;
            document.getElementById('data_compra').value = data.data_compra;
            document.getElementById('material_compra').value = data.material;
            document.getElementById('qtd_compra').value = data.qtd_compra;
            document.getElementById('valor_un_compra').value = data.valor_un;
            document.getElementById('id_distribuidora').value = data.id_distribuidora || '';
            document.getElementById('tituloModalCompra').textContent = 'Editar Compra';
            document.getElementById('btnSalvarCompra').textContent = 'Atualizar';
            document.getElementById('formCompra').action = baseUrl + '/compras/update';
            openModal('modalCompra');
        });
}

// Funções de ver ficha
function verFichaCliente(id) {
    fetch(baseUrl + '/clientes/ficha?id=' + id)
        .then(r => r.text())
        .then(html => {
            const container = document.querySelector('.main-content');
            if (container) {
                container.innerHTML = html;
            }
            setupServicoFilters();
        });
}

function verFichaSindico(id) {
    fetch(baseUrl + '/sindico/ficha', {
        method: 'POST',
        body: new URLSearchParams({id: id})
    })
        .then(r => r.text())
        .then(html => {
            document.querySelector('.main-content').innerHTML = html;
        });
}

function verFichaServico(id) {
    fetch(baseUrl + '/servicos/ficha?id=' + id)
        .then(r => r.text())
        .then(html => {
            document.querySelector('.main-content').innerHTML = html;
        });
}

function verFichaAdmin(id) {
    fetch(baseUrl + '/admin/ficha?id=' + id)
        .then(r => r.text())
        .then(html => {
            document.querySelector('.main-content').innerHTML = html;
        });
}

function verFichaCompra(id) {
    fetch(baseUrl + '/compras/obter?id=' + id)
        .then(r => r.json())
        .then(data => {
            alert('Compra: ' + data.material + ' - Valor: R$ ' + data.valor_total);
        });
}

// Função para filtrar materiais
function filtrarMateriais(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const mesSelecionado = document.getElementById('filtro').value;

    fetch('../backend/php/lista_material.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'filtro-mes=' + encodeURIComponent(mesSelecionado)
    })
    .then(response => response.text())
    .then(data => {
        const tbody = document.querySelector('#pecas table tbody');
        if (tbody) {
            tbody.innerHTML = data;
        }
    })
    .catch(error => {
        console.error('Erro ao filtrar materiais:', error);
        alert('Erro ao carregar os dados. Tente novamente.');
    });

    return false;
}

// Função para alternar entre filtro por período e ano
function atualizarOpcoesFiltro() {
    const tipoFiltro = document.getElementById('tipo-filtro').value;
    const periodContainer = document.getElementById('filtro-periodo-container');
    const anoContainer = document.getElementById('filtro-ano-container');

    if (tipoFiltro === 'periodo') {
        periodContainer.style.display = 'inline-block';
        anoContainer.style.display = 'none';
    } else {
        periodContainer.style.display = 'none';
        anoContainer.style.display = 'inline-block';
    }

    filtrarServicos();
}

// Função para filtrar serviços por período ou ano
function filtrarServicos() {
    const tipoFiltroEl = document.getElementById('tipo-filtro');
    const periodoEl = document.getElementById('filtro-periodo');
    const anoEl = document.getElementById('filtro-ano');
    if (!tipoFiltroEl) {
        return;
    }
    const tipoFiltro = tipoFiltroEl.value;
    let parametroFiltro = '';

    if (tipoFiltro === 'periodo') {
        if (!periodoEl) {
            return;
        }
        parametroFiltro = periodoEl.value;
    } else {
        if (!anoEl) {
            return;
        }
        parametroFiltro = anoEl.value;
    }

    fetch('../backend/php/lista_servicos.php?filtro-periodo=' + encodeURIComponent(parametroFiltro))
        .then(response => response.text())
        .then(html => {
            const tbody = document.getElementById('servicosTable');
            if (tbody) {
                tbody.innerHTML = html;
            }
        })
        .catch(error => console.error('Erro ao filtrar serviços:', error));
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    setupClienteSelectSearch();
    setupFichaPage();
    setupSearchInput('search_admin', 'adminTable', [0, 1]);
    setupSearchInput('search_cliente', 'clienteTable', [0, 2]);
    setupSearchInput('search_sindico', 'sindicoTable', [0, 1]);
    setupCompraFilters();
    applyCompraFilterFromUrl();
    setupServicoFilters();
    applyServicoStatusFromUrl();
    applyServicoFilterFromUrl();

    // Validação do formulário de serviço
    const formServico = document.getElementById('formServico');
    if (formServico) {
        formServico.addEventListener('submit', function(e) {
            const cliente = formServico.querySelector('select[name="id_cliente"]').value;
            const data = formServico.querySelector('input[name="data_hora"]').value;
            const tipo = formServico.querySelector('select[name="id_tipo"]').value;
            const valor = formServico.querySelector('input[name="valor_servico"]').value;
            const status = formServico.querySelector('select[name="id_status"]').value;
            const isPago = isStatusPagoSelecionado();
            const formaSelect = formServico.querySelector('select[name="id_forma_pagamento"]');
            const forma = formaSelect ? formaSelect.value : "";

            if (!cliente || !data || !tipo || !valor || !status || (isPago && !forma)) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios!');
                return false;
            }

            let valorNormalizado = valor.replace(/\s/g, "");
            if (valorNormalizado.includes(",")) {
                valorNormalizado = valorNormalizado.replace(/\./g, "").replace(",", ".");
            }
            if (isNaN(valorNormalizado) || Number(valorNormalizado) <= 0) {
                e.preventDefault();
                alert('O valor deve ser um número maior que zero!');
                return false;
            }
        });
    }

    // Toggle condomínio fields
    document.querySelectorAll('input[name="tipo-cliente"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const condForm = document.getElementById('condform');
            if (condForm) {
                condForm.style.display = this.value === 'Condomínio' ? 'block' : 'none';
            }
        });
    });

    // Forma de pagamento visibility
    const statusPagamento = document.getElementById('statusPag');
    if (statusPagamento) {
        toggleFormaPagamento();
        statusPagamento.addEventListener('change', toggleFormaPagamento);
    }

    // Filtro de materiais
    const formFiltro = document.getElementById('formFiltro');
    const selectFiltro = document.getElementById('filtro');
    const btnFiltrar = document.getElementById('btnFiltrar');

    if (formFiltro) {
        formFiltro.addEventListener('submit', filtrarMateriais);
    }

    if (selectFiltro) {
        selectFiltro.addEventListener('change', filtrarMateriais);
    }

    if (btnFiltrar) {
        btnFiltrar.addEventListener('click', function(e) {
            e.preventDefault();
            filtrarMateriais(e);
            return false;
        });
    }

    // Filtros de serviços legados (se existirem)
    const selectPeriodo = document.getElementById('filtro-periodo');
    const selectAno = document.getElementById('filtro-ano');
    const tipoFiltro = document.getElementById('tipo-filtro');

    if (selectPeriodo) {
        selectPeriodo.addEventListener('change', filtrarServicos);
    }

    if (selectAno) {
        selectAno.addEventListener('change', filtrarServicos);
    }

    if (tipoFiltro && (selectPeriodo || selectAno)) {
        filtrarServicos();
    }

    // Verificar mensagens de URL
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const msg = urlParams.get('msg');

    if (status && msg) {
        if (status === 'success') {
            alert(msg);
        } else if (status === 'error') {
            alert('Erro: ' + msg);
        }
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

// Event listener para fechar modal ao clicar fora
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}

// Verificar parâmetros da URL ao carregar a página
window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    if (page) {
        const element = document.querySelector(`.menu-item[onclick*="showPage('${page}'"]`);
        if (element) {
            showPage(page, element);
        }
    }
    const status = urlParams.get('status');
    if (status === 'success') {
        alert('Operação realizada com sucesso!');
    } else if (status === 'error') {
        const message = urlParams.get('message') || 'Erro desconhecido';
        alert('Erro: ' + decodeURIComponent(message));
    }
    if (status) {
        const cleanParams = new URLSearchParams(window.location.search);
        cleanParams.delete('status');
        cleanParams.delete('message');
        cleanParams.delete('msg');
        const query = cleanParams.toString();
        const cleanUrl = query ? `${window.location.pathname}?${query}` : window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
    }
});


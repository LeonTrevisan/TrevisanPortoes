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
            document.querySelector('.main-content').innerHTML = html;
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
    const tipoFiltro = document.getElementById('tipo-filtro').value;
    let parametroFiltro = '';

    if (tipoFiltro === 'periodo') {
        parametroFiltro = document.getElementById('filtro-periodo').value;
    } else {
        parametroFiltro = document.getElementById('filtro-ano').value;
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

    // Filtros de serviços
    const selectPeriodo = document.getElementById('filtro-periodo');
    const selectAno = document.getElementById('filtro-ano');

    if (selectPeriodo) {
        selectPeriodo.addEventListener('change', filtrarServicos);
    }

    if (selectAno) {
        selectAno.addEventListener('change', filtrarServicos);
    }

    // Carregar filtros padrão
    filtrarServicos();

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

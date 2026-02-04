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
            document.getElementById('id_cliente').value = data.id_cliente;
            document.getElementById('nome_cliente').value = data.nome;
            document.getElementById('telefone_cliente').value = data.telefone;
            document.getElementById('email_cliente').value = data.email;
            document.getElementById('id_tipo_cliente').value = data.id_tipo_cliente;
            document.getElementById('cnpj_cliente').value = data.cnpj || '';
            document.getElementById('id_sindico').value = data.id_sindico || '';
            document.getElementById('id_admin').value = data.id_admin || '';
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
    fetch(baseUrl + '/sindico/obter', {
        method: 'POST',
        body: new URLSearchParams({id: id})
    })
        .then(r => r.json())
        .then(data => {
            document.getElementById('id_sindico').value = data.id_sindico;
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
            const cliente = document.querySelector('select[name="clientes"]').value;
            const data = document.querySelector('input[name="data_hora"]').value;
            const tipo = document.querySelector('select[name="tipo"]').value;
            const preco = document.querySelector('input[name="preco"]').value;
            const status = document.querySelector('select[name="statusPagamento"]').value;
            const forma = document.querySelector('select[name="formaPagamento"]').value;

            if (!cliente || !data || !tipo || !preco || !status || !forma) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios!');
                return false;
            }

            if (isNaN(preco) || preco <= 0) {
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
    const formaPagamento = document.querySelector('#forma_pagamento');

    function verificarStatusPagamento() {
        if (statusPagamento && formaPagamento) {
            if (statusPagamento.value !== '1') {
                formaPagamento.classList.add('active');
            } else {
                formaPagamento.classList.remove('active');
            }
        }
    }

    if (statusPagamento) {
        verificarStatusPagamento();
        statusPagamento.addEventListener('change', verificarStatusPagamento);
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
});
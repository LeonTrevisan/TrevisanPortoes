function showPage(pageId, element) {
    const pages = document.querySelectorAll('.page');
    const menuItems = document.querySelectorAll('.menu-item');
    
    pages.forEach(page => page.classList.remove('active'));
    menuItems.forEach(item => item.classList.remove('active'));
    
    document.getElementById(pageId).classList.add('active');
    element.classList.add('active');
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function abrirNovoServico() {
    const form = document.getElementById('formServico');
    if (form) {
        form.reset();
        document.getElementById('id_servico').value = '';
        document.getElementById('tituloModalServico').textContent = 'Novo Serviço';
        document.getElementById('btnSalvarServico').textContent = 'Salvar';
        form.action = '../backend/php/cadastro_servico.php';
    }
    openModal('modalServico');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

function carregarFicha(id) {
    fetch('ficha_cliente.php?id=' + id)
        .then(r => r.text())
        .then(html => {
            const conteudo = document.getElementById('conteudo');
            if (conteudo) {
                conteudo.innerHTML = html;
            }
        });
}

function editarCliente(id) {
    console.log('Editando cliente ID:', id);
    
    fetch('../backend/php/obterCliente.php?id=' + id)
        .then(response => response.text())
        .then(text => {
            console.log('Resposta recebida');
            const data = JSON.parse(text);
            
            // Preenchendo os campos do formulário
            document.getElementById('id_cliente').value = data.id_cliente;
        
            // Campos de texto
            document.querySelector('input[name="nome-cliente"]').value = data.nome || '';
            document.querySelector('input[name="tel-cliente"]').value = data.telefone || '';
            document.querySelector('input[name="email-cliente"]').value = data.email || '';
            document.querySelector('input[name="rua-cliente"]').value = data.rua || '';
            document.querySelector('input[name="bairro-cliente"]').value = data.bairro || '';
            document.querySelector('input[name="num-cliente"]').value = data.numero || '';
            document.querySelector('input[name="cidade-cliente"]').value = data.cidade || '';
            
            // Selects
            document.querySelector('select[name="adm-cliente"]').value = data.id_admin || '';
            document.querySelector('select[name="sindico-cliente"]').value = data.id_sindico || '';
            
            // Campo oculto para id_endereco (adicione se não existir no HTML)
            const idEnderecoField = document.querySelector('input[name="id_endereco"]');
            if (idEnderecoField) {
                idEnderecoField.value = data.id_endereco || '';
            }
            
            // Atualizar modal e formulário
            document.getElementById('titleModalCliente').textContent = 'Editar Cliente';
            document.getElementById('btnSalvarCliente').textContent = 'Atualizar';
            
            const form = document.querySelector('#modalCliente form');
            form.action = '../backend/php/editarCliente.php';
            
            openModal('modalCliente');
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao carregar o cliente: ' + error.message);
        });
}

function editarServico(id) {
    console.log('Editando serviço ID:', id);
    
    fetch('../backend/php/obter_servico.php?id=' + id)
        .then(response => response.text())
        .then(text => {
            console.log('Resposta recebida');
            const data = JSON.parse(text);
            
            document.getElementById('id_servico').value = data.id_servico;
            document.querySelector('select[name="clientes"]').value = data.id_cliente;
            document.querySelector('input[name="data_hora"]').value = data.data_hora ? data.data_hora.split(' ')[0] : '';
            document.querySelector('select[name="tipo"]').value = data.id_tipo;
            document.querySelector('textarea[name="descricao"]').value = data.descricao || '';
            document.querySelector('textarea[name="observacao"]').value = data.observacao || '';
            document.querySelector('input[name="preco"]').value = data.valor || '';
            document.querySelector('select[name="statusPagamento"]').value = data.id_status;
            document.querySelector('select[name="formaPagamento"]').value = data.id_forma_pagamento;
            
            document.getElementById('tituloModalServico').textContent = 'Editar Serviço';
            document.getElementById('btnSalvarServico').textContent = 'Atualizar';
            document.getElementById('formServico').action = '../backend/php/editar_servico.php';
            
            openModal('modalServico');
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao carregar o serviço: ' + error.message);
        });
}

function editarCompra(id) {

    fetch('../backend/php/obterCompra.php?id=' + id)
        .then(response => response.json())
        .then(data => {

            if (data.error) {
                alert(data.error);
                return;
            }

            document.getElementById('id_compra').value = data.id_compra;
            document.querySelector('input[name="material"]').value = data.material;
            document.querySelector('input[name="data_hora"]').value = data.data_compra;
            document.querySelector('select[name="fornecedor"]').value = data.id_distribuidora;
            document.querySelector('input[name="valor"]').value = data.valor_un;
            document.querySelector('input[name="qtd"]').value = data.qtd_compra;

            document.getElementById('tituloModalCompra').textContent = 'Editar Compra';
            document.getElementById('btnSalvarCompra').textContent = 'Atualizar';
            document.getElementById('formCompra').action = '../backend/php/editar_compra.php';

            openModal('modalCompra');
        })
        .catch(error => {
            console.error(error);
            alert('Erro ao carregar a compra');
        });
}

function editarSindico(id) {

    fetch('../backend/php/obterSindico.php?id=' + id)
        .then(response => response.json())
        .then(data => {

            if (data.error) {
                alert(data.error);
                return;
            }

            document.getElementById('id_sindico').value = data.id_sindico;
            document.querySelector('input[name="nome-sindico"]').value = data.nome;
            document.querySelector('input[name="tel-sindico"]').value = data.telefone;

            document.getElementById('tituloModalSindico').textContent = 'Editar Sindico';
            document.getElementById('btnSalvarSindico').textContent = 'Atualizar';
            document.getElementById('formSindico').action = '../backend/php/editarSindico.php';

            openModal('modalSindico');
        })
        .catch(error => {
            console.error(error);
            alert('Erro ao carregar');
        });
}

function editarAdmin(id) {

    fetch('../backend/php/obterAdmin.php?id=' + id)
        .then(response => response.json())
        .then(data => {

            if (data.error) {
                alert(data.error);
                return;
            }

            document.getElementById('id_admin').value = data.id_admin;
            document.querySelector('input[name="nome-admin"]').value = data.nome;
            document.querySelector('input[name="tel-admin"]').value = data.telefone;
            document.querySelector('input[name="email-admin"]').value = data.email;

            document.getElementById('tituloModalAdmin').textContent = 'Editar Administrador';
            document.getElementById('btnSalvarAdmin').textContent = 'Atualizar';
            document.getElementById('formAdmin').action = '../backend/php/editarAdmin.php';

            openModal('modalAdm');
        })
        .catch(error => {
            console.error(error);
            alert('Erro ao carregar');
        });
}

function confirmarExclusao(id, tipo) {
    if (confirm('Tem certeza que deseja deletar?')) {
        window.location.href = '../backend/php/deletar_' + tipo + '.php?id=' + id;
    }
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Validação do formulário
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
    
    // Inicializar elementos de cliente
    const radioMorador = document.getElementById('tipo-morador');
    const radioCondominio = document.getElementById('tipo-condominio');
    const condInfo = document.querySelector('#condform');
    
    if (radioMorador && radioCondominio && condInfo) {
        function verificarTipoCliente() {
            if (radioCondominio && radioCondominio.checked) {
                condInfo.classList.add('active');
            } else {
                condInfo.classList.remove('active');
            }
        }
        
        radioMorador.addEventListener('change', verificarTipoCliente);
        radioCondominio.addEventListener('change', verificarTipoCliente);
        verificarTipoCliente();
    }

    // Forma de pagamento visibility
    const statusPagamento = document.getElementById('statusPag');
    const formaPagamento = document.querySelector('#forma_pagamento');
    
    function verificarStatusPagamento() {
    if (statusPagamento.value !== '1') {
            formaPagamento.classList.add('active');
    } else {
            formaPagamento.classList.remove('active');
        }
}
        verificarStatusPagamento();

        statusPagamento.addEventListener('change', verificarStatusPagamento);
    
    // Verificar se há mensagens de sucesso
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const msg = urlParams.get('msg');

    if (status && msg) {
        if (status === 'success') {
            alert(msg);
        } else if (status === 'error') {
            alert('Erro: ' + msg);
        }

    // Limpa a URL depois de mostrar a mensagem
    window.history.replaceState({}, document.title, window.location.pathname);
}

});


function filtrarMateriais(event) {
    event.preventDefault();
    event.stopPropagation();
    
    console.log('Filtrando materiais...');
    const mesSelecionado = document.getElementById('filtro').value;
    console.log('Mês selecionado:', mesSelecionado);
    
    fetch('../backend/php/lista_material.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'filtro-mes=' + encodeURIComponent(mesSelecionado)
    })
    .then(response => response.text())
    .then(data => {
        console.log('Dados recebidos, atualizando tabela');
        // Encontra a tabela correta dentro da seção #pecas
        const tbody = document.querySelector('#pecas table tbody');
        if (tbody) {
            tbody.innerHTML = data;
            console.log('Tabela atualizada com sucesso');
        } else {
            console.error('tbody não encontrado');
        }
    })
    .catch(error => {
        console.error('Erro ao filtrar materiais:', error);
        alert('Erro ao carregar os dados. Tente novamente.');
    });
    
    return false;
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Configurando filtro de materiais');
    
    // Procura pelo formulário correto
    const formFiltro = document.getElementById('formFiltro');
    const selectFiltro = document.getElementById('filtro');
    const btnFiltrar = document.getElementById('btnFiltrar');
    
    if (formFiltro) {
        formFiltro.addEventListener('submit', filtrarMateriais);
        console.log('Listener adicionado ao formulário');
    } else {
        console.warn('Formulário formFiltro não encontrado');
    }
    
    if (selectFiltro) {
        // Também filtra ao mudar o select (mais intuitivo)
        selectFiltro.addEventListener('change', filtrarMateriais);
        console.log('Listener de change adicionado ao select');
    }
    
    if (btnFiltrar) {
        btnFiltrar.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            filtrarMateriais(e);
            return false;
        });
        console.log('Listener adicionado ao botão');
    }
});

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
    
    // Recarregar com novo filtro
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
    
    // Fazer requisição ao servidor
    fetch('../backend/php/lista_servicos.php?filtro-periodo=' + encodeURIComponent(parametroFiltro))
        .then(response => response.text())
        .then(html => {
            // Atualizar o conteúdo da tabela
            const tbody = document.getElementById('servicosTable');
            if (tbody) {
                tbody.innerHTML = html;
            }
        })
        .catch(error => console.error('Erro ao filtrar serviços:', error));
}

// Adicionar listeners aos selects de filtro
document.addEventListener('DOMContentLoaded', function() {
    const selectPeriodo = document.getElementById('filtro-periodo');
    const selectAno = document.getElementById('filtro-ano');
    
    if (selectPeriodo) {
        selectPeriodo.addEventListener('change', filtrarServicos);
    }
    
    if (selectAno) {
        selectAno.addEventListener('change', filtrarServicos);
    }
    
    // Carregar com filtro padrão ao abrir a página
    filtrarServicos();
});

// Funções para modais
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

// Fechar modal ao clicar fora
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});

// Função para exibir páginas
function showPage(pageId, element) {
    // Ocultar todas as páginas
    const pages = document.querySelectorAll('.page');
    pages.forEach(page => page.classList.remove('active'));
    
    // Remover classe active de todos os menu-items
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => item.classList.remove('active'));
    
    // Mostrar página selecionada
    const page = document.getElementById(pageId);
    if (page) {
        page.classList.add('active');
    }
    
    // Adicionar classe active ao menu-item
    if (element) {
        element.classList.add('active');
    }
}
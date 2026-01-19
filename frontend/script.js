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

function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja deletar este serviço?')) {
        window.location.href = '../backend/php/deletar_servico.php?id=' + id;
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
    if (urlParams.get('status') === 'success') {
        const msg = urlParams.get('msg');
        if (msg) {
            alert(msg);
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }
});



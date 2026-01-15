function showPage(pageId, element) {
    const pages = document.querySelectorAll('.page');
    const menuItems = document.querySelectorAll('.menu-item');
    
    pages.forEach(page => page.classList.remove('active'));
    menuItems.forEach(item => item.classList.remove('active'));
    
    document.getElementById(pageId).classList.add('active');
        
    element.classList.add('active');
}

function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}

const radioMorador = document.getElementById('tipo-morador');
const radioCondominio = document.getElementById('tipo-condominio');
const condInfo = document.querySelector('#condform');

function verificarTipoCliente() {
    if (radioCondominio.checked) {
        condInfo.classList.add('active');
    } else {
        condInfo.classList.remove('active');
    }
}

radioMorador.addEventListener('change', verificarTipoCliente);
radioCondominio.addEventListener('change', verificarTipoCliente);

verificarTipoCliente();

function carregarFicha(id) {
    fetch('ficha_cliente.php?id=' + id)
        .then(r => r.text())
        .then(html => {
            document.getElementById('conteudo').innerHTML = html;
        });
}


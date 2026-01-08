function showPage(pageId) {
    const pages = document.querySelectorAll('.page');
    const menuItems = document.querySelectorAll('.menu-item');
    
    pages.forEach(page => page.classList.remove('active'));
    menuItems.forEach(item => item.classList.remove('active'));
    
    document.getElementById(pageId).classList.add('active');
    event.target.classList.add('active');
}

function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function viewClientDetails() {
    alert('Funcionalidade de visualização de ficha do cliente será implementada');
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Dados salvos com sucesso!');
        const modal = this.closest('.modal');
        if (modal) {
            modal.classList.remove('active');
        }
        this.reset();
    });
});
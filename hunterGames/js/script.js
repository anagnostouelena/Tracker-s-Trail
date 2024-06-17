class Modal {

    popModal(id) {
        const inputFields = document.querySelectorAll('input');

    
        inputFields.forEach(input => {
            input.value = '';
        });
        const modalDiv = document.getElementById(id);
        modalDiv.classList.remove('fade-out');
        modalDiv.classList.add('fade-in');
        modalDiv.style.display = 'flex';
    }
    closeModal(id) {
        const modalDiv = document.getElementById(id);
        modalDiv.classList.remove('fade-in');
        modalDiv.classList.add('fade-out');


        setTimeout(function () {
            modalDiv.style.display = 'none';
            modalDiv.classList.remove('fade-out');
        }, 100);
    }
}


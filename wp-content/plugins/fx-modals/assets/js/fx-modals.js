setTimeout(() => {
    const modalButtons = document.querySelectorAll('.fx-modal-open-button');
    for (const modalButton of modalButtons){
        modalButton.addEventListener('click', function(e) {
            const id = this.getAttribute('id');
            const modal = document.getElementById('fx-modal-' + id);
            const overlay = modal.parentElement;
            modal.remove();
            overlay.remove();
            document.body.insertBefore(overlay, document.body.firstChild);
            overlay.appendChild(modal);
            modal.style.display = 'block';
            overlay.style.display = 'flex';
            
            //request a quote button only
            if(modalButton.id == '19864'){
                if (e.target.getAttribute('data-product-title')) {
                    const machineText = e.target.getAttribute('data-product-title');
                    const formHeader = modal.querySelector('span');
                    const formHeaderValue =  formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('h2').innerHTML = formHeaderValue;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else if(e.target.parentElement && e.target.parentElement.parentElement && e.target.parentElement.parentElement.parentElement && e.target.parentElement.parentElement.parentElement.classList.contains('product-card-detail-info')){
                    const container = e.target.parentElement.parentElement.parentElement;
                    const machineText = container.querySelector('.product-card__title').textContent;
                    const formHeader = modal.querySelector('span');
                    const formHeaderValue =  formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('h2').innerHTML = formHeaderValue;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else if(e.target.parentElement && e.target.parentElement.parentElement && e.target.parentElement.parentElement.classList.contains('product-card-detail-info')){ // products page
                    const container = e.target.parentElement.parentElement;
                    const machineText = container.querySelector('.product-card__title').textContent;
                    const formHeader = modal.querySelector('span');
                    const formHeaderValue =  formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('h2').innerHTML = formHeaderValue;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else if(document.querySelector('.product__overview h1.flush')){
                    const machineText = document.querySelector('.product__overview h1.flush').textContent;
                    const formHeader = modal.querySelector('span');
                    const formHeaderValue =  formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('h2').innerHTML = formHeaderValue;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else {
                    modal.querySelector('[name=yourmachine]').value = '';
                }
            
            }

            //request a quote button only
            if(modalButton.id == '20325'){
                if (e.target.getAttribute('data-product-title')) {
                    const machineText = e.target.getAttribute('data-product-title');
                    const formHeader = modal.querySelector('span');
                    const formHeaderValue =  formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('h2').innerHTML = formHeaderValue;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else if(e.target.parentElement && e.target.parentElement.parentElement && e.target.parentElement.parentElement.parentElement && e.target.parentElement.parentElement.parentElement.classList.contains('product-card-detail-info')){
                    const container = e.target.parentElement.parentElement.parentElement;
                    const machineText = container.querySelector('.product-card__title').textContent;
                    const formHeader = modal.querySelector('span');
                    const formHeaderValue =  formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('h2').innerHTML = formHeaderValue;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else if(e.target.parentElement && e.target.parentElement.parentElement && e.target.parentElement.parentElement.classList.contains('product-card-detail-info')){ // products page
                    const container = e.target.parentElement.parentElement;
                    const machineText = container.querySelector('.product-card__title').textContent;
                    const formHeader = modal.querySelector('span');
                    const formHeaderValue =  formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('h2').innerHTML = formHeaderValue;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else if(document.querySelector('.product__overview h1.flush')){
                    const machineText = document.querySelector('.product__overview h1.flush').textContent;
                    const formHeader = modal.querySelector('span');
                    const formHeaderValue =  formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('h2').innerHTML = formHeaderValue;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else {
                    modal.querySelector('[name=yourmachine]').value = '';
                }
            
            }
            
            //get a free quote button only
            if(modalButton.id == '19786'){
                if (e.target.getAttribute('data-product-title')) {
                    const machineText = e.target.getAttribute('data-product-title');
                    const formHeader = modal.querySelector('span');
                    const formHeaderValue =  formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('h2').innerHTML = formHeaderValue;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else if(e.target.parentElement && e.target.parentElement.parentElement && e.target.parentElement.parentElement.parentElement && e.target.parentElement.parentElement.parentElement.classList.contains('intro-text-info')){
                    const container = e.target.parentElement;
                    const machineText = container.querySelector('h2').textContent;
                    const formHeader = modal.querySelector('span');
                    modal.querySelector('h2').innerHTML = formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else if(e.target.parentElement && e.target.parentElement.parentElement && e.target.parentElement.parentElement.classList.contains('intro-text-info')){
                    const container = e.target.parentElement;
                    const machineText = container.querySelector('h2').textContent;
                    const formHeader = modal.querySelector('span');
                    modal.querySelector('h2').innerHTML = formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else if(e.target.parentElement && e.target.parentElement.classList.contains('intro-text-info')){
                    const container = e.target.parentElement;
                    const machineText = container.querySelector('h2').textContent;
                    const formHeader = modal.querySelector('span');
                    modal.querySelector('h2').innerHTML = formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else if(document.querySelector('.product__overview h1.flush')){
                    //const machineText = document.querySelector('.product__overview h1.flush').textContent;
                    const machineText = container.querySelector('h2').textContent;
                    const formHeader = modal.querySelector('h2');
                    modal.querySelector('h2').innerHTML = formHeader.textContent + ' - ' + machineText;
                    modal.querySelector('[name=yourmachine]').value = machineText;
                } else {
                    modal.querySelector('[name=yourmachine]').value = '';
                }
            }
        });
    }
}, 1000);

document.addEventListener('click', function(e){
    if(!e.target.closest(".fx-modal-open-button") && (!e.target.closest(".fx-modal-container") || e.target.matches(".fx-close-modal-button"))) {
        const modals = document.querySelectorAll('.fx-modal-container');
        modals.forEach(modal => {
            modal.style.display = "none";
        });
        const overlays = document.querySelectorAll('.fx-modal-overlay');
        overlays.forEach(overlay => {
            overlay.style.display = "none";
        });
		
		var modal_event = document.createEvent("Event");
            modal_event.initEvent("modal_close", false, true); 
            document.dispatchEvent(modal_event);
    }    
})
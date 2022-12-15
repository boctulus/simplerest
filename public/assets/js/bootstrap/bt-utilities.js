
/*
    https://stackoverflow.com/a/66545752/980631
*/
function hideModal(id) {
    const modal_el  = document.querySelector('#'+id);
    const modal_obj = bootstrap.Modal.getInstance(modal_el);

    if (modal_obj ==  null){
        return;
    }

    modal_obj.hide();
}

function showModal(id) {
    const modal_el  = document.querySelector('#'+id);
    let   modal_obj = bootstrap.Modal.getInstance(modal_el);

    if (modal_obj ==  null){
        modal_obj = new bootstrap.Modal(modal_el, {
            backdrop: 'static'
        });
    }

    modal_obj.show();
}

const hide_elem_by_id = id => {
    $(`#${id}`).hide();
}

const show_elem_by_id = id => {
    $(`#${id}`).show();
}



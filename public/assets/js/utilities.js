/*
    @author Pablo Bozzolo
*/

if (typeof $ == 'undefined' && typeof jQuery != 'undefined'){
    $=jQuery
}

/*
    onReady(event => {
        // Código que se ejecutará cuando el evento DOMContentLoaded ocurra
    });
*/
const onReady = (callback) => {
    document.addEventListener("DOMContentLoaded", callback);
}
  
/*
    https://stackoverflow.com/questions/27746304/how-to-check-if-an-object-is-a-promise/27746324#27746324
*/

const isPromise = p => {
    return p && Object.prototype.toString.call(p) === "[object Promise]";
}

/*
    Antes llamada decodeProp

    Trabaja con var_encode() de PHP
*/
const var_decode = (id) => {
    const el = document.getElementById(id + '-encoded');

    if (el == null){
        throw `Propery ${id} not found`
    }

    const val = el.value;

    if (val == null){
        throw `Value of ${id} is empty?`
    }

    const bin = atob(val);

    if (bin.startsWith('--array--')){
        return JSON.parse(bin.substring(9));
    }

    return bin;
}


const setNotification = (msg) => {
    if (Array.isArray(msg)){    
        let block_elems = [];

        msg.forEach((el) => {
            block_elems.push(`<li>${el}</li>`)
        })

        msg = '<ul style="list-style: none; margin: 0; padding: 0;">' + block_elems.join("\r\n") + '</ul>'
    }

    $('#modal_notifications').html(msg)
}

const clearNotifications = () => {
    $('#modal_notifications').html()
}



/*
    @author Pablo Bozzolo
*/

/*
    Si merge es true, se hace un "shallow merge" de propiedades
*/
const toStorage = (data, storage = 'local', merge = true) => {
    switch (storage){
        case 'session':
            st_obj = sessionStorage
            break;
        case 'local':
            st_obj = localStorage
            break;
        default:
            throw "Invalid Storage"        
    }

    if (merge){
        let prev_data = JSON.parse(st_obj.getItem('srsw'))
            data      = { ...prev_data, ...data }
    }

    st_obj.setItem('srsw', JSON.stringify(data)) 
    
    // dado es util en algunos casos,
    // recuperar el objeto cuando fue mergeado
    return data;
}

const fromStorage = (storage = 'local') => {    
    switch (storage){
        case 'session':
            st_obj = sessionStorage
            break;
        case 'local':
            st_obj = localStorage
            break;
        default:
            throw "Invalid Storage"        
    }

    return JSON.parse(st_obj.getItem('srsw'));    
}

const clearStorage = (storage) => {
    switch (storage){
        case 'session':
            st_obj = sessionStorage
            break;
        case 'local':
            st_obj = localStorage
            break;
        default:
            throw "Invalid Storage"        
    }

    st_obj.clear()
}
/*
    GeoLocation - diferentes proveedores
*/

if (typeof $ == 'undefined' && typeof jQuery != 'undefined'){
    $=jQuery
}

/*
    get_geolocation().then((ubicacion) => {
      console.log(ubicacion);
    });
/*/

async function get_geolocation() {
    const url = 'https://api.ipregistry.co/?key=tryout'

    return await fetch(url)
    .then(response => {
        return response.json()
    })
    .then(payload => {
        return { 
            'country': payload.location.country.name, 
            'city': payload.location.city.name
        }
    })
    .catch(error => {
        console.log('error', error)
        Promise.reject(error);
    });
}

/*
    Solo para testing porque no tiene HTTPS con lo cual genera "mixed-content"

    get_geolocation_2().then((ubicacion) => {
      console.log(ubicacion);
    });
/*/

async function get_geolocation_2() {
    const url = "http://ip-api.com/json"

    return await fetch(url)
    .then(response => {
        return response.json()
    })
    .then(payload => {
        return { 
            'country': payload.country
        }
    })
    .catch(error => {
        console.log('error', error)
        Promise.reject(error);
    });
}

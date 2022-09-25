const login_page = base_url + '/login';


function password_show_hide(id = 'password') {
    let icon_s = document.getElementById('show_eye');
    let icon_h = document.getElementById('hide_eye');
    let input = document.getElementById(id);
    let input2 = input.cloneNode(false);

    if (input.type == 'password') {
        input2.type = 'text';
        icon_s.classList.add('d-none');
        icon_h.classList.remove('d-none');
    } else {
        input2.type = 'password';
        icon_h.classList.add('d-none');
        icon_s.classList.remove('d-none');
    }

    input.parentNode.replaceChild(input2, input);
}

/*
    Access token expiration

    Estoy olvidando que el refresh token tambien tiene expiracion y ni siquiera la estoy guardando
    en localStorage !
*/
function acccess_expired(){
    return ((localStorage.getItem('exp') != null) && ((localStorage.getItem('exp') * 1000) - (new Date()).getTime()) < 0);
}

function logged(){
    return localStorage.getItem('access_token') != null && !acccess_expired();
}

function logout(redirect = true) {
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');

    if (redirect){
        window.location.href = login_page;
    }
}

function keep_alive(){
    if (typeof localStorage === 'undefined') {
        throw "No localStorage";
    }

    // Sino hay un access token, tampoco habra fresh token en localStorage
    if (localStorage.getItem('access_token') == null) {
        return false;
    }

    if (!acccess_expired()){
        return true;
    }
    
    if (localStorage.getItem('refresh_token')) {
        //console.log("Renovando access token,...");
        return renew();
    } else {
        return false;
    }
}

function checkpoint() {
    if (!keep_alive()){
        if (window.location != base_url && !window.location.toString().startsWith(login_page)){
            window.location = login_page;
        }
    }
}

function register() {
    var data = {};

    if ($('#password').val() != $('#password_confirmation').val()) {
        $('#registerError').text('Contraseñas no coinciden');
        console.log('Contraseñas no coinciden');
        return;
    } else {
        $('#registerError').text('');
    }

    data[$__username] = $('#username').val();
    data[$__email]    = $('#email').val();
    data[$__password] = $('#password').val();

    $.ajax({
        type: "POST",
        url: base_url + "/api/v1/auth/register",
        data,
        dataType: 'json',
        success: function(res) {
            let data = res.data;

            if (typeof data.access_token != 'undefined') {
                console.log('Token recibido');
                localStorage.setItem('access_token', data.access_token);
                localStorage.setItem('refresh_token', data.refresh_token);
                localStorage.setItem('expires_in', data.expires_in);
                localStorage.setItem('exp', parseInt((new Date).getTime() / 1000) + data.expires_in);
                console.log('Tokens obtenidos', data);
                window.location = base_url;
            } else {
                $('#registerError').text('Error desconcido');
                console.log(data);
            }
        },
        error: function(xhr, status, error) {
            console.log(JSON.parse(xhr.responseText));
            $('#registerError').text(JSON.parse(xhr.responseText).error);
        }
    });

    return false;
}

function login() {
    var data = {};

    if ($('#email_username').val().match(/@/) != null)
        data[$__email] = $('#email_username').val();
    else
        data[$__username] = $('#email_username').val();

    data[$__password] = $('#password').val();

    $.ajax({
        type: "POST",
        url: base_url + '/api/v1/auth/login',
        data,
        dataType: 'json',
        success: function(res) {

            var data = res.data;

            if (typeof data.access_token != 'undefined' && typeof data.refresh_token != 'undefined') {
                localStorage.setItem('access_token', data.access_token);
                localStorage.setItem('refresh_token', data.refresh_token);
                localStorage.setItem('expires_in', data.expires_in);
                localStorage.setItem('exp', parseInt((new Date).getTime() / 1000) + data.expires_in);
                console.log('Tokens obtenidos');
                window.location = base_url;
            } else {
                console.log('Error (success)', data);
                $('#loginError').text(data.responseJSON.error);
            }
        },
        error: function(xhr) {
            console.log('Error (error)', xhr);
            $('#loginError').text('Error de autenticación!!!');
        }
    });

    return false;
}

function renew() {
    console.log('Renewing token at ...' + (new Date()).toString());

    $.ajax({
        type: "POST",
        url: base_url + '/api/v1/auth/token',
        dataType: 'json',
        headers: { "Authorization": 'Bearer ' + localStorage.getItem('refresh_token') },
        success: function(res) {
            var data = res.data;

            if (typeof data.access_token != 'undefined') {
                localStorage.setItem('access_token', data.access_token);
                localStorage.setItem('expires_in', data.expires_in);
                localStorage.setItem('exp', parseInt((new Date).getTime() / 1000) + data.expires_in);

                return true;
            } else {
                console.log('Error en la renovación del token');
                
                return false;
            }
        },
        error: function(data) {
            console.log('Error en la renovación del token!!!!!!!!!!!!');
            console.log(data);
            
            return false;
        }
    });
}

function rememberme() {
    let data = {};

    data.email = $('#email').val();

    $('#remembermeError').text('');

    $.ajax({
        type: "POST",
        url: base_url + '/api/v1/auth/rememberme',
        data,
        dataType: 'text',
        success: function(res) {
            window.location.replace(base_url + '/login/rememberme_mail_sent/' + window.btoa(data.email));
        },
        error: function(xhr, status, error) {
            console.log('ERROR');
            console.log(xhr.responseJSON);

            if (xhr.responseJSON && xhr.responseJSON.error)
                $('#remembermeError').text(xhr.responseJSON.error);
            else {
                $('#remembermeError').text('Error - intente más tarde');
                console.log(xhr);
                console.log(status);
                console.log(error);
            }

        }
    });

    return false;
}


function update_pass() {
    if ($('#password').val() != $('#password_confirmation').val()) {
        $('#passChangeError').text('Contraseñas no coinciden');
        return;
    } else
        $('#passChangeError').text('');

    let data = {};

    data['password'] = $('#password').val();

    const slugs = window.location.pathname.split('/');
    const token = slugs[slugs.indexOf('change_pass_by_link') + 1];

    if (typeof token === 'undefined') {
        $('#passChangeError').text('No autorizado');
    }

    $.ajax({
        type: "POST",
        url: base_url + "/api/v1/auth/change_pass_process",
        headers: { "Authorization": 'Bearer ' + token },
        data,
        dataType: 'json',
        success: function(res) {
            let data = res.data;

            if (data && data.access_token) {
                console.log('Token recibido');
                localStorage.setItem('access_token', data.access_token);
                localStorage.setItem('refresh_token', data.refresh_token);
                localStorage.setItem('expires_in', data.expires_in);
                localStorage.setItem('exp', parseInt((new Date).getTime() / 1000) + data.expires_in);
                console.log('Tokens obtenidos', data);
                window.location = base_url;
            } else {
                $('#passChangeError').text('Error desconcido');
                console.log(res);
            }
        },
        error: function(xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
            $('#passChangeError').text(JSON.parse(xhr.responseText).error);
        }
    });

    return false;
}


checkpoint();
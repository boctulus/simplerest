'use strict';

/* ---------------------------------------------------------------
   JWT helpers — no library needed, just base64 decode the payload
--------------------------------------------------------------- */
function _jwtPayload() {
    var tok = localStorage.getItem('access_token');
    if (!tok) return null;
    try {
        var parts = tok.split('.');
        if (parts.length !== 3) return null;
        var b64 = parts[1].replace(/-/g, '+').replace(/_/g, '/');
        return JSON.parse(atob(b64));
    } catch (e) { return null; }
}

function username() {
    var p = _jwtPayload();
    if (!p) return '';
    return p.email || p.username || ('#' + p.uid) || '';
}

function isLoggedIn() {
    var p = _jwtPayload();
    if (!p) return false;
    return p.exp && p.exp > Math.floor(Date.now() / 1000);
}

/* ---------------------------------------------------------------
   login() — called by the login form button
--------------------------------------------------------------- */
function login() {
    var email    = (document.getElementById('email_username') || {}).value || '';
    var password = (document.getElementById('password')       || {}).value || '';
    var errEl    = document.getElementById('loginError');

    if (!email || !password) {
        if (errEl) errEl.textContent = 'Email/usuario y contraseña requeridos.';
        return;
    }

    var body = {};
    body[$__email]    = email;
    body[$__password] = password;

    fetch(base_url + '/api/v1/auth/login', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(body)
    })
    .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, status: r.status, data: d }; }); })
    .then(function(res) {
        if (!res.ok) {
            if (errEl) errEl.textContent = res.data.error || ('Error ' + res.status);
            return;
        }
        var d = res.data;
        localStorage.setItem('access_token',  d.access_token  || '');
        localStorage.setItem('refresh_token', d.refresh_token || '');
        localStorage.setItem('expires_in',    d.expires_in    || '');
        localStorage.setItem('exp', Math.floor(Date.now() / 1000) + (d.expires_in || 3600));
        window.location.replace(base_url + '/admin/acl-permissions');
    })
    .catch(function(e) {
        if (errEl) errEl.textContent = 'Error de red: ' + e.message;
    });
}

/* ---------------------------------------------------------------
   logout()
--------------------------------------------------------------- */
function logout() {
    ['access_token', 'refresh_token', 'expires_in', 'exp'].forEach(function(k) {
        localStorage.removeItem(k);
    });
    window.location.replace(base_url + '/auth/login');
}

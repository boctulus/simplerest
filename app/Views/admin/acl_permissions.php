<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-shield-alt mr-2"></i> User Permissions Manager</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userSearch">Select User</label>
                                <div class="position-relative">
                                    <input type="text" id="userSearch" class="form-control" placeholder="Type name or email..." autocomplete="off">
                                    <div id="userDropdown" class="dropdown-menu w-100" style="max-height:250px;overflow-y:auto;"></div>
                                    <input type="hidden" id="selectedUserId" value="">
                                </div>
                                <div id="selectedUserBadge" class="mt-2 d-none">
                                    <span class="badge bg-primary fs-6" id="selectedUserLabel"></span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-1" id="btnClearUser">&times;</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button id="btnRefreshUser" class="btn btn-outline-secondary" disabled>
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="userInfoSection" class="row mb-4 d-none">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user mr-2"></i> User Info</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4"><strong>ID:</strong> <span id="uidDisplay">-</span></div>
                        <div class="col-md-4"><strong>Name:</strong> <span id="unameDisplay">-</span></div>
                        <div class="col-md-4"><strong>Email:</strong> <span id="uemailDisplay">-</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="rolesSection" class="row mb-4 d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tags mr-2"></i> Roles</h3>
                </div>
                <div class="card-body">
                    <div id="rolesContainer" class="d-flex flex-wrap gap-2"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="spSection" class="row mb-4 d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-star mr-2"></i> Special Permissions</h3>
                    <div class="card-tools">
                        <button id="btnAddSpPerm" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addSpModal">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="spPermissionsContainer" class="row"></div>
                    <div id="spEmptyState" class="text-muted text-center py-3">No special permissions assigned.</div>
                </div>
            </div>
        </div>
    </div>

    <div id="tbSection" class="row mb-4 d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-table mr-2"></i> Table (Resource) Permissions</h3>
                    <div class="card-tools">
                        <button id="btnAddTbPerm" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addTbModal">
                            <i class="fas fa-plus"></i> Add Table
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="tbPermissionsContainer"></div>
                    <div id="tbEmptyState" class="text-muted text-center py-3">No table permissions assigned.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addSpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Special Permission</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Special Permission</label>
                    <select id="newSpPermSelect" class="form-control">
                        <option value="">-- Select --</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button id="btnSaveSpPerm" type="button" class="btn btn-primary" disabled>Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addTbModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Table Permission</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Table / Resource Name</label>
                    <input type="text" id="newTbName" class="form-control" placeholder="e.g. products, users">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button id="btnSaveTbPerm" type="button" class="btn btn-primary" disabled>Save</button>
            </div>
        </div>
    </div>
</div>

<style>
#rolesContainer .badge-role {
    font-size: 0.9rem;
    padding: 0.4rem 0.8rem;
    margin: 0.15rem;
    background: #e8eaf6;
    color: #283593;
    border-radius: 4px;
    display: inline-block;
}
.permission-checkbox-grid label {
    font-size: 0.85rem;
    cursor: pointer;
}
.permission-checkbox-grid .form-check {
    margin-bottom: 0.25rem;
}
.tb-permission-card {
    border-left: 4px solid #17a2b8;
    margin-bottom: 1rem;
}
.sp-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #fff3e0;
    color: #e65100;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    margin: 0.2rem;
}
.sp-badge .btn-del-sp {
    background: none;
    border: none;
    color: #bf360c;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    font-size: 1rem;
}
.sp-badge .btn-del-sp:hover {
    color: #870000;
}
#userDropdown {
    position: absolute;
    z-index: 1050;
    display: none;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    background: #fff;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
}
#userDropdown.show {
    display: block;
}
#userDropdown .dropdown-item {
    cursor: pointer;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 0.4rem 0.75rem;
    font-size: 0.9rem;
    color: #212529;
    display: block;
}
#userDropdown .dropdown-item:hover,
#userDropdown .dropdown-item.active {
    background: #e9ecef;
}
#userDropdown .dropdown-item strong {
    color: #0d6efd;
}
#userDropdown .dropdown-item small {
    color: #6c757d;
}
.dropdown-empty,
.dropdown-loading {
    padding: 0.5rem 0.75rem;
    color: #6c757d;
    font-size: 0.85rem;
    text-align: center;
}
</style>

<script>
function username(){
    const token = localStorage.getItem('access_token');
    if (!token) return '';
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        return payload.username || payload.name || payload.email || payload.sub || '';
    } catch(e){
        return '';
    }
}

function logout(){
    localStorage.removeItem('access_token');
    localStorage.removeItem('exp');
    window.location.href = '/';
}

let selectedUserId = null;
let selectedUserName = '';
let currentUserRoles = [];
let currentSpPerms = [];
let spPermCatalog = [];
let availableSpPerms = [];
let currentTbPerms = [];
let searchTimeout = null;

$(document).ready(function(){
    initEventHandlers();
});

function initEventHandlers(){
    $('#userSearch').on('input', function(){
        const q = $(this).val().trim();
        if (q.length < 2){
            $('#userDropdown').removeClass('show');
            return;
        }
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function(){ searchUsers(q); }, 250);
    });

    $('#userSearch').on('keydown', function(e){
        const $items = $('#userDropdown .dropdown-item');
        const $active = $('#userDropdown .dropdown-item.active');
        let idx = $items.index($active);

        if (e.key === 'ArrowDown'){
            e.preventDefault();
            idx = Math.min(idx + 1, $items.length - 1);
        } else if (e.key === 'ArrowUp'){
            e.preventDefault();
            idx = Math.max(idx - 1, -1);
        } else if (e.key === 'Enter'){
            e.preventDefault();
            if ($active.length) $active.click();
            return;
        } else if (e.key === 'Escape'){
            $('#userDropdown').removeClass('show');
            return;
        } else { return; }

        $items.removeClass('active');
        if (idx >= 0) $items.eq(idx).addClass('active');
    });

    $(document).on('click', function(e){
        if (!$(e.target).closest('#userSearch, #userDropdown').length){
            $('#userDropdown').removeClass('show');
        }
    });

    $('#btnClearUser').on('click', function(){
        clearSelectedUser();
    });

    $('#btnRefreshUser').on('click', function(){
        if (selectedUserId) loadUserData(selectedUserId);
    });

    $('#newSpPermSelect').on('change', function(){
        $('#btnSaveSpPerm').prop('disabled', !$(this).val());
    });

    $('#btnSaveSpPerm').on('click', function(){
        const permId = parseInt($('#newSpPermSelect').val());
        if (!permId || !selectedUserId) return;
        addSpPermission(selectedUserId, permId);
    });

    $('#newTbName').on('input', function(){
        $('#btnSaveTbPerm').prop('disabled', !$(this).val().trim());
    });

    $('#btnSaveTbPerm').on('click', function(){
        const tb = $('#newTbName').val().trim();
        if (!tb || !selectedUserId) return;
        addTbPermission(selectedUserId, tb);
    });
}

function searchUsers(q){
    $.ajax({
        url: base_url + 'api/v1/users',
        headers: apiHeaders(),
        data: { q: q, limit: 20 },
        dataType: 'json',
        beforeSend: function(){
            $('#userDropdown').html('<div class="dropdown-loading"><i class="fas fa-spinner fa-spin"></i> Searching...</div>').addClass('show');
        }
    }).done(function(data){
        const items = Array.isArray(data) ? data : (data.data || []);
        if (!items.length){
            $('#userDropdown').html('<div class="dropdown-empty">No users found</div>').addClass('show');
            return;
        }
        let html = '';
        items.forEach(function(u){
            const name = u.name || u.nombre || u.username || '';
            const email = u.email || '';
            const display = name ? escapeHtml(name) : ('#' + u.id);
            const sub = email ? '<small>' + escapeHtml(email) + '</small>' : '<small>#' + u.id + '</small>';
            html += '<div class="dropdown-item" data-id="' + u.id + '" data-name="' + escapeHtml(name || email) + '">' + display + ' ' + sub + '</div>';
        });
        $('#userDropdown').html(html).addClass('show');
    }).fail(function(xhr){
        handleApiError(xhr);
    });
}

$(document).on('click', '#userDropdown .dropdown-item', function(){
    const id = parseInt($(this).data('id'));
    const name = $(this).data('name');
    selectUser(id, name);
});

function selectUser(id, name){
    selectedUserId = id;
    selectedUserName = name;
    $('#selectedUserId').val(id);
    $('#userSearch').val('').hide();
    $('#selectedUserLabel').text(name + ' (#' + id + ')');
    $('#selectedUserBadge').removeClass('d-none');
    $('#userDropdown').removeClass('show');
    $('#btnRefreshUser').prop('disabled', false);
    $('#btnRefreshUser').click();
}

function clearSelectedUser(){
    selectedUserId = null;
    selectedUserName = '';
    $('#selectedUserId').val('');
    $('#userSearch').val('').show().focus();
    $('#selectedUserBadge').addClass('d-none');
    $('#btnRefreshUser').prop('disabled', true);
    hideAllSections();
}

function hideAllSections(){
    $('#userInfoSection, #rolesSection, #spSection, #tbSection').addClass('d-none');
}

function showLoading(btn){
    $(btn).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
}

function hideLoading(btn, html){
    $(btn).prop('disabled', false).html(html);
}

function getToken(){
    return localStorage.getItem('access_token');
}

function apiHeaders(){
    return {
        'Authorization': 'Bearer ' + getToken(),
        'Content-Type': 'application/json'
    };
}

function handleApiError(xhr){
    if (xhr.status === 401 || xhr.status === 403){
        alert('Session expired or unauthorized. Redirecting to login.');
        window.location.href = '/';
        return;
    }
    const msg = xhr.responseJSON?.error || xhr.statusText || 'Request failed';
    alert('Error: ' + msg);
}

function loadUserData(userId){
    showLoading('#btnRefreshUser');
    hideAllSections();

    const promises = [
        fetchUserInfo(userId),
        fetchUserRoles(userId),
        fetchUserSpPerms(userId),
        fetchUserTbPerms(userId),
        fetchSpPermCatalog()
    ];

    Promise.allSettled(promises).then(function(results){
        hideLoading('#btnRefreshUser', '<i class="fas fa-sync-alt"></i> Load');

        if (results[0].status === 'fulfilled'){
            displayUserInfo(results[0].value);
        }
        if (results[1].status === 'fulfilled'){
            displayRoles(results[1].value);
        }
        if (results[2].status === 'fulfilled' && results[4].status === 'fulfilled'){
            spPermCatalog = results[4].value;
            displaySpPerms(results[2].value);
        }
        if (results[3].status === 'fulfilled'){
            displayTbPerms(results[3].value);
        }
    });
}

function fetchUserInfo(userId){
    return new Promise(function(resolve, reject){
        $.ajax({
            url: base_url + 'api/v1/users/' + userId,
            headers: apiHeaders(),
            success: function(data){
                resolve(data.data || data);
            },
            error: function(xhr){ handleApiError(xhr); reject(xhr); }
        });
    });
}

function fetchUserRoles(userId){
    return new Promise(function(resolve, reject){
        $.ajax({
            url: base_url + 'api/v1/user_roles?user_id=' + userId + '&expand=roles',
            headers: apiHeaders(),
            success: function(data){
                resolve(Array.isArray(data) ? data : (data.data || []));
            },
            error: function(xhr){ handleApiError(xhr); reject(xhr); }
        });
    });
}

function fetchUserSpPerms(userId){
    return new Promise(function(resolve, reject){
        $.ajax({
            url: base_url + 'api/v1/user_sp_permissions?user_id=' + userId + '&expand=sp_permissions',
            headers: apiHeaders(),
            success: function(data){
                resolve(Array.isArray(data) ? data : (data.data || []));
            },
            error: function(xhr){ handleApiError(xhr); reject(xhr); }
        });
    });
}

function fetchUserTbPerms(userId){
    return new Promise(function(resolve, reject){
        $.ajax({
            url: base_url + 'api/v1/user_tb_permissions?user_id=' + userId,
            headers: apiHeaders(),
            success: function(data){
                resolve(Array.isArray(data) ? data : (data.data || []));
            },
            error: function(xhr){ handleApiError(xhr); reject(xhr); }
        });
    });
}

function fetchSpPermCatalog(){
    return new Promise(function(resolve, reject){
        $.ajax({
            url: base_url + 'api/v1/sp_permissions',
            headers: apiHeaders(),
            success: function(data){
                resolve(Array.isArray(data) ? data : (data.data || []));
            },
            error: function(xhr){ handleApiError(xhr); reject(xhr); }
        });
    });
}

function displayUserInfo(user){
    const name = user.name || user.nombre || user.email || '-';
    $('#uidDisplay').text(user.id);
    $('#unameDisplay').text(name);
    $('#uemailDisplay').text(user.email || '-');
    $('#userInfoSection').removeClass('d-none');
}

function displayRoles(roles){
    const $container = $('#rolesContainer');
    $container.empty();

    currentUserRoles = roles;

    if (!roles.length){
        $container.html('<span class="text-muted">No roles assigned.</span>');
    } else {
        roles.forEach(function(r){
            const roleName = r.role_name || r.name || r.role_id || 'Unknown';
            $container.append('<span class="badge-role">' + escapeHtml(roleName) + '</span>');
        });
    }

    $('#rolesSection').removeClass('d-none');
}

function displaySpPerms(userSpPerms){
    const $container = $('#spPermissionsContainer');
    $container.empty();

    currentSpPerms = userSpPerms;

    const userPermIds = userSpPerms.map(function(p){
        return p.sp_permission_id;
    });

    availableSpPerms = spPermCatalog.filter(function(sp){
        return !userPermIds.includes(sp.id);
    });

    if (!userSpPerms.length){
        $('#spEmptyState').show();
    } else {
        $('#spEmptyState').hide();
        userSpPerms.forEach(function(p){
            const permName = p.sp_permission_name || p.name || 'id:' + p.sp_permission_id;
            const spId = p.id;
            const badgeId = 'sp-' + spId;
            $container.append(
                '<div class="col-auto mb-2">' +
                '   <span class="sp-badge" id="' + badgeId + '">' +
                        escapeHtml(permName) +
                '       <button class="btn-del-sp" data-sp-id="' + spId + '" data-user-id="' + selectedUserId + '" title="Remove">&times;</button>' +
                '   </span>' +
                '</div>'
            );
        });
    }

    populateSpPermSelect();
    $('#spSection').removeClass('d-none');
}

function displayTbPerms(tbPerms){
    const $container = $('#tbPermissionsContainer');
    $container.empty();

    currentTbPerms = tbPerms;

    if (!tbPerms.length){
        $('#tbEmptyState').show();
    } else {
        $('#tbEmptyState').hide();
        tbPerms.forEach(function(perm){
            const tb = perm.tb;
            const permId = perm.id;
            const cardId = 'tb-' + permId;

            let html = '<div class="card tb-permission-card" id="' + cardId + '">' +
                '<div class="card-body">' +
                '   <div class="d-flex justify-content-between align-items-start mb-2">' +
                '       <h6 class="mb-0 font-weight-bold"><code>' + escapeHtml(tb) + '</code></h6>' +
                '       <button class="btn btn-sm btn-outline-danger btn-del-tb" data-tb-id="' + permId + '" data-user-id="' + selectedUserId + '" title="Remove all permissions for this table">&times;</button>' +
                '   </div>' +
                '   <div class="row permission-checkbox-grid">';

            const perms = [
                { key: 'can_list_all', label: 'list_all' },
                { key: 'can_show_all', label: 'show_all' },
                { key: 'can_list', label: 'list' },
                { key: 'can_show', label: 'show' },
                { key: 'can_create', label: 'create' },
                { key: 'can_update', label: 'update' },
                { key: 'can_delete', label: 'delete' }
            ];

            perms.forEach(function(p){
                const checked = perm[p.key] ? 'checked' : '';
                html +=
                    '   <div class="col-md-3 col-sm-4 col-6">' +
                    '       <div class="form-check">' +
                    '           <input class="form-check-input tb-perm-checkbox" type="checkbox" ' +
                                    'data-tb-id="' + permId + '" data-user-id="' + selectedUserId + '" ' +
                                    'data-field="' + p.key + '" data-table="' + escapeHtml(tb) + '" ' +
                                    'id="chk-' + permId + '-' + p.key + '" ' + checked + '>' +
                    '           <label class="form-check-label" for="chk-' + permId + '-' + p.key + '">' + p.label + '</label>' +
                    '       </div>' +
                    '   </div>';
            });

            html += '   </div></div></div>';
            $container.append(html);
        });
    }

    $('#tbSection').removeClass('d-none');
}

function populateSpPermSelect(){
    const $select = $('#newSpPermSelect');
    $select.empty().append('<option value="">-- Select --</option>');
    availableSpPerms.forEach(function(sp){
        $select.append('<option value="' + sp.id + '">' + escapeHtml(sp.name) + '</option>');
    });
    $('#btnSaveSpPerm').prop('disabled', true);
}

function addSpPermission(userId, spPermId){
    $.ajax({
        url: base_url + 'api/v1/user_sp_permissions',
        method: 'POST',
        headers: apiHeaders(),
        data: JSON.stringify({
            user_id: userId,
            sp_permission_id: spPermId
        }),
        success: function(){
            $('#addSpModal').modal('hide');
            loadUserData(userId);
        },
        error: function(xhr){
            handleApiError(xhr);
        }
    });
}

function removeSpPermission(spId, userId){
    if (!confirm('Remove this special permission?')) return;

    $.ajax({
        url: base_url + 'api/v1/user_sp_permissions/' + spId,
        method: 'DELETE',
        headers: apiHeaders(),
        success: function(){
            loadUserData(userId);
        },
        error: function(xhr){
            handleApiError(xhr);
        }
    });
}

function addTbPermission(userId, tbName){
    const data = {
        user_id: userId,
        tb: tbName,
        can_list: true
    };

    $.ajax({
        url: base_url + 'api/v1/user_tb_permissions',
        method: 'POST',
        headers: apiHeaders(),
        data: JSON.stringify(data),
        success: function(){
            $('#addTbModal').modal('hide');
            $('#newTbName').val('');
            $('#btnSaveTbPerm').prop('disabled', true);
            loadUserData(userId);
        },
        error: function(xhr){
            handleApiError(xhr);
        }
    });
}

function updateTbPermission(tbId, userId, field, checked, tbName){
    const data = {
        user_id: userId,
        tb: tbName
    };
    data[field] = checked ? 1 : 0;

    $.ajax({
        url: base_url + 'api/v1/user_tb_permissions',
        method: 'POST',
        headers: apiHeaders(),
        data: JSON.stringify(data),
        error: function(xhr){
            handleApiError(xhr);
        }
    });
}

function removeTbPermission(tbId, userId){
    if (!confirm('Remove all table permissions for this table?')) return;

    $.ajax({
        url: base_url + 'api/v1/user_tb_permissions/' + tbId,
        method: 'DELETE',
        headers: apiHeaders(),
        success: function(){
            loadUserData(userId);
        },
        error: function(xhr){
            handleApiError(xhr);
        }
    });
}

$(document).on('click', '.btn-del-sp', function(){
    const spId = $(this).data('sp-id');
    const userId = $(this).data('user-id');
    removeSpPermission(spId, userId);
});

$(document).on('click', '.btn-del-tb', function(){
    const tbId = $(this).data('tb-id');
    const userId = $(this).data('user-id');
    removeTbPermission(tbId, userId);
});

$(document).on('change', '.tb-perm-checkbox', function(){
    const tbId = $(this).data('tb-id');
    const userId = $(this).data('user-id');
    const field = $(this).data('field');
    const tbName = $(this).data('table');
    const checked = $(this).prop('checked');
    updateTbPermission(tbId, userId, field, checked, tbName);
});

function escapeHtml(str){
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
</script>

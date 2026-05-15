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


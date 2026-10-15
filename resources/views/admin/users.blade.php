@include('partials.header', ['title' => 'All Users'])

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">
                    <div class="title-header option-title">
                        <h5>All Users</h5>
                        <a href="javascript:void(0)" id="openAddUserBtn" class="align-items-center btn btn-theme d-flex">
                            <i data-feather="plus"></i>Add New
                        </a>
                    </div>

                    <div id="ajaxNotice" class="alert alert-success d-none" role="alert"></div>
                    <div id="ajaxError" class="alert alert-danger d-none" role="alert"></div>

                    <form id="searchForm" class="row">
                        <div class="mb-4 col-lg-4 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1" for="searchName">Name</label>
                            <input class="form-control" type="text" id="searchName">
                        </div>
                        <div class="mb-4 col-lg-4 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1" for="searchEmail">Email</label>
                            <input class="form-control" type="text" id="searchEmail">
                        </div>
                        <div class="mb-4 col-lg-3 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1" for="searchStatus">Status</label>
                            <select class="form-control" id="searchStatus">
                                <option value="">-- All --</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="mb-4 col-lg-1 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1">&nbsp;</label>
                            <button type="submit" class="btn btn-theme d-flex w-100">Search</button>
                        </div>
                    </form>

                    <div class="table-responsive table-product">
                        <table class="table all-package theme-table crm-table">
                            <thead>
                                <tr>
                                    <th style="width: 6%">ID</th>
                                    <th style="width: 24%">Name</th>
                                    <th style="width: 28%">Email</th>
                                    <th style="width: 14%">Status</th>
                                    <th style="width: 14%">Added Date</th>
                                    <th style="width: 14%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <tr><td colspan="6" class="text-center py-4">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex align-items-center justify-content-between flex-wrap mt-3 pagination-bar">
                        <p class="mb-0" id="paginationSummary"></p>
                        <ul class="pagination mb-0" id="paginationControls"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addUserForm">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="addUserName">Name</label>
                        <input class="form-control" type="text" name="name" id="addUserName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="addUserEmail">Email</label>
                        <input class="form-control" type="email" name="email" id="addUserEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="addUserPassword">Password</label>
                        <input class="form-control" type="password" name="password" id="addUserPassword" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="addUserStatus">Status</label>
                        <select class="form-control" name="status" id="addUserStatus">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-theme">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update User Modal -->
<div class="modal fade" id="updateUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="updateUserForm">
                <input type="hidden" id="updateUserId">
                <div class="modal-header">
                    <h5 class="modal-title">Update User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="updateUserName">Name</label>
                        <input class="form-control" type="text" name="name" id="updateUserName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="updateUserEmail">Email</label>
                        <input class="form-control" type="email" name="email" id="updateUserEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="updateUserStatus">Status</label>
                        <select class="form-control" name="status" id="updateUserStatus">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-theme">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade theme-modal remove-coupon" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-block text-center">
                <h5 class="modal-title w-100">Are You Sure?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="remove-box">
                    <p>This will permanently delete this admin user account.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-animation btn-md fw-bold" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-animation btn-md fw-bold" id="confirmDeleteUserBtn">Yes</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    var urls = {
        data: @json(route('users.data')),
        store: @json(route('users.store')),
        base: @json(url('users'))
    };
    var currentPage = 1;
    var currentUsers = {};
    var deleteUserId = null;

    function renderUserRow(user) {
        var deleteBtn = user.is_self ? '' :
            '<li><a href="javascript:void(0)" class="delete-user-btn" data-id="' + user.id + '" title="Delete"><i class="ri-delete-bin-line"></i></a></li>';
        return '<tr>' +
            '<td><strong>#' + user.id + '</strong></td>' +
            '<td title="' + CRM.escapeHtml(user.name) + '">' + CRM.escapeHtml(user.name) + (user.is_self ? ' <span class="badge badge-light-primary">You</span>' : '') + '</td>' +
            '<td title="' + CRM.escapeHtml(user.email) + '">' + CRM.escapeHtml(user.email) + '</td>' +
            '<td><span class="badge-status ' + CRM.statusClass(user.status) + '">' + CRM.escapeHtml(user.status) + '</span></td>' +
            '<td>' + CRM.escapeHtml(user.created_at) + '</td>' +
            '<td><ul>' +
                '<li><a href="javascript:void(0)" class="edit-user-btn" data-id="' + user.id + '" title="Edit"><i class="ri-pencil-line"></i></a></li>' +
                deleteBtn +
            '</ul></td>' +
        '</tr>';
    }

    function loadUsers(page) {
        $.getJSON(urls.data, {
            page: page || 1,
            name: $('#searchName').val(),
            email: $('#searchEmail').val(),
            status: $('#searchStatus').val()
        }, function (res) {
            var $body = $('#usersTableBody').empty();
            currentUsers = {};
            currentPage = res.pagination.page;
            if (res.users.length === 0) {
                $body.append('<tr><td colspan="6" class="text-center py-4">No users found.</td></tr>');
            }
            res.users.forEach(function (user) {
                currentUsers[user.id] = user;
                $body.append(renderUserRow(user));
            });
            CRM.renderPagination(res.pagination, loadUsers);
        }).fail(function (xhr) {
            CRM.showError(CRM.errorMessage(xhr, 'Failed to load users.'));
        });
    }

    loadUsers(1);

    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        CRM.clearAlerts();
        loadUsers(1);
    });

    $('#openAddUserBtn').on('click', function () {
        $('#addUserForm')[0].reset();
        CRM.modal('addUserModal').show();
    });

    $('#addUserForm').on('submit', function (e) {
        e.preventDefault();
        CRM.clearAlerts();
        CRM.send('POST', urls.store, {
            name: $('#addUserName').val(),
            email: $('#addUserEmail').val(),
            password: $('#addUserPassword').val(),
            status: $('#addUserStatus').val()
        }, 'Failed to add user.').done(function () {
            CRM.modal('addUserModal').hide();
            CRM.showNotice('User added.');
            loadUsers(1);
        });
    });

    $(document).on('click', '.edit-user-btn', function () {
        var user = currentUsers[$(this).data('id')];
        $('#updateUserId').val(user.id);
        $('#updateUserName').val(user.name);
        $('#updateUserEmail').val(user.email);
        $('#updateUserStatus').val(user.status);
        CRM.modal('updateUserModal').show();
    });

    $('#updateUserForm').on('submit', function (e) {
        e.preventDefault();
        CRM.clearAlerts();
        CRM.send('PUT', urls.base + '/' + $('#updateUserId').val(), {
            name: $('#updateUserName').val(),
            email: $('#updateUserEmail').val(),
            status: $('#updateUserStatus').val()
        }, 'Failed to update user.').done(function () {
            CRM.modal('updateUserModal').hide();
            CRM.showNotice('User updated.');
            loadUsers(currentPage);
        });
    });

    $(document).on('click', '.delete-user-btn', function () {
        deleteUserId = $(this).data('id');
        CRM.modal('deleteUserModal').show();
    });

    $('#confirmDeleteUserBtn').on('click', function () {
        CRM.clearAlerts();
        CRM.modal('deleteUserModal').hide();
        CRM.send('DELETE', urls.base + '/' + deleteUserId, {}, 'Failed to delete user.').done(function () {
            CRM.showNotice('User deleted.');
            loadUsers(currentPage);
        });
    });
});
</script>

@include('partials.footer')

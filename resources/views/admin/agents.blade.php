@include('partials.header', ['title' => 'All Agents'])

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">
                    <div class="title-header option-title">
                        <h5>All Agents</h5>
                        <a href="javascript:void(0)" id="openAddAgentBtn" class="align-items-center btn btn-theme d-flex">
                            <i data-feather="plus"></i>Add New
                        </a>
                    </div>

                    <div id="ajaxNotice" class="alert alert-success d-none" role="alert"></div>
                    <div id="ajaxError" class="alert alert-danger d-none" role="alert"></div>

                    <form id="searchForm" class="row">
                        <div class="mb-4 col-lg-3 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1" for="searchName">Employee Name</label>
                            <input class="form-control" type="text" id="searchName">
                        </div>
                        <div class="mb-4 col-lg-3 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1" for="searchPhone">Phone</label>
                            <input class="form-control" type="text" id="searchPhone">
                        </div>
                        <div class="mb-4 col-lg-3 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1" for="searchEmail">Email</label>
                            <input class="form-control" type="text" id="searchEmail">
                        </div>
                        <div class="mb-4 col-lg-2 col-md-6 col-sm-12">
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
                                    <th style="width: 20%">Employee Name</th>
                                    <th style="width: 14%">Phone</th>
                                    <th style="width: 22%">Email</th>
                                    <th style="width: 12%">Status</th>
                                    <th style="width: 12%">Added Date</th>
                                    <th style="width: 14%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="agentsTableBody">
                                <tr><td colspan="7" class="text-center py-4">Loading...</td></tr>
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

<!-- Add Agent Modal -->
<div class="modal fade" id="addAgentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addAgentForm">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="addAgentName">Employee Name</label>
                        <input class="form-control" type="text" id="addAgentName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="addAgentPhone">Phone</label>
                        <input class="form-control" type="text" id="addAgentPhone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="addAgentEmail">Email</label>
                        <input class="form-control" type="email" id="addAgentEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="addAgentPassword">Password</label>
                        <input class="form-control" type="password" id="addAgentPassword" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="addAgentStatus">Status</label>
                        <select class="form-control" id="addAgentStatus">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-theme">Add Agent</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Agent Modal -->
<div class="modal fade" id="updateAgentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="updateAgentForm">
                <input type="hidden" id="updateAgentId">
                <div class="modal-header">
                    <h5 class="modal-title">Update Agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="updateAgentName">Employee Name</label>
                        <input class="form-control" type="text" id="updateAgentName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="updateAgentPhone">Phone</label>
                        <input class="form-control" type="text" id="updateAgentPhone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="updateAgentEmail">Email</label>
                        <input class="form-control" type="email" id="updateAgentEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="updateAgentStatus">Status</label>
                        <select class="form-control" id="updateAgentStatus">
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
<div class="modal fade theme-modal remove-coupon" id="deleteAgentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-block text-center">
                <h5 class="modal-title w-100">Are You Sure?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="remove-box">
                    <p>This will permanently delete this agent. Leads assigned to them will become unassigned.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-animation btn-md fw-bold" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-animation btn-md fw-bold" id="confirmDeleteAgentBtn">Yes</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    var urls = {
        data: @json(route('agents.data')),
        store: @json(route('agents.store')),
        base: @json(url('agents'))
    };
    var currentPage = 1;
    var currentAgents = {};
    var deleteAgentId = null;

    function renderAgentRow(agent) {
        return '<tr>' +
            '<td><strong>#' + agent.id + '</strong></td>' +
            '<td title="' + CRM.escapeHtml(agent.employee_name) + '">' + CRM.escapeHtml(agent.employee_name) + '</td>' +
            '<td title="' + CRM.escapeHtml(agent.phone) + '">' + CRM.escapeHtml(agent.phone) + '</td>' +
            '<td title="' + CRM.escapeHtml(agent.email) + '">' + CRM.escapeHtml(agent.email) + '</td>' +
            '<td><span class="badge-status ' + CRM.statusClass(agent.status) + '">' + CRM.escapeHtml(agent.status) + '</span></td>' +
            '<td>' + CRM.escapeHtml(agent.created_at) + '</td>' +
            '<td><ul>' +
                '<li><a href="javascript:void(0)" class="edit-agent-btn" data-id="' + agent.id + '" title="Edit"><i class="ri-pencil-line"></i></a></li>' +
                '<li><a href="javascript:void(0)" class="delete-agent-btn" data-id="' + agent.id + '" title="Delete"><i class="ri-delete-bin-line"></i></a></li>' +
            '</ul></td>' +
        '</tr>';
    }

    function loadAgents(page) {
        $.getJSON(urls.data, {
            page: page || 1,
            employee_name: $('#searchName').val(),
            phone: $('#searchPhone').val(),
            email: $('#searchEmail').val(),
            status: $('#searchStatus').val()
        }, function (res) {
            var $body = $('#agentsTableBody').empty();
            currentAgents = {};
            currentPage = res.pagination.page;
            if (res.agents.length === 0) {
                $body.append('<tr><td colspan="7" class="text-center py-4">No agents found.</td></tr>');
            }
            res.agents.forEach(function (agent) {
                currentAgents[agent.id] = agent;
                $body.append(renderAgentRow(agent));
            });
            CRM.renderPagination(res.pagination, loadAgents);
        }).fail(function (xhr) {
            CRM.showError(CRM.errorMessage(xhr, 'Failed to load agents.'));
        });
    }

    loadAgents(1);

    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        CRM.clearAlerts();
        loadAgents(1);
    });

    $('#openAddAgentBtn').on('click', function () {
        $('#addAgentForm')[0].reset();
        CRM.modal('addAgentModal').show();
    });

    $('#addAgentForm').on('submit', function (e) {
        e.preventDefault();
        CRM.clearAlerts();
        CRM.send('POST', urls.store, {
            employee_name: $('#addAgentName').val(),
            phone: $('#addAgentPhone').val(),
            email: $('#addAgentEmail').val(),
            password: $('#addAgentPassword').val(),
            status: $('#addAgentStatus').val()
        }, 'Failed to add agent.').done(function () {
            CRM.modal('addAgentModal').hide();
            CRM.showNotice('Agent added.');
            loadAgents(1);
        });
    });

    $(document).on('click', '.edit-agent-btn', function () {
        var agent = currentAgents[$(this).data('id')];
        $('#updateAgentId').val(agent.id);
        $('#updateAgentName').val(agent.employee_name);
        $('#updateAgentPhone').val(agent.phone);
        $('#updateAgentEmail').val(agent.email);
        $('#updateAgentStatus').val(agent.status);
        CRM.modal('updateAgentModal').show();
    });

    $('#updateAgentForm').on('submit', function (e) {
        e.preventDefault();
        CRM.clearAlerts();
        CRM.send('PUT', urls.base + '/' + $('#updateAgentId').val(), {
            employee_name: $('#updateAgentName').val(),
            phone: $('#updateAgentPhone').val(),
            email: $('#updateAgentEmail').val(),
            status: $('#updateAgentStatus').val()
        }, 'Failed to update agent.').done(function () {
            CRM.modal('updateAgentModal').hide();
            CRM.showNotice('Agent updated.');
            loadAgents(currentPage);
        });
    });

    $(document).on('click', '.delete-agent-btn', function () {
        deleteAgentId = $(this).data('id');
        CRM.modal('deleteAgentModal').show();
    });

    $('#confirmDeleteAgentBtn').on('click', function () {
        CRM.clearAlerts();
        CRM.modal('deleteAgentModal').hide();
        CRM.send('DELETE', urls.base + '/' + deleteAgentId, {}, 'Failed to delete agent.').done(function () {
            CRM.showNotice('Agent deleted.');
            loadAgents(currentPage);
        });
    });
});
</script>

@include('partials.footer')

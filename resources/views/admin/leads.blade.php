@include('partials.header', ['title' => $title])

@php
    // The Unassigned page has no agent column or agent filter.
    $showAgent = $scope !== 'unassigned';
    $statuses = \App\Models\Lead::STATUSES;
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">
                    <div class="title-header option-title">
                        <h5>{{ $title }}</h5>
                        <a href="javascript:void(0)" id="openAddLeadBtn" class="align-items-center btn btn-theme d-flex">
                            <i data-feather="plus"></i>Add New
                        </a>
                    </div>

                    <div id="ajaxNotice" class="alert alert-success d-none" role="alert"></div>
                    <div id="ajaxError" class="alert alert-danger d-none" role="alert"></div>

                    <form id="searchForm" class="row">
                        <div class="mb-4 col-lg-2 col-md-4 col-sm-6">
                            <label class="form-label-title mb-1" for="searchLeadTitle">Lead Title</label>
                            <input class="form-control" type="text" id="searchLeadTitle">
                        </div>
                        <div class="mb-4 col-lg-2 col-md-4 col-sm-6">
                            <label class="form-label-title mb-1" for="searchCustomerName">Customer Name</label>
                            <input class="form-control" type="text" id="searchCustomerName">
                        </div>
                        <div class="mb-4 col-lg-2 col-md-4 col-sm-6">
                            <label class="form-label-title mb-1" for="searchPhone">Phone</label>
                            <input class="form-control" type="text" id="searchPhone">
                        </div>
                        <div class="mb-4 col-lg-2 col-md-4 col-sm-6">
                            <label class="form-label-title mb-1" for="searchEmail">Email</label>
                            <input class="form-control" type="text" id="searchEmail">
                        </div>
                        @if ($showAgent)
                            <div class="mb-4 col-lg-2 col-md-4 col-sm-6">
                                <label class="form-label-title mb-1" for="searchAgent">Assigned Agent</label>
                                <select class="form-control" id="searchAgent">
                                    <option value="">-- All --</option>
                                    @if ($scope === 'all')
                                        <option value="0">Unassigned</option>
                                    @endif
                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->employee_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="mb-4 col-lg-2 col-md-4 col-sm-6">
                            <label class="form-label-title mb-1" for="searchStatus">Status</label>
                            <select class="form-control" id="searchStatus">
                                <option value="">-- All --</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4 col-lg-2 col-md-4 col-sm-6">
                            <label class="form-label-title mb-1">&nbsp;</label>
                            <button type="submit" class="btn btn-theme d-flex w-100">Search</button>
                        </div>
                    </form>

                    <div class="table-responsive table-product">
                        <table class="table all-package theme-table crm-table">
                            <thead>
                                <tr>
                                    <th style="width: 6%">ID</th>
                                    <th style="width: {{ $showAgent ? 15 : 20 }}%">Lead Title</th>
                                    <th style="width: {{ $showAgent ? 12 : 16 }}%">Customer</th>
                                    <th style="width: {{ $showAgent ? 15 : 18 }}%">Contact</th>
                                    @if ($showAgent)
                                        <th style="width: 13%">Assigned Agent</th>
                                    @endif
                                    <th style="width: 12%">Status</th>
                                    <th style="width: {{ $showAgent ? 10 : 13 }}%">Received</th>
                                    <th style="width: {{ $showAgent ? 17 : 15 }}%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="leadsTableBody">
                                <tr><td colspan="{{ $showAgent ? 8 : 7 }}" class="text-center py-4">Loading...</td></tr>
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

<!-- Add / Update Lead Modal (one form for both) -->
<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="leadForm">
                <input type="hidden" id="leadId">
                <div class="modal-header">
                    <h5 class="modal-title" id="leadModalTitle">Add New Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label class="form-label-title mb-1" for="leadTitle">Lead Title</label>
                            <input class="form-control" type="text" id="leadTitle" required>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label class="form-label-title mb-1" for="leadCustomerName">Customer Name</label>
                            <input class="form-control" type="text" id="leadCustomerName" required>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label class="form-label-title mb-1" for="leadPhone">Phone</label>
                            <input class="form-control" type="text" id="leadPhone">
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label class="form-label-title mb-1" for="leadEmail">Email</label>
                            <input class="form-control" type="email" id="leadEmail">
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label class="form-label-title mb-1" for="leadAgent">Assigned Agent</label>
                            <select class="form-control" id="leadAgent">
                                <option value="">-- Unassigned --</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->employee_name }}{{ $agent->status === 'Inactive' ? ' (Inactive)' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label class="form-label-title mb-1" for="leadStatus">Status</label>
                            <select class="form-control" id="leadStatus">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label class="form-label-title mb-1" for="leadMessage">Message</label>
                            <textarea class="form-control" id="leadMessage" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-theme" id="leadSubmitBtn">Add Lead</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notes Modal -->
<div class="modal fade" id="notesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notes for Lead <span id="notesModalLeadNo"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="notesList" class="notes-list mb-3"></div>
                <form id="addNoteForm">
                    <div class="mb-3">
                        <label class="form-label-title mb-1" for="noteText">Add Note</label>
                        <textarea class="form-control" id="noteText" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-theme w-100">Add Note</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade theme-modal remove-coupon" id="deleteLeadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-block text-center">
                <h5 class="modal-title w-100">Are You Sure?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="remove-box">
                    <p>This will permanently delete this lead along with its notes and activity history.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-animation btn-md fw-bold" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-animation btn-md fw-bold" id="confirmDeleteLeadBtn">Yes</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    var scope = @json($scope);
    var showAgent = @json($showAgent);
    var urls = {
        data: @json(route('leads.data')),
        store: @json(route('leads.store')),
        base: @json(url('leads')),
        notes: @json(url('notes'))
    };
    var columnCount = showAgent ? 8 : 7;
    var currentPage = 1;
    var currentLeads = {};
    var deleteLeadId = null;
    var notesLeadId = null;

    function renderLeadRow(lead) {
        var agentCell = '';
        if (showAgent) {
            var agentLabel = lead.agent_name ? CRM.escapeHtml(lead.agent_name) : '<span class="text-muted">Unassigned</span>';
            agentCell = '<td title="' + CRM.escapeHtml(lead.agent_name || 'Unassigned') + '">' + agentLabel + '</td>';
        }
        return '<tr>' +
            '<td><strong>#' + lead.id + '</strong></td>' +
            '<td title="' + CRM.escapeHtml(lead.lead_title) + '">' + CRM.escapeHtml(lead.lead_title) + '</td>' +
            '<td title="' + CRM.escapeHtml(lead.customer_name) + '">' + CRM.escapeHtml(lead.customer_name) + '</td>' +
            '<td class="contact-cell"><div title="' + CRM.escapeHtml(lead.phone) + '">' + CRM.escapeHtml(lead.phone) + '</div>' +
                '<div title="' + CRM.escapeHtml(lead.email) + '">' + CRM.escapeHtml(lead.email) + '</div></td>' +
            agentCell +
            '<td><span class="badge-status ' + CRM.statusClass(lead.status) + '">' + CRM.escapeHtml(lead.status) + '</span></td>' +
            '<td>' + CRM.escapeHtml(lead.created_at) + '</td>' +
            '<td><ul>' +
                '<li><a href="javascript:void(0)" class="edit-lead-btn" data-id="' + lead.id + '" title="Edit"><i class="ri-pencil-line"></i></a></li>' +
                '<li><a href="javascript:void(0)" class="view-notes-btn" data-id="' + lead.id + '" title="Notes"><i class="ri-chat-3-line"></i></a></li>' +
                '<li><a href="javascript:void(0)" class="delete-lead-btn" data-id="' + lead.id + '" title="Delete"><i class="ri-delete-bin-line"></i></a></li>' +
            '</ul></td>' +
        '</tr>';
    }

    function loadLeads(page) {
        $.getJSON(urls.data, {
            page: page || 1,
            assignment: scope === 'all' ? '' : scope,
            lead_title: $('#searchLeadTitle').val(),
            customer_name: $('#searchCustomerName').val(),
            phone: $('#searchPhone').val(),
            email: $('#searchEmail').val(),
            assigned_agent_id: showAgent ? $('#searchAgent').val() : '',
            status: $('#searchStatus').val()
        }, function (res) {
            var $body = $('#leadsTableBody').empty();
            currentLeads = {};
            currentPage = res.pagination.page;
            if (res.leads.length === 0) {
                $body.append('<tr><td colspan="' + columnCount + '" class="text-center py-4">No leads found.</td></tr>');
            }
            res.leads.forEach(function (lead) {
                currentLeads[lead.id] = lead;
                $body.append(renderLeadRow(lead));
            });
            CRM.renderPagination(res.pagination, loadLeads);
        }).fail(function (xhr) {
            CRM.showError(CRM.errorMessage(xhr, 'Failed to load leads.'));
        });
    }

    function loadNotes() {
        $('#notesList').html('<p class="text-muted">Loading...</p>');
        $.getJSON(urls.base + '/' + notesLeadId + '/notes', function (res) {
            if (res.notes.length === 0) {
                $('#notesList').html('<p class="text-muted">No notes yet.</p>');
                return;
            }
            var html = '';
            res.notes.forEach(function (note) {
                html += '<div class="note-item d-flex justify-content-between align-items-start">' +
                    '<div class="flex-grow-1">' +
                        '<p class="mb-1">' + CRM.escapeHtml(note.note).replace(/\n/g, '<br>') + '</p>' +
                        '<div class="note-meta"><strong>' + CRM.escapeHtml(note.author) + '</strong> &middot; ' + CRM.escapeHtml(note.created_at) + '</div>' +
                    '</div>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger ms-2 delete-note-btn" data-note-id="' + note.id + '" title="Delete Note"><i class="ri-delete-bin-line"></i></button>' +
                '</div>';
            });
            $('#notesList').html(html);
        }).fail(function (xhr) {
            $('#notesList').html('<p class="text-danger">' + CRM.escapeHtml(CRM.errorMessage(xhr, 'Failed to load notes.')) + '</p>');
        });
    }

    loadLeads(1);

    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        CRM.clearAlerts();
        loadLeads(1);
    });

    $('#openAddLeadBtn').on('click', function () {
        $('#leadForm')[0].reset();
        $('#leadId').val('');
        $('#leadModalTitle').text('Add New Lead');
        $('#leadSubmitBtn').text('Add Lead');
        CRM.modal('leadModal').show();
    });

    $(document).on('click', '.edit-lead-btn', function () {
        var lead = currentLeads[$(this).data('id')];
        $('#leadId').val(lead.id);
        $('#leadModalTitle').text('Update Lead #' + lead.id);
        $('#leadSubmitBtn').text('Save');
        $('#leadTitle').val(lead.lead_title);
        $('#leadCustomerName').val(lead.customer_name);
        $('#leadPhone').val(lead.phone);
        $('#leadEmail').val(lead.email);
        $('#leadMessage').val(lead.message);
        $('#leadAgent').val(lead.assigned_agent_id || '');
        $('#leadStatus').val(lead.status);
        CRM.modal('leadModal').show();
    });

    $('#leadForm').on('submit', function (e) {
        e.preventDefault();
        CRM.clearAlerts();
        var id = $('#leadId').val();
        var isNew = id === '';
        CRM.send(isNew ? 'POST' : 'PUT', isNew ? urls.store : urls.base + '/' + id, {
            lead_title: $('#leadTitle').val(),
            customer_name: $('#leadCustomerName').val(),
            phone: $('#leadPhone').val(),
            email: $('#leadEmail').val(),
            message: $('#leadMessage').val(),
            assigned_agent_id: $('#leadAgent').val(),
            status: $('#leadStatus').val()
        }, isNew ? 'Failed to add lead.' : 'Failed to update lead.').done(function () {
            CRM.modal('leadModal').hide();
            CRM.showNotice(isNew ? 'Lead added.' : 'Lead updated.');
            loadLeads(isNew ? 1 : currentPage);
        });
    });

    $(document).on('click', '.view-notes-btn', function () {
        notesLeadId = $(this).data('id');
        $('#notesModalLeadNo').text('#' + notesLeadId);
        $('#noteText').val('');
        loadNotes();
        CRM.modal('notesModal').show();
    });

    $('#addNoteForm').on('submit', function (e) {
        e.preventDefault();
        CRM.clearAlerts();
        var $submitBtn = $(this).find('button[type="submit"]').prop('disabled', true);
        CRM.send('POST', urls.base + '/' + notesLeadId + '/notes', { note: $('#noteText').val() }, 'Failed to add note.')
            .done(function () {
                $('#noteText').val('');
                loadNotes();
                CRM.showNotice('Note added.');
            })
            .always(function () {
                $submitBtn.prop('disabled', false);
            });
    });

    $(document).on('click', '.delete-note-btn', function () {
        if (!confirm('Delete this note?')) {
            return;
        }
        CRM.clearAlerts();
        CRM.send('DELETE', urls.notes + '/' + $(this).data('note-id'), {}, 'Failed to delete note.').done(function () {
            loadNotes();
            CRM.showNotice('Note deleted.');
        });
    });

    $(document).on('click', '.delete-lead-btn', function () {
        deleteLeadId = $(this).data('id');
        CRM.modal('deleteLeadModal').show();
    });

    $('#confirmDeleteLeadBtn').on('click', function () {
        CRM.clearAlerts();
        CRM.modal('deleteLeadModal').hide();
        CRM.send('DELETE', urls.base + '/' + deleteLeadId, {}, 'Failed to delete lead.').done(function () {
            CRM.showNotice('Lead deleted.');
            loadLeads(currentPage);
        });
    });
});
</script>

@include('partials.footer')

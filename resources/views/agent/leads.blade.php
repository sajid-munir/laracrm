@include('partials.header', ['title' => 'My Leads'])

@php
    $statuses = \App\Models\Lead::STATUSES;
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">
                    <div class="title-header option-title">
                        <h5>My Leads</h5>
                    </div>

                    <div id="ajaxNotice" class="alert alert-success d-none" role="alert"></div>
                    <div id="ajaxError" class="alert alert-danger d-none" role="alert"></div>

                    <form id="searchForm" class="row">
                        <div class="mb-4 col-lg-4 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1" for="searchLeadTitle">Lead Title</label>
                            <input class="form-control" type="text" id="searchLeadTitle">
                        </div>
                        <div class="mb-4 col-lg-4 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1" for="searchCustomerName">Customer Name</label>
                            <input class="form-control" type="text" id="searchCustomerName">
                        </div>
                        <div class="mb-4 col-lg-2 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1" for="searchStatus">Status</label>
                            <select class="form-control" id="searchStatus">
                                <option value="">-- All --</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4 col-lg-2 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1">&nbsp;</label>
                            <button type="submit" class="btn btn-theme d-flex w-100">Search</button>
                        </div>
                    </form>

                    <div class="table-responsive table-product">
                        <table class="table all-package theme-table crm-table">
                            <thead>
                                <tr>
                                    <th style="width: 6%">ID</th>
                                    <th style="width: 20%">Lead Title</th>
                                    <th style="width: 16%">Customer</th>
                                    <th style="width: 18%">Contact</th>
                                    <th style="width: 13%">Status</th>
                                    <th style="width: 12%">Received</th>
                                    <th style="width: 15%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="leadsTableBody">
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

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="statusForm">
                <div class="modal-header">
                    <h5 class="modal-title">Update Lead <span id="statusModalLeadNo"></span> Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label-title mb-1" for="statusSelect">Status</label>
                    <select class="form-control" id="statusSelect">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-theme">Save</button>
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

<script>
$(function () {
    var urls = {
        data: @json(route('agent.leads.data')),
        base: @json(url('agent/leads'))
    };
    var currentPage = 1;
    var currentLeads = {};
    var activeLeadId = null;

    function renderLeadRow(lead) {
        // Leads the agent has not updated yet are highlighted.
        return '<tr class="' + (lead.is_new ? 'lead-row-new' : '') + '">' +
            '<td><strong>#' + lead.id + '</strong></td>' +
            '<td title="' + CRM.escapeHtml(lead.lead_title) + '">' + CRM.escapeHtml(lead.lead_title) + '</td>' +
            '<td title="' + CRM.escapeHtml(lead.customer_name) + '">' + CRM.escapeHtml(lead.customer_name) + '</td>' +
            '<td class="contact-cell"><div title="' + CRM.escapeHtml(lead.phone) + '">' + CRM.escapeHtml(lead.phone) + '</div>' +
                '<div title="' + CRM.escapeHtml(lead.email) + '">' + CRM.escapeHtml(lead.email) + '</div></td>' +
            '<td><span class="badge-status ' + CRM.statusClass(lead.status) + '">' + CRM.escapeHtml(lead.status) + '</span></td>' +
            '<td>' + CRM.escapeHtml(lead.created_at) + '</td>' +
            '<td><ul>' +
                '<li><a href="javascript:void(0)" class="update-status-btn" data-id="' + lead.id + '" title="Update Status"><i class="ri-pencil-line"></i></a></li>' +
                '<li><a href="javascript:void(0)" class="view-notes-btn" data-id="' + lead.id + '" title="Notes"><i class="ri-chat-3-line"></i></a></li>' +
            '</ul></td>' +
        '</tr>';
    }

    function loadLeads(page) {
        $.getJSON(urls.data, {
            page: page || 1,
            lead_title: $('#searchLeadTitle').val(),
            customer_name: $('#searchCustomerName').val(),
            status: $('#searchStatus').val()
        }, function (res) {
            var $body = $('#leadsTableBody').empty();
            currentLeads = {};
            currentPage = res.pagination.page;
            if (res.leads.length === 0) {
                $body.append('<tr><td colspan="7" class="text-center py-4">No leads found.</td></tr>');
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
        $.getJSON(urls.base + '/' + activeLeadId + '/notes', function (res) {
            if (res.notes.length === 0) {
                $('#notesList').html('<p class="text-muted">No notes yet.</p>');
                return;
            }
            var html = '';
            res.notes.forEach(function (note) {
                html += '<div class="note-item">' +
                    '<p class="mb-1">' + CRM.escapeHtml(note.note).replace(/\n/g, '<br>') + '</p>' +
                    '<div class="note-meta"><strong>' + CRM.escapeHtml(note.author) + '</strong> &middot; ' + CRM.escapeHtml(note.created_at) + '</div>' +
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

    $(document).on('click', '.update-status-btn', function () {
        var lead = currentLeads[$(this).data('id')];
        activeLeadId = lead.id;
        $('#statusModalLeadNo').text('#' + lead.id);
        $('#statusSelect').val(lead.status);
        CRM.modal('statusModal').show();
    });

    $('#statusForm').on('submit', function (e) {
        e.preventDefault();
        CRM.clearAlerts();
        CRM.send('PATCH', urls.base + '/' + activeLeadId + '/status', { status: $('#statusSelect').val() }, 'Failed to update status.')
            .done(function () {
                CRM.modal('statusModal').hide();
                CRM.showNotice('Lead status updated.');
                loadLeads(currentPage);
            });
    });

    $(document).on('click', '.view-notes-btn', function () {
        activeLeadId = $(this).data('id');
        $('#notesModalLeadNo').text('#' + activeLeadId);
        $('#noteText').val('');
        loadNotes();
        CRM.modal('notesModal').show();
    });

    $('#addNoteForm').on('submit', function (e) {
        e.preventDefault();
        CRM.clearAlerts();
        var $submitBtn = $(this).find('button[type="submit"]').prop('disabled', true);
        CRM.send('POST', urls.base + '/' + activeLeadId + '/notes', { note: $('#noteText').val() }, 'Failed to add note.')
            .done(function () {
                $('#noteText').val('');
                loadNotes();
                CRM.showNotice('Note added.');
            })
            .always(function () {
                $submitBtn.prop('disabled', false);
            });
    });
});
</script>

@include('partials.footer')

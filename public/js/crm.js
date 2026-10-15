/*
 * Helpers shared by the CRM list pages (users, agents, leads).
 * Needs jQuery and Bootstrap, which the header loads first.
 */
var CRM = (function ($) {
    // Every AJAX call sends the CSRF token and asks for JSON (so validation errors come back as JSON).
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    function escapeHtml(str) {
        return $('<div>').text(str == null ? '' : str).html();
    }

    function statusClass(status) {
        return 'status-' + String(status).replace(/ /g, '-');
    }

    function showNotice(msg) {
        $('#ajaxError').addClass('d-none');
        $('#ajaxNotice').removeClass('d-none').text(msg);
    }

    function showError(msg) {
        $('#ajaxNotice').addClass('d-none');
        $('#ajaxError').removeClass('d-none').text(msg);
    }

    function clearAlerts() {
        $('#ajaxNotice').addClass('d-none');
        $('#ajaxError').addClass('d-none');
    }

    // First readable error from a failed request: a validation message, our own message, or the fallback.
    function errorMessage(xhr, fallback) {
        var res = xhr && xhr.responseJSON;
        if (res && res.errors) {
            var firstField = Object.keys(res.errors)[0];
            if (firstField && res.errors[firstField].length) {
                return res.errors[firstField][0];
            }
        }
        if (res && res.message && xhr.status !== 500) {
            return res.message;
        }
        if (xhr && xhr.status === 419) {
            return 'Your session has expired. Please refresh the page.';
        }
        return fallback;
    }

    // Sends a JSON request; resolves with the response, or shows the error and rejects.
    function send(method, url, data, fallbackError) {
        return $.ajax({ url: url, method: method, data: data, dataType: 'json' })
            .fail(function (xhr) {
                showError(errorMessage(xhr, fallbackError));
            });
    }

    function modal(id) {
        return bootstrap.Modal.getOrCreateInstance(document.getElementById(id));
    }

    // Fills "#paginationSummary" and "#paginationControls"; page links call onPage(pageNumber).
    function renderPagination(pagination, onPage) {
        var $summary = $('#paginationSummary');
        var $controls = $('#paginationControls');

        if (pagination.total === 0) {
            $summary.text('');
            $controls.empty();
            return;
        }

        var firstRow = (pagination.page - 1) * pagination.per_page + 1;
        var lastRow = Math.min(pagination.page * pagination.per_page, pagination.total);
        $summary.text('Showing ' + firstRow + '-' + lastRow + ' of ' + pagination.total);

        var html = '<li class="page-item' + (pagination.page <= 1 ? ' disabled' : '') + '">' +
            '<a class="page-link" href="javascript:void(0)" data-page="' + (pagination.page - 1) + '">Previous</a></li>';

        for (var p = 1; p <= pagination.total_pages; p++) {
            html += '<li class="page-item' + (p === pagination.page ? ' active' : '') + '">' +
                '<a class="page-link" href="javascript:void(0)" data-page="' + p + '">' + p + '</a></li>';
        }

        html += '<li class="page-item' + (pagination.page >= pagination.total_pages ? ' disabled' : '') + '">' +
            '<a class="page-link" href="javascript:void(0)" data-page="' + (pagination.page + 1) + '">Next</a></li>';

        $controls.html(html);
        $controls.off('click').on('click', '.page-link', function () {
            var $li = $(this).closest('.page-item');
            if (!$li.hasClass('disabled') && !$li.hasClass('active')) {
                onPage($(this).data('page'));
            }
        });
    }

    return {
        escapeHtml: escapeHtml,
        statusClass: statusClass,
        showNotice: showNotice,
        showError: showError,
        clearAlerts: clearAlerts,
        errorMessage: errorMessage,
        send: send,
        modal: modal,
        renderPagination: renderPagination
    };
})(jQuery);

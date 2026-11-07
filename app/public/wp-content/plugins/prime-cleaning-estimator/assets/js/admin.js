/**
 * Prime Cleaning Estimator Admin JS
 */

jQuery(document).ready(function ($) {
    $('.pce-status-dropdown').on('change', function () {
        var dropdown = $(this);
        var id = dropdown.data('id');
        var newStatus = dropdown.val();
        var spinner = $('#spinner-' + id);

        spinner.addClass('is-active');
        dropdown.prop('disabled', true);

        $.ajax({
            url: pceAdminData.ajax_url,
            type: 'POST',
            data: {
                action: 'pce_update_status',
                nonce: pceAdminData.nonce,
                enquiry_id: id,
                status: newStatus
            },
            success: function (res) {
                spinner.removeClass('is-active');
                dropdown.prop('disabled', false);
                if (!res.success) {
                    alert(res.data && res.data.message ? res.data.message : 'Error updating status');
                }
            },
            error: function () {
                spinner.removeClass('is-active');
                dropdown.prop('disabled', false);
                alert('Connection error while updating status.');
            }
        });
    });
});

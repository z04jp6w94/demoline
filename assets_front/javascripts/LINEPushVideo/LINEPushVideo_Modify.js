
jQuery(document).ready(function () {
    $('#send_date').hide();
    var lpv_send_status = $('#lpv_send_status').val();
    if (lpv_send_status == '2') {
        $('#send_date').show();
    }
});

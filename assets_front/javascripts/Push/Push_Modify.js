
jQuery(document).ready(function () {
    $('#sh_name').hide();
    $('#sh_content').hide();
    $('#sh_url').hide();
    $('#sh_img').hide();
    $('#send_date').hide();
    var _p_send_status = $('#p_send_status').val();
    if (_p_send_status == '2') {
        $('#send_date').show();
    }
    var _lp_type = $('#lp_type').val();
    if (_lp_type == '1') {
        $('#sh_name').show();
        $('#sh_content').show();
        $('#sh_url').hide();
        $('#sh_img').hide();
        $('#p_url').val('');
        $('#p_img').val('');
        $('#uploadFileDelete').html('');
        $('#uploadFilePreview').html('');
        $('#uploadFileMsg').text('');
    } else if (_lp_type == '2') {
        $('#sh_name').show();
        $('#sh_content').hide();
        $('#sh_url').show();
        $('#sh_img').show();
        $('#p_content').val('');
    } else if (_lp_type == '3') {
        $('#sh_name').show();
        $('#sh_content').show();
        $('#sh_url').show();
        $('#sh_img').show();
    }
});


jQuery(document).ready(function () {

});

function Checkbox_count() {
    var len = $("input[name='ct_id[]']:checked").length;
    if (len > 0) {
        $('#tips').tooltip("destroy");
    } else {
        $('#tips').tooltip().attr('data-original-title', '請至少勾選一個發送目標');
        $('#tips').tooltip("show");
    }
}

function chkFormField(updateForm) {
    var check_length = document.getElementsByName('ct_id[]');
    var count_checked = 0;
    for (var i = 0; i < check_length.length; i++) {
        if (check_length[i].checked) {
            count_checked++;
        }
    }
    if (count_checked == 0) {
        $('#tips').tooltip().attr('data-original-title', '請至少勾選一個發送目標');
        $('#tips').tooltip("show");
    } else {
        $("#Save").attr('disabled', true);
        updateForm.submit();
    }
}
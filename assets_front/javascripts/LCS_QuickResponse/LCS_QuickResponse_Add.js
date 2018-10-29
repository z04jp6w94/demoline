
jQuery(document).ready(function () {

});

function change_cp_id(value) {
    if (value == '') {
        $('#cp_id').tooltip("show");
    } else {
        $('#cp_id').tooltip("hide");
    }
}

function chkFormField(createForm) {

    if (FormDataEmptyTooltip(createForm.lcsmc_content)) {
        $('#lcsmc_content').tooltip().attr('data-original-title', '請輸入快速回應文字');
        $('#lcsmc_content').tooltip("show");
    } else {
        $("#Save").attr('disabled', true);
        createForm.submit();
    }

}
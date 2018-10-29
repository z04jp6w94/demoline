
jQuery(document).ready(function () {

});

function chkFormField(updateForm) {
    var lcsmc_type = $('#lcsmc_type').val();

    if (FormDataEmptyTooltip(updateForm.lcsmc_content)) {
        if (lcsmc_type == 1) {
            $('#lcsmc_content').tooltip().attr('data-original-title', '請選取客服上線時間');
        } else if (lcsmc_type == 2) {
            $('#lcsmc_content').tooltip().attr('data-original-title', '請選取客服下線時間');
        } else if (lcsmc_type == 3) {
            $('#lcsmc_content').tooltip().attr('data-original-title', '請輸入無客服在線回應文字');
        } else if (lcsmc_type == 4) {
            $('#lcsmc_content').tooltip().attr('data-original-title', '請輸入客服問候語');
        } else if (lcsmc_type == 5) {
            $('#lcsmc_content').tooltip().attr('data-original-title', '請輸入客服離開文字');
        } else if (lcsmc_type == 6) {
            $('#lcsmc_content').tooltip().attr('data-original-title', '請輸入下班時間回應');
        } else if (lcsmc_type == 7) {
            $('#lcsmc_content').tooltip().attr('data-original-title', '請輸入客服忙線回應');
        } else if (lcsmc_type == 8) {
            $('#lcsmc_content').tooltip().attr('data-original-title', '請輸入快速回應文字');
        }
        $('#lcsmc_content').tooltip("show");
    } else {
        $("#Save").attr('disabled', true);
        updateForm.submit();
    }
}
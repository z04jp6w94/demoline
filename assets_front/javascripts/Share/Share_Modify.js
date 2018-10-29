
jQuery(document).ready(function () {

    /*img*/
    $(document).on('change', '#sa_awards_img', function () {
        $("#ischange_sa_awards_img").val("Y");
        uploadFilePreviewIMG(this);
    });
    $(document).on('click', '#uploadFileDeleteButton', function () {
        $("#ischange_sa_awards_img").val("Y");
        $('#uploadFileChooseButton').html("<input type='file' name='sa_awards_img' id='sa_awards_img' style='display:none;'></input>選擇檔案");
        $('#uploadFileDelete').html("");
        $('#uploadFilePreview').html("");
        $('#uploadFileMsg').text("");
    });

});

function chkFormField(updateForm) {

    if (FormDataEmptyTooltip(updateForm.sa_title)) {
        $('#sa_title').tooltip().attr('data-original-title', '請輸入活動主旨');
        $('#sa_title').tooltip("show");
    } else if (FormDataEmptyTooltip(updateForm.sa_content)) {
        $('#sa_content').tooltip().attr('data-original-title', '請輸入活動內容');
        $('#sa_content').tooltip("show");
    } else if (/[^\x00-\xff\u4E00-\u9FA5]/g.test($('#sa_content').val())) {
        alert('請確認活動內容是否有非半形字元!');    
    } else if (FormDataEmptyTooltip(updateForm.sa_awards_content)) {
        $('#sa_awards_content').tooltip().attr('data-original-title', '請輸入活動獎項內容');
        $('#sa_awards_content').tooltip("show");
    } else if (FormDataEmptyTooltip(updateForm.sa_standard_content)) {
        $('#sa_standard_content').tooltip().attr('data-original-title', '請輸入達標回應訊息');
        $('#sa_standard_content').tooltip("show");
    } else {
        $("#sa_awards_img").show();
        $("#Save").attr('disabled', true);
        updateForm.submit();
    }

}
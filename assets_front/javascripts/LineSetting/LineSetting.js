
jQuery(document).ready(function () {
    /*img*/
    $(document).on('change', '#ls_customer_richmenu_img', function () {
        $("#ischange_ls_customer_richmenu_img").val("Y");
        uploadFileLineRichMenuIMG(this);
    });
    $(document).on('click', '#uploadFileDeleteButton', function () {
        $("#ischange_ls_customer_richmenu_img").val("Y");
        $('#uploadFileChooseButton').html("<input type='file' name='ls_customer_richmenu_img' id='ls_customer_richmenu_img' style='display:none;' required></input>選擇檔案");
        $('#uploadFileDelete').html("");
        $('#uploadFilePreview').html("");
        $('#uploadFileMsg').text("請上傳寬度: 2500px 高度: 1686px圖檔");
    });
});

function chkFormField(createForm) {

    if (FormDataEmptyTooltip(createForm.ls_follow_content)) {
        $('#ls_follow_content').tooltip().attr('data-original-title', '請填寫加入歡迎詞');
        $('#ls_follow_content').tooltip("show");
    } else {
        var img_status = $('#richmenu_img_status').val();
        if (img_status == 'Y') {
            $("#Save").attr('disabled', true);
            $("#SaveAndUse").attr('disabled', true);
            createForm.submit();
        } else {
            $('#tip_rs_img').tooltip().attr('data-original-title', '請上傳正確格式的圖片');
            $('#tip_rs_img').tooltip("show");
        }
    }

}
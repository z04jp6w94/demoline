
jQuery(document).ready(function () {
    $('#send_date').hide();
    /*radio*/
    $('input[type=radio][name=lpv_send_status]').change(function () {
        if (this.value == '1') {
            $('#send_date').hide();
            $('#;pv_send_date').val('');
        } else if (this.value == '2') {
            $('#send_date').show();
        }
    });
    /*img*/
    $(document).on('change', '#lpv_img', function () {
        $("#ischange_lpv_img").val("Y");
        $('#sh_img').tooltip().attr('data-original-title', '');
        uploadFilePreviewIMG(this);
    });
    $(document).on('click', '#uploadFileDeleteButton', function () {
        $("#ischange_p_img").val("Y");
        $('#uploadFileChooseButton').html("<input type='file' name='lpv_img' id='lpv_img' style='display:none;'></input>選擇檔案");
        $('#uploadFileDelete').html("");
        $('#uploadFilePreview').html("");
        $('#uploadFileMsg').text("");
    });

    /*video*/
    $(document).on('change', '#lpv_video', function () {
        $("#ischange_lpv_video").val("Y");
        $('#sh_img').tooltip().attr('data-original-title', '');
        uploadFilePreviewVideo(this);
    });
    $(document).on('click', '#uploadVideoDeleteButton', function () {
        $("#ischange_lpv_video").val("Y");
        $('#uploadVideoChooseButton').html("<input type='file' name='lpv_video' id='lpv_video' style='display:none;'></input>選擇檔案");
        $('#uploadVideoDelete').html("");
        $('#uploadVideoPreview').html("");
        $('#uploadVideoMsg').text("");
    });
});

function chkFormField(createForm) {
    var _send_status = $('input[type=radio][name=lpv_send_status]:checked').val();

    if (FormDataEmptyTooltip(createForm.lpv_name)) {
        $('#lpv_name').tooltip().attr('data-original-title', '請輸入影片名稱');
        $('#lpv_name').tooltip("show");
    } else if (FormDataEmptyTooltip(createForm.lpv_img)) {
        $('#tool_tip_img').tooltip().attr('data-original-title', '請上傳一張圖片');
        $('#tool_tip_img').tooltip("show");
    } else if ($('#uploadFileMsg').text() != "") {
        alert("請選擇正確的檔案格式(jpg/png/gif)");
    } else if (FormDataEmptyTooltip(createForm.lpv_video)) {
        $('#tool_tip_video').tooltip().attr('data-original-title', '請上傳一則.mp4影片');
        $('#tool_tip_video').tooltip("show");
    } else if ($('#uploadVideoMsg').text() != "") {
        alert("請選擇正確的檔案格式(mp4)且檔案小於10MB");
    } else {
        if (_send_status == '2') {
            if (FormDataEmptyTooltip(createForm.lpv_send_date)) {
                $('#lpv_send_date').tooltip().attr('data-original-title', '請選擇發送日期');
                $('#lpv_send_date').tooltip("show");
            } else {
                $("#lpv_img").show();
                $("#Save").attr('disabled', true);
                createForm.submit();
            }
        } else {
            $("#lpv_img").show();
            $("#Save").attr('disabled', true);
            createForm.submit();
        }
    }

}
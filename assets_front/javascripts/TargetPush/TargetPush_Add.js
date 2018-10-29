
jQuery(document).ready(function () {
    $('#sh_name').hide();
    $('#sh_content').hide();
    $('#sh_url').hide();
    $('#sh_img').hide();
    $('#send_date').hide();
    /*radio*/
    $('input[type=radio][name=p_send_status]').change(function () {
        if (this.value == '1') {
            $('#send_date').hide();
            $('#p_send_date').val('');
        } else if (this.value == '2') {
            $('#send_date').show();
        } else if (this.value == '3') {
            $('#send_date').hide();
            $('#p_send_date').val('');
        }
    });
    /*img*/
    $(document).on('change', '#p_img', function () {
        $("#ischange_p_img").val("Y");
        $('#sh_img').tooltip().attr('data-original-title', '');
        uploadFilePreviewIMG(this);
    });
    $(document).on('click', '#uploadFileDeleteButton', function () {
        $("#ischange_p_img").val("Y");
        $('#uploadFileChooseButton').html("<input type='file' name='p_img' id='p_img' style='display:none;'></input>選擇檔案");
        $('#uploadFileDelete').html("");
        $('#uploadFilePreview').html("");
        $('#uploadFileMsg').text("");
        var _type = $('#lp_type').val();
        if (_type == '2') {
            $('#sh_img').tooltip().attr('data-original-title', '建議使用長寬相同之圖片，避免發送至用戶端變形');
            $("#sh_img").tooltip("show");
        } else {
            $("#sh_img").tooltip("destroy");
        }
    });

    $('#lp_type').change(function () {
        var num = this.value;
        if (num == '1') {
            $('#p_content').tooltip().attr('data-original-title', '限制字數1000');
            $('#p_content').attr('maxlength', '1000');
            $('#p_content').attr('placeholder', '限制字數1000');
            $('#sh_name').show();
            $('#sh_content').show();
            $('#sh_url').hide();
            $('#sh_img').hide();
            $('#p_url').val('');
            $('#p_img').val('');
            $('#uploadFileDelete').html('');
            $('#uploadFilePreview').html('');
            $('#uploadFileMsg').text('');
            /* sign */
            $('#lp_type').tooltip("hide");
        } else if (num == '2') {
            $('#sh_name').show();
            $('#sh_content').hide();
            $('#sh_url').show();
            $('#sh_img').show();
            $('#sh_img').tooltip().attr('data-original-title', '建議使用長寬相同之圖片，避免發送至用戶端變形');
            $('#sh_img').tooltip("show");
            $('#p_content').val('');
            /* sign */
            $('#lp_type').tooltip("hide");
        } else if (num == '3') {
            $('#p_content').tooltip().attr('data-original-title', '限制字數60');
            $('#p_content').attr('maxlength', '60');
            $('#p_content').attr('placeholder', '限制字數60');
            $('#sh_name').show();
            $('#sh_content').show();
            $('#sh_url').show();
            $('#sh_img').show();
            $('#sh_img').tooltip("destroy");
            /* sign */
            $('#lp_type').tooltip("hide");
        } else {
            $('#sh_name').hide();
            $('#sh_content').hide();
            $('#sh_url').hide();
            $('#sh_img').hide();
            $('#p_name').val('');
            $('#p_content').val('');
            $('#p_url').val('');
            $('#p_img').val('');
        }
    });

});

function change_cp_id(value) {
    if (value == '') {
        $('#cp_id').tooltip("show");
    } else {
        $('#cp_id').tooltip("hide");
    }
}

function chkFormField(createForm) {
    var _send_status = $('input[type=radio][name=p_send_status]:checked').val();
    var _lp_type = $('#lp_type').val();
    var _form_status = false;

    if (_lp_type == 1) {
        $('#lp_type').tooltip("hide");
        if (FormDataEmptyTooltip(createForm.p_name)) {
            $('#p_name').tooltip().attr('data-original-title', '請輸入推文標題');
            $('#p_name').tooltip("show");
            _form_status = false;
        } else if (FormDataEmptyTooltip(createForm.cp_id)) {
            $('#cp_id').tooltip().attr('data-original-title', '請選擇推文分類');
            $('#cp_id').tooltip("show");
            _form_status = false;
        } else if (FormDataEmptyTooltip(createForm.p_content)) {
            $('#cp_id').tooltip("destroy");
            $('#p_content').tooltip("show");
            _form_status = false;
        } else if (FormDataEmptyTooltip(createForm.p_send_status)) {
            _form_status = false;
        } else {
            _form_status = true;
        }
    } else if (_lp_type == 2) {
        $('#lp_type').tooltip("hide");
        if (FormDataEmptyTooltip(createForm.p_name)) {
            $('#p_name').tooltip().attr('data-original-title', '請輸入推文標題');
            $('#p_name').tooltip("show");
            _form_status = false;
        } else if (FormDataEmptyTooltip(createForm.cp_id)) {
            $('#cp_id').tooltip().attr('data-original-title', '請選擇推文分類');
            $('#cp_id').tooltip("show");
            _form_status = false;
        } else if (FormDataEmptyTooltip(createForm.p_url)) {
            $('#p_url').tooltip().attr('data-original-title', '請輸入推文連結');
            $('#p_url').tooltip("show");
            _form_status = false;
        } else if (FormDataEmptyTooltip(createForm.p_img)) {
            $('#sh_img').tooltip().attr('data-original-title', '請上傳一張圖片');
            $('#sh_img').tooltip("show");
            _form_status = false;
        } else if (FormDataEmptyTooltip(createForm.p_send_status)) {
            _form_status = false;
        } else if ($('#uploadFileMsg').text() != "") {
            alert("請選擇正確的檔案格式(jpg/png/gif)");
            _form_status = false;
        } else if (FormDataEmptyTooltip(createForm.p_send_status)) {
            _form_status = false;
        } else {
            _form_status = true;
        }
    } else if (_lp_type == 3) {
        $('#lp_type').tooltip("hide");
        if (FormDataEmptyTooltip(createForm.p_name)) {
            $('#p_name').tooltip().attr('data-original-title', '請輸入推文標題');
            $('#p_name').tooltip("show");
            _form_status = false;
        } else if (FormDataEmptyTooltip(createForm.cp_id)) {
            $('#cp_id').tooltip().attr('data-original-title', '請選擇推文分類');
            $('#cp_id').tooltip("show");
            _form_status = false;
        } else if (FormDataEmptyTooltip(createForm.p_content)) {
            $('#cp_id').tooltip("destroy");
            $('#p_content').tooltip("show");
            _form_status = false;
        } else if (FormDataEmptyTooltip(createForm.p_img)) {
            $('#sh_img').tooltip().attr('data-original-title', '請上傳一張圖片');
            $('#sh_img').tooltip("show");
            _form_status = false;
        } else if (FormDataEmptyTooltip(createForm.p_send_status)) {
            _form_status = false;
        } else if ($('#uploadFileMsg').text() != "") {
            alert("請選擇正確的檔案格式(jpg/png/gif)");
            _form_status = false;
        } else {
            _form_status = true;
        }
    } else {
        $('#lp_type').tooltip().attr('data-original-title', '請選擇類型');
        $('#lp_type').tooltip("show");
        _form_status = false;
    }

    if (_form_status) {
        if (_send_status == '2') {
            if (FormDataEmptyTooltip(createForm.p_send_date)) {
                $('#p_send_date').tooltip().attr('data-original-title', '請選擇發送日期');
                $('#p_send_date').tooltip("show");
            } else {
                $("#p_img").show();
                $("#Save").attr('disabled', true);
                createForm.submit();
            }
        } else {
            $("#p_img").show();
            $("#Save").attr('disabled', true);
            createForm.submit();
        }
    }
}

jQuery(document).ready(function () {
    $('#type2').hide();
    /*radio*/
    $('input[type=radio][name=cm_type]').change(function () {
        if (this.value == '1') {
            $('#type1').show();
            $('#type2').hide();
        } else if (this.value == '2') {
            $('#type1').hide();
            $('#type2').show();
        }
    });
    /*img*/
    $(document).on('change', '#cm_img', function () {
        $("#ischange_cm_img").val("Y");
        $('#cm_img_tooltip').tooltip().attr('data-original-title', '');
        uploadFilePreviewIMG(this);
    });
    $(document).on('click', '#uploadFileDeleteButton', function () {
        $("#ischange_cm_img").val("Y");
        $('#uploadFileChooseButton').html("<input type='file' name='cm_img' id='cm_img' style='display:none;'></input>選擇檔案");
        $('#uploadFileDelete').html("");
        $('#uploadFilePreview').html("");
        $('#uploadFileMsg').text("");
        $("#cm_img_tooltip").tooltip("destroy");
    });
});

function chkFormField(createForm) {
    var cm_type = $('input[type=radio][name=cm_type]:checked').val();

    if (FormDataEmptyTooltip(createForm.cm_name)) {
        $('#cm_name').tooltip().attr('data-original-title', '請輸入商品名稱');
        $('#cm_name').tooltip("show");
    } else if (FormDataEmptyTooltip(createForm.cm_date_range)) {
        $('#cm_date_range').tooltip().attr('data-original-title', '請選擇上架時間 - 下架時間');
        $('#cm_date_range').tooltip("show");
    } else if (FormDataEmptyTooltip(createForm.cc_id)) {
        $('#cc_id').tooltip().attr('data-original-title', '請選擇商品分類');
        $('#cc_id').tooltip("show");
    } else if (FormDataEmptyTooltip(createForm.cc_id)) {
        $('#cm_price').tooltip().attr('data-original-title', '請選擇商品價格');
        $('#cm_price').tooltip("show");
    } else if (FormDataEmptyTooltip(createForm.cc_id)) {
        $('#cm_intro').tooltip().attr('data-original-title', '請輸入商品介紹');
        $('#cm_intro').tooltip("show");
    } else if (FormDataEmptyTooltip(createForm.cm_img)) {
        $('#cm_img_tooltip').tooltip().attr('data-original-title', '請上傳一張圖片');
        $('#cm_img_tooltip').tooltip("show");
    } else if ($('#uploadFileMsg').text() != "") {
        alert("請選擇正確的檔案格式(jpg/png/gif)");
    } else {
        if (cm_type == '1') {
            if (FormDataEmptyTooltip(createForm.cm_shipping_fee)) {
                $('#cm_shipping_fee').tooltip().attr('data-original-title', '請輸入商品運費');
                $('#cm_shipping_fee').tooltip("show");
            } else if (FormDataEmptyTooltip(createForm.cm_max_buy)) {
                $('#cm_max_buy').tooltip().attr('data-original-title', '請輸入最大購買數量');
                $('#cm_max_buy').tooltip("show");
            } else if (FormDataEmptyTooltip(createForm.cm_min_buy)) {
                $('#cm_min_buy').tooltip().attr('data-original-title', '請輸入最小購買數量');
                $('#cm_min_buy').tooltip("show");
            } else {
                $("#cm_img").show();
                $("#Save").attr('disabled', true);
                createForm.submit();
            }
        } else if (cm_type == '2') {
            if (FormDataEmptyTooltip(createForm.cm_url)) {
                $('#cm_url').tooltip().attr('data-original-title', '請輸入商品連結');
                $('#cm_url').tooltip("show");
            } else {
                $("#cm_img").show();
                $("#Save").attr('disabled', true);
                createForm.submit();
            }
        }
    }
}
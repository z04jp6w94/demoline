
jQuery(document).ready(function () {

    //初始狀態
    InitView();

    /*img*/
    $(document).on('change', '.upload', function () {
        uploadFilePreviewIMGS(this);
    });
    $(document).on('click', '.uploadFileDeleteButton', function () {
        var number = '';
        number = this.id.replace(/uploadFileDeleteButton/, "");
//        $("#ischange_p_img").val("Y");
        $('#uploadFileChooseButton' + number).html("<input type='file' name='lrcm_img[]' id='lrcm_img" + number + "' value='' class='upload' style='display:none;'></input>選擇檔案");
        $('#uploadFileDelete' + number).html("");
        $('#uploadFilePreview' + number).html("");
        $('#uploadFileMsg' + number).text("");
    });

    $(document).on('click', '.action_type', function () {
        var at_type = '';
        var at_number = '';
        at_number = this.id.replace(/lrcm_action_type/, "");
        at_type = this.value;
        if (at_type == '1') {
            $("#title" + at_number).show();
            $("#content" + at_number).show();
            $("#keyword" + at_number).show();
            $("#url" + at_number).hide();
            $("#lrcm_url" + at_number).val("");
            $("#commodity" + at_number).hide();
            $("#img_" + at_number).show();
//            $('#lrcm_img' + at_number).addClass("need");
        } else if (at_type == '2') {
            $("#title" + at_number).show();
            $("#content" + at_number).show();
            $("#keyword" + at_number).hide();
            $("#url" + at_number).show();
            $("#commodity" + at_number).hide();
            $("#img_" + at_number).show();
//            $('#lrcm_img' + at_number).addClass("need");
        } else if (at_type == '3') {
            $("#title" + at_number).show();
            $("#content" + at_number).show();
            $("#keyword" + at_number).hide();
            $("#url" + at_number).hide();
            $("#lrcm_url" + at_number).val("");
            $("#commodity" + at_number).hide();
            $("#img_" + at_number).show();
//            $('#lrcm_img' + at_number).addClass("need");
        } else if (at_type == '4') {
            $("#title" + at_number).hide();
            $("#content" + at_number).hide();
            $("#keyword" + at_number).hide();
            $("#url" + at_number).hide();
            $("#lrcm_url" + at_number).val("");
            $("#commodity" + at_number).show();
            $("#img_" + at_number).hide();
//            $('#lrcm_img' + at_number).removeClass("need");
            $("#img_" + at_number).tooltip("destroy");
        }
    });

    $('#lrcm_type').change(function () {
        $('#menu_content').html('');
        $('#change_lrcm_type').val("Y");
        var _lrcm_type = this.value;
        $.post(
                'LINERichMenuContent_Ajax.php',
                {lrcm_type: _lrcm_type},
                function (data) {
                    $('#menu_content').html(data);
                    $('.e1').select2();
                    if (_lrcm_type == '2') {
                        $('#img_1').tooltip().attr('data-original-title', '建議使用寬高比為 => 1.51 : 1 圖片，避免發送至用戶端變形');
                        $("#img_1").tooltip("show");
                    } else if (_lrcm_type == '3') {
                        $('#img_1').tooltip().attr('data-original-title', '建議使用長寬相同之圖片，避免發送至用戶端變形');
                        $("#img_1").tooltip("show");
                    }
                }
        );
        if (_lrcm_type == '2') {
            var html = '';
            html += '<a id="add-row-btn" onclick="AddCarousel();" style="float:right;" class="btn btn-default btn-sm tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="">新增一組訊息</a>';
            html += '<hr class="full-width" />';
            $('#AddButton').append(html);
            $('#count_lrcm_img').val('1');
            $('#count_carousel').val('1');
            $('#del_text').val('');
            $('#del_lrct_id').val('');
        } else {
            $('#AddButton').html('');
        }
    });
});

function InitView() {
    //初始狀態
    var dataKey = $('#dataKey').val();
    var _type = $('#temp_lrcm_type').val();
    $.post(
            'LINERichMenuContent_Data.php',
            {lrcm_type: _type, dataKey: dataKey},
            function (data) {
                $('#menu_content').html(data);
                $('.e1').select2();
            }
    );
    if (_type == '2') {
        var html = '';
        html += '<a id="add-row-btn" onclick="AddCarousel();" style="float:right;" class="btn btn-default btn-sm tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="">新增一組訊息</a>';
        html += '<hr class="full-width" />';
        $('#AddButton').append(html);
    } else {
        $('#AddButton').html('');
    }
}

function ValidateValue(textbox) {
    var IllegalString = "[`~!!@%_#$^&*()=|{}':;',\\[\\].<>/?~！#￥……&*（）——|{}【】‘；：”“'。，、？]‘'";
    var textboxvalue = textbox.value;
    var index = textboxvalue.length - 1;

    var s = textbox.value.charAt(index);

    if (IllegalString.indexOf(s) >= 0) {
        s = textboxvalue.substring(0, index);
        textbox.value = s;
    }
}

function AddCarousel() {
    $('#add-row-btn').tooltip("destroy");
    var num_carousel = $('#count_carousel').val();
    num_carousel = parseInt(num_carousel) + 1;
    var num = $('#count_lrcm_img').val();
    num = parseInt(num) + 1;
    $.post(
            'LINERichMenuContent_Ajax.php',
            {lrcm_type: 2, number: num, number_carousel: num_carousel},
            function (data) {
                $('#menu_content').append(data);
                $('.e1').select2();
                $("#img_" + num).tooltip().attr('data-original-title', '建議使用寬高比為 => 1.51 : 1 圖片，避免發送至用戶端變形');
                $("#img_" + num).tooltip("show");

                $('#count_lrcm_img').val(num);
                $('#count_carousel').val(num_carousel);
            }
    );
    //$('#count_lrcm_img').val(num);
    //$('#count_carousel').val(num_carousel);
}

function DeleteCarousel(value, lrct_id) {
    var num_carousel = $('#count_carousel').val();
    num_carousel = parseInt(num_carousel) - 1;
    $('#count_carousel').val(num_carousel);
    $('#carousel_' + value).remove();
    if ($('#del_text').val() === '') {
        $('#del_text').val(value);
    } else {
        $('#del_text').val($('#del_text').val() + ',' + value);
    }
    /* 刪除原始麻將牌資料記錄 */
    if (lrct_id != "") {
        if ($('#del_lrct_id').val() === '') {
            $('#del_lrct_id').val(lrct_id);
        } else {
            $('#del_lrct_id').val($('#del_lrct_id').val() + ',' + lrct_id);
        }
    }
}

function chkFormField(updateForm) {
    var _lrcm_type = $('#lrcm_type').val();
    var _form_status = false;

    if (_lrcm_type == 1) {
        $('#lrcm_type').tooltip("hide");
        if (FormDataEmptyTooltip(updateForm.lrcm_keyword)) {
            $('#lrcm_keyword').tooltip().attr('data-original-title', '請輸入關鍵字');
            $('#lrcm_keyword').tooltip("show");
            $('#lrcm_keyword').focus();
            _form_status = false;
        } else if (FormDataEmptyTooltip(updateForm.lrcm_title)) {
            $('#lrcm_title').tooltip().attr('data-original-title', '請輸入主旨');
            $('#lrcm_title').tooltip("show");
            $('#lrcm_title').focus();
            _form_status = false;
        } else if (FormDataEmptyTooltip(updateForm.lrcm_content)) {
            $('#lrcm_content').tooltip().attr('data-original-title', '請輸入內容');
            $('#lrcm_content').tooltip("show");
            $('#lrcm_content').focus();
            _form_status = false;
        } else {
            _form_status = true;
        }
    } else if (_lrcm_type == 2) {
        $('#lrcm_type').tooltip("hide");
        if (FormDataEmptyTooltip(updateForm.lrcm_keyword)) {
            $('#lrcm_keyword').tooltip().attr('data-original-title', '請輸入關鍵字');
            $('#lrcm_keyword').tooltip("show");
            $('#lrcm_keyword').focus();
            _form_status = false;
        } else {
            $('#lrcm_keyword').tooltip("hide");
            _form_status = true;
        }
        if (_form_status) {
            var _count_carousel = $('#count_carousel').val();
            if (_count_carousel == 0) {
                $('#add-row-btn').tooltip().attr('data-original-title', '請新增至少一組訊息');
                $('#add-row-btn').tooltip("show");
                _form_status = false;
            } else {
                _form_status = true;
            }
            /* SELECT KEY */
            if (_form_status) {
                $("input:radio.action_type:checked").each(function () {
                    var number;
                    number = this.id.replace(/lrcm_action_type/, "");
                    if ($(this).val() == '1') {
                        if ($('#lrcm_title' + number).val() == '') {
                            $('#lrcm_title' + number).tooltip().attr('data-original-title', '請輸入主旨');
                            $('#lrcm_title' + number).tooltip("show");
                            _form_status = false;
                            return false;
                        } else if ($('#lrcm_content' + number).val() == '') {
                            $('#lrcm_content' + number).tooltip().attr('data-original-title', '請輸入內容');
                            $('#lrcm_content' + number).tooltip("show");
                            _form_status = false;
                            return false;
                        } else if ($('#key_id' + number).val() == '') {
                            $('#key_id' + number).tooltip().attr('data-original-title', '請選擇關鍵字');
                            $('#key_id' + number).tooltip("show");
                            _form_status = false;
                            return false;
                        } else {
                            $('#key_id' + number).tooltip("hide");
                            _form_status = true;
                        }
                    } else if ($(this).val() == '2') {
                        if ($('#lrcm_title' + number).val() == '') {
                            $('#lrcm_title' + number).tooltip().attr('data-original-title', '請輸入主旨');
                            $('#lrcm_title' + number).tooltip("show");
                            _form_status = false;
                            return false;
                        } else if ($('#lrcm_content' + number).val() == '') {
                            $('#lrcm_content' + number).tooltip().attr('data-original-title', '請輸入內容');
                            $('#lrcm_content' + number).tooltip("show");
                            _form_status = false;
                            return false;
                        } else if ($('#lrcm_url' + number).val() == '') {
                            $('#lrcm_url' + number).tooltip().attr('data-original-title', '請輸入超連結');
                            $('#lrcm_url' + number).tooltip("show");
                            _form_status = false;
                            return false;
                        } else {
                            $('#lrcm_url' + number).tooltip("destroy");
                            _form_status = true;
                        }
                    } else if ($(this).val() == '4') {
                        if ($('#cm_id' + number).val() == '') {
                            $('#cm_id' + number).tooltip().attr('data-original-title', '請選擇商品');
                            $('#cm_id' + number).tooltip("show");
                            _form_status = false;
                            return false;
                        } else {
                            $('#cm_id' + number).tooltip("destroy");
                            _form_status = true;
                        }
                    }
                });
            }
            /* SELECT KEY IMG */
            if (_form_status) {
                $("input:radio.action_type:checked").each(function () {
                    var number;
                    number = this.id.replace(/lrcm_action_type/, "");
                    var img_info = $('#lrcm_img' + number);
                    var img_value = img_info.val();
                    var img_file_value = img_info.attr("value");
                    if ($(this).val() == '1') {
                        if (img_value == '' && img_file_value == '') {
                            $('#img_' + number).tooltip().attr('data-original-title', '請上傳圖片');
                            $('#img_' + number).tooltip("show");
                            $('#img_' + number).focus();
                            _form_status = false;
                            return false;
                        } else {
                            $('#img_' + number).tooltip("destroy");
                            _form_status = true;
                        }
                    } else if ($(this).val() == '2') {
                        if (img_value == '' && img_file_value == '') {
                            $('#img_' + number).tooltip().attr('data-original-title', '請上傳圖片');
                            $('#img_' + number).tooltip("show");
                            $('#img_' + number).focus();
                            _form_status = false;
                            return false;
                        } else {
                            $('#img_' + number).tooltip("destroy");
                            _form_status = true;
                        }
                    }
                });
            }
            if (_form_status) {
                $('input[name="lrct_sort[]"]').each(function () {
                    if ($(this).val() == '') {
                        $(this).tooltip().attr('data-original-title', '請輸入排序數字');
                        $(this).tooltip("show");
                        $(this).focus();
                        _form_status = false;
                        return false;
                    } else {
                        $(this).tooltip("hide");
                        _form_status = true;
                    }
                });
            }
        }
    } else if (_lrcm_type == 3) {
        $('#lrcm_type').tooltip("hide");
        if (FormDataEmptyTooltip(updateForm.lrcm_keyword)) {
            $('#lrcm_keyword').tooltip().attr('data-original-title', '請輸入關鍵字');
            $('#lrcm_keyword').tooltip("show");
            $('#lrcm_keyword').focus();
            _form_status = false;
        } else {
            $('#lrcm_title').tooltip("hide");
            _form_status = true;
        }
        if (_form_status) {
            var radio_check = $("input:radio.action_type:checked");
            var img_info = $('input[name="lrcm_img[]"]');
            var img_value = img_info.attr("value");
            if (radio_check.val() == '1') {
                if (FormDataEmptyTooltip(updateForm.lrcm_title)) {
                    $('#lrcm_title').tooltip().attr('data-original-title', '請輸入主旨');
                    $('#lrcm_title').tooltip("show");
                    $('#lrcm_title').focus();
                    _form_status = false;
                } else if ($('#key_id1').val() == '') {
                    $('#key_id1').tooltip().attr('data-original-title', '請選擇關鍵字');
                    $('#key_id1').tooltip("show");
                    _form_status = false;
                } else if (img_info.val() == '' && img_value == '') {
                    $('#img_1').tooltip().attr('data-original-title', '請上傳圖片');
                    $('#img_1').tooltip("show");
                    $('#img_1').focus();
                    _form_status = false;
                } else {
                    $('#img_1').tooltip("destroy");
                    $('#key_id1').tooltip("destroy");
                    _form_status = true;
                }
            } else if (radio_check.val() == '2') {
                if (FormDataEmptyTooltip(updateForm.lrcm_title)) {
                    $('#lrcm_title').tooltip().attr('data-original-title', '請輸入主旨');
                    $('#lrcm_title').tooltip("show");
                    $('#lrcm_title').focus();
                    _form_status = false;
                } else if ($('#lrcm_url1').val() == '') {
                    $('#lrcm_url1').tooltip().attr('data-original-title', '請輸入超連結');
                    $('#lrcm_url1').tooltip("show");
                    _form_status = false;
                } else if (img_info.val() == '' && img_value == '') {
                    $('#img_1').tooltip().attr('data-original-title', '請上傳圖片');
                    $('#img_1').tooltip("show");
                    $('#img_1').focus();
                    _form_status = false;
                } else {
                    $('#img_1').tooltip("destroy");
                    $('#lrcm_url1').tooltip("destroy");
                    _form_status = true;
                }
            } else if (radio_check.val() == '3') {
                if (FormDataEmptyTooltip(updateForm.lrcm_title)) {
                    $('#lrcm_title').tooltip().attr('data-original-title', '請輸入主旨');
                    $('#lrcm_title').tooltip("show");
                    $('#lrcm_title').focus();
                    _form_status = false;
                } else if (img_info.val() == '' && img_value == '') {
                    $('#img_1').tooltip().attr('data-original-title', '請上傳圖片');
                    $('#img_1').tooltip("show");
                    $('#img_1').focus();
                    _form_status = false;
                } else {
                    $('#img_1').tooltip("destroy");
                    _form_status = true;
                }
            } else if (radio_check.val() == '4') {
                if ($('#cm_id1').val() == '') {
                    $('#cm_id1').tooltip().attr('data-original-title', '請選擇商品');
                    $('#cm_id1').tooltip("show");
                    _form_status = false;
                } else {
                    $('#cm_id_1').tooltip("destroy");
                    _form_status = true;
                }
            }
        }
    } else {
        $('#lrcm_type').tooltip().attr('data-original-title', '請選擇類型');
        $('#lrcm_type').tooltip("show");
        _form_status = false;
    }
    if (_form_status) {
        if (confirm('提醒您!!一但更改後，先前設定資料將會清除，是否確定要修改？')) {
            $("#Save").attr('disabled', true);
            updateForm.submit();
        }
    } else {
        $('#Save').tooltip().attr('data-original-title', '尚有資料未填寫！');
    }
}
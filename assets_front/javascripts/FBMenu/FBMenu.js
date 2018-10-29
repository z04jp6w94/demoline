
jQuery(document).ready(function () {
    /*img*/
    $(document).on('change', '#rs_img', function () {
        $("#ischange_rs_img").val("Y");
        uploadFileLineRichMenuIMG(this);
    });
    $(document).on('click', '#uploadFileDeleteButton', function () {
        $("#ischange_rs_img").val("Y");
        $('#uploadFileChooseButton').html("<input type='file' name='rs_img' id='rs_img' style='display:none;' required></input>選擇檔案");
        $('#uploadFileDelete').html("");
        $('#uploadFilePreview').html("");
        $('#uploadFileMsg').text("請上傳寬度: 2500px 高度: 1686px圖檔");
    });

    $('.menu_type').change(function () {
        $('#menu_setting').html('');
        $('#menu_content').html('');
        var num = this.value;
        $.post(
                'LineMenu_Ajax.php',
                {menu_type: num},
                function (data) {
                    var obj = JSON.parse(data);
                    $('#menu_setting').html(obj.str1);
                    $('#menu_content').html(obj.str2);
                    $('.e1').select2();
                }
        );
    });

    $(document).on('click', '.btn-input', function () {
        /* color */
        var parent_div = $(this).parents('div').attr('class');
        $("." + parent_div).each(function () {
            $(this).children('ul').find('li').css('background-color', '#D3D3D3');
        });
        $(this).parents('li').css('background-color', '#FFC0CB');
        /* radio */
        var choose_val = $(this).attr('value');
        /*$(".col-xs-8").each(function () {
            $(this).hide();
        });*/
        $("#radio_choose_" + choose_val).show();
    });

    $(document).on('click', '.radio_check', function () {
        var check_id = '';
        var check_value = '';
        check_id = this.id.replace(/rst_type_/, "");/* rst_type_1 */
        check_value = this.value;
        $(".input_" + check_id).each(function () {
            $(this).hide();
        });
        if (check_value == '1') {
            $("#input_" + check_id + "_" + check_value).show();
        } else if (check_value == '2') {
            $("#input_" + check_id + "_" + check_value).show();
        } else if (check_value == '3') {
            $(".input_" + check_id).each(function () {
                $(this).hide();
            });
        }
    });

});

function SelectLength(Name) {
    var check_length = document.getElementsByName(Name);
    var count_checked = 0;

    for (var i = 0; i < check_length.length; i++) {
        if (document.getElementsByName(Name)[i].value == "") {
            count_checked++;
        }
    }
    return count_checked;
}

function setKey(value) {
    createForm.useValue.value = value;
}

function chkFormField(createForm) {

    var check_length_lrcm_keyword = SelectLength('lrcm_keyword[]');

    if (FormDataEmptyTooltip(createForm.rs_title)) {
        $('#rs_title').tooltip().attr('data-original-title', '請填寫Menu標題');
        $('#rs_title').tooltip("show");
        setKey('');
    } else if (FormDataEmptyTooltip(createForm.rs_img)) {
        $('#tip_rs_img').focus();
        $("#rs_title").tooltip("destroy");
        $('#tip_rs_img').tooltip().attr('data-original-title', '請上傳圖片');
        $('#tip_rs_img').tooltip("show");
        setKey('');
    } else if (check_length_lrcm_keyword != 0) {
        $('#menu_setting').focus();
        $("#tip_rs_img").tooltip("destroy");
        $('#menu_setting').tooltip().attr('data-original-title', '請確認關鍵字是否選取');
        $('#menu_setting').tooltip("show");
        setKey('');
    } else {
        var img_status = $('#rs_img_status').val();
        if (img_status == 'Y') {
            $("#menu_setting").tooltip("destroy");
            $("#rs_img").show();
            $("#Save").attr('disabled', true);
            $("#SaveAndUse").attr('disabled', true);
            createForm.submit();
        } else {
            setKey('');
            $('#tip_rs_img').tooltip().attr('data-original-title', '請上傳正確格式的圖片');
            $('#tip_rs_img').tooltip("show");
        }
    }

}
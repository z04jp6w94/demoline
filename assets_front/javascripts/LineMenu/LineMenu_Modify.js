
jQuery(document).ready(function () {

    $(document).on('change', '#rs_img', function () {
        $("#ischange_rs_img").val("Y");
        uploadFileLineRichMenuIMG(this);
    });

    $(document).on('click', '#uploadFileDeleteButton', function () {
        $("#ischange_rs_img").val("Y");
        $('#uploadFileChooseButton').html("<input type='file' name='rs_img' id='rs_img' style='display:none;' required></input>選擇檔案");
        $('#uploadFileDelete').html("");
        $('#uploadFilePreview').html("");
        $('#uploadFileMsg').text("");
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
        $(".col-xs-8").each(function () {
            $(this).hide();
        });
        $("#radio_choose_" + choose_val).show();
    });

    $('#menu_setting').html('');
    $('#menu_content').html('');
    var key = $('#dataKey').val();
    var num = $('#rsm_type').val();
    $.post(
            'LINERichMenu_ViewContent.php',
            {dataKey: key, menu_type: num},
            function (data) {
                var obj = JSON.parse(data);
                $('#menu_setting').html(obj.str1);
                $('#menu_content').html(obj.str2);
                $('.e1').select2();
            }
    );
});
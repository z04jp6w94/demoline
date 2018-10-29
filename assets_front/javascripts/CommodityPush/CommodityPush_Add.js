
jQuery(document).ready(function () {

    $('#line_push_type').change(function () {
        var num = this.value;
        if (num == "1") {
            $("input[name='cm_id[]']").each(function () {
                $(this).attr('onclick', 'SingleCheck(this);');
            });
            $("#line_push_type").tooltip("destroy");
        } else if (num == "2") {
            $("input[name='cm_id[]']").each(function () {
                $(this).attr('onclick', 'SingleCheck(this);');
            });
            $("#line_push_type").tooltip("destroy");
        } else if (num == "3") {
            $("input[name='cm_id[]']").each(function () {
                $(this).attr('onclick', 'DefaultCheck();');
            });
            $("#line_push_type").tooltip("destroy");
        }
    });

});

function DefaultCheck() {
    var _line_push_type = $('#line_push_type').val();
    if (_line_push_type == "") {
        $('#line_push_type').tooltip().attr('data-original-title', '請選擇類型');
        $('#line_push_type').tooltip("show");
        $("input[name='cm_id[]']").each(function () {
            $(this).prop("checked", false);
        });
    }
}

function SingleCheck(checkbox) {
    $("input[name='cm_id[]']").each(function () {
        $(this).prop("checked", false);
    });
    $(checkbox).prop('checked', true);
}

function CheckLength(Name) {
    var check_length = document.getElementsByName(Name);
    var count_checked = 0;

    for (var i = 0; i < check_length.length; i++) {
        if (check_length[i].checked) {
            count_checked++;
        }
    }
    return count_checked;
}

function chkFormField(createForm) {
    var _line_push_type = $('#line_push_type').val();
    if (_line_push_type != "") {
        var check_length_oa = CheckLength('OA');
        var check_length_ct = CheckLength('ct_id[]');
        var check_length_cm = CheckLength('cm_id[]');
        if (check_length_oa == 0) {
            if (check_length_ct == 0) {
                $('#ct_tips').tooltip().attr('data-original-title', '請至少勾選一個發送目標');
                $('#ct_tips').tooltip("show");
            } else if (check_length_cm == 0) {
                $('#cm_tips').tooltip().attr('data-original-title', '請至少勾選一個商品');
                $('#cm_tips').tooltip("show");
            } else {
                $("#Save").attr('disabled', true);
                createForm.submit();
            }
        } else {
            if (check_length_cm == 0) {
                $('#cm_tips').tooltip().attr('data-original-title', '請至少勾選一個商品');
                $('#cm_tips').tooltip("show");
            } else {
                $("#Save").attr('disabled', true);
                createForm.submit();
            }
        }
    } else {
        $('#line_push_type').tooltip().attr('data-original-title', '請選擇類型');
        $('#line_push_type').tooltip("show");
    }
}
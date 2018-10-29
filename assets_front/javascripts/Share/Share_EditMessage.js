
jQuery(document).ready(function () {

});

function chkFormField(updateForm) {

    if (FormDataEmptyTooltip(updateForm.send_message)) {
        $('#send_message').tooltip().attr('data-original-title', '請輸入發送訊息');
        $('#send_message').tooltip("show");
    } else {
        $("#Save").attr('disabled', true);
        $.post(
                'Share_SendMessage.php',
                $("#updateForm").serialize(),
                function (data) {
                    
                }
        );
        updateForm.submit();
    }

}
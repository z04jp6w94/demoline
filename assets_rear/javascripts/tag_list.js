$(document).ready(function () {
    var isReturn = true;
    $("body").click(function () {
        if (isReturn) {
            $('.table1div5').hide();
            $('.table1div6').hide();
        } else {
            isReturn = true;
        }
    });
    $(".table6 tr").mouseover(function () {
        $(this).css('background-color', '#D4E9F1');
        $('#ckeditorSpan').css('visibility', 'visible');
    }).mouseout(function () {
        $(this).css('background-color', '#E2F5FC');
        $('#ckeditorSpan').css('visibility', 'hidden');
    });
    $("#ckeditorSpan a").click(function () {
        if ($(this).attr("status") === "off") {
            $(this).attr("status", "on");
            $('.table3').css("display", "block");
        } else {
            $(this).attr("status", "off");
            $('.table3').css("display", "none");
        }
    });
    $(".licss").click(function () {
        $(".ulcss li").removeClass("licssselect");
        $(this).addClass("licssselect");
        var type = $(this).attr("type");
        $(".divexternalcss div").removeClass("divinternalcssselect");
        $(".divcss" + type).addClass("divinternalcssselect");
    });
    $(".table1div4").mouseover(function () {
        $(this).css('border-width', '1.2px');
        $(this).css('border-color', '#5599FF');
    }).mouseout(function () {
        $(this).css('border-width', '1px');
        $(this).css('border-color', '#CCCCCC');
    }).click(function () {
        if ($('.table1div5').css("display") === "block") {
            $('.table1div5').hide();
        } else {
            isReturn = false;
            $('.table1div5').show();
        }
        if ($('.table1div6').css("display") == "block") {
            $('.table1div6').hide();
        } else {
            isReturn = false;
            $('.table1div6').show();
        }
    })
    $(".table1div5").click(function () {
        isReturn = false;
    });
    $(".table1div6").click(function () {
        isReturn = false;
    });
});
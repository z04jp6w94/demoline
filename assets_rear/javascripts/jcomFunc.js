// JavaScript Document

/*
 
 本comFunc需搭配jQuery使用
 
 搭配版本：1.3.2
 
 */





/*
 
 功能說明：動態Select元素填值
 
 開發人員：peter
 
 開發日期：2009/09/01
 
 */



//移除指定下拉選單中的值

function RemoveOption(wSel) {

    for (var j = wSel.options.length - 1; j >= 0; j--) {

        wSel.options[j] = null;

    }

}



//插入選項到下拉選單中

function AddOption(wSel, optNM, optValue) {

    wSel.options[wSel.options.length] = new Option(optNM, optValue);

}



function FillDataToSelect(wSel, _xmlObj, Clear) {

    var items = _xmlObj.getElementsByTagName('option');

    if (Clear) {

        RemoveOption(wSel);

        AddOption(wSel, '選擇...', '');

    }

    for (var j = 0; j < items.length; j++) {

        //alert(items[j].text +','+ items[j].getAttribute('value'))

        AddOption(wSel, items[j].text, items[j].getAttribute('value'));

    }

}



function getRelativeOption(mSel, sSel, DataSrc, para, keep1stOpt, defaultOptText) {


    //取得相關選項

    //主要用在兩個關聯性的Select上，如縣市與區域...

    var omSel = $(mSel);

    var osSel = $('#' + sSel);

    if (DataSrc == '') {
        alert('缺少資料來源');
        return false;
    }

    if (omSel.length > 0 && osSel.length > 0) {

        var p = new Object();

        for (var x in para) {

            p[x] = para[x];

        }

        p.mcode = omSel.val();

        $.post(
                DataSrc,
                p,
                function (xml) {

                    if ($('resu', xml).text() == "1") {

                        $('option' + (keep1stOpt ? ':gt(0)' : ''), osSel).remove();

                        if ($('option:first', osSel).length > 0) {

                            //$('option:first',osSel).text((defaultOptText==''?'請選擇':defaultOptText));

                        } else {

                            osSel.append('<option value="">' + (defaultOptText == '' ? '請選擇' : defaultOptText) + '</option>');

                        }

                        $('data', xml).each(function () {

                            osSel.append('<option value="' + $('code', this).text() + '">' + $('desc', this).text() + '</option>');

                        });

                        //if($('option',osSel).length==2) $('option:last',osSel).attr('selected','selected');

                        //osSel.change();

                    } else {

                        //alert($('msg',xml).text());

                    }

                }

        );

    } else {

        alert('未找到相依的兩個select');

    }

}

function getRelativeRadio(mSel, sSel, DataSrc, para, keep1stOpt, defaultOptText) {


    //取得相關選項

    //主要用在兩個關聯性的Select上，如縣市與區域...

    var omSel = $(mSel);

    var osSel = $('#' + sSel);

    if (DataSrc == '') {
        alert('缺少資料來源');
        return false;
    }

    if (omSel.length > 0 && osSel.length > 0) {

        var p = new Object();

        for (var x in para) {

            p[x] = para[x];

        }

        p.mcode = omSel.val();

        $.post(
                DataSrc,
                p,
                function (xml) {

                    if ($('resu', xml).text() == "1") {

//					   $('option'+(keep1stOpt?':gt(0)':''),osSel).remove();

//					   if($('option:first',osSel).length>0){

                        //$('option:first',osSel).text((defaultOptText==''?'請選擇':defaultOptText));

//				   }else{

//						   osSel.append('<option value="">'+(defaultOptText==''?'請選擇':defaultOptText)+'</option>');

//					   }

                        $('data', xml).each(function () {

                            osSel.append('<input id="ActivityT3_No" name="ActivityT3_No" type="radio" value="' + $('code', this).text() + '">' + $('desc', this).text());

                        });

                        //if($('option',osSel).length==2) $('option:last',osSel).attr('selected','selected');

                        //osSel.change();

                    } else {

                        //alert($('msg',xml).text());

                    }

                }

        );

    } else {

        alert('未找到相依的KEY');

    }

}


function dspDate(wdate) {

    var newstr = '';

    newstr = wdate.substr(0, 4) + '/' + wdate.substr(4, 2) + '/' + wdate.substr(6, 2);

    return newstr;

}



function dspTime(wtime) {

    var newstr = '';

    newstr = wtime.substr(0, 2) + ':' + wtime.substr(2, 2) + ':' + wtime.substr(4, 2);

    return newstr;

}



function StopEvent(pE) {

    //停止事件

    if (!pE) {

        if (window.event) {

            pE = window.event;

        } else {

            return;

        }

    }

    if (pE.cancelBubble != null)
        pE.cancelBubble = true;

    if (pE.stopPropagation)
        pE.stopPropagation();

    if (pE.preventDefault)
        pE.preventDefault();

    if (window.event)
        pE.returnValue = false;

    if (pE.cancel != null)
        pE.cancel = true;

}



//設定焦點

(function ($)

{

    jQuery.fn.setfocus = function ()

    {

        return this.each(function ()

        {

            var dom = this;

            setTimeout(function ()

            {

                try {
                    dom.focus();
                } catch (e) {
                }

            }, 0);

        });

    };

})(jQuery);





function jCheckForm(wContainer) {

    //表單送出前檢查必填欄位

    var val;



    //確認欄位不應設為必填

    $('*[confirm]', wContainer).each(function () {

        $('*[id=' + $(this).attr('confirm') + '][require]', wContainer).removeAttr('require');

    });



    $('*[inErr]', wContainer).removeClass('RequireEmpty').removeAttr('inErr');

    //檢查必填欄位與相關性檢查

    $('*[require]', wContainer).each(function () {

        $(this).removeAttr('inErr');

        switch (this.type) {

            case 'text':

                this.value.replace(' ', '');

                if (this.value != '') {

                    $(this).removeClass('RequireEmpty');

                } else {

                    $(this).addClass('RequireEmpty');

                    $(this).attr('inErr', 'err');

                }

                break;

            case 'password':

                this.value.replace(' ', '');

                if (this.value != '') {

                    $(this).removeClass('RequireEmpty');

                    if ($(this).attr('confirm') != '' && $(this).attr('confirm') != undefined) {

                        var passconfirm = $('#' + $(this).attr('confirm'));

                        passconfirm.next('.checkhint').remove();

                        if (passconfirm.val() != $(this).val()) {

                            passconfirm.after('<span class="checkhint">密碼確認不符，請確認！</span>');

                            $(this).attr('inErr', 'err');

                        }

                    }

                } else {

                    $(this).addClass('RequireEmpty');

                    $(this).attr('inErr', 'err');

                }

                break;

            case 'select-one':

                if (this.value != '') {

                    $(this).removeClass('RequireEmpty');

                } else {

                    $(this).addClass('RequireEmpty');

                    $(this).attr('inErr', 'err');

                }

        }

    });

    //alert($('*[inErr]',wContainer).map(function(){return this.id;}).get().join(', '));

    return ($('*[inErr]', wContainer).length > 0) ? false : true;

}
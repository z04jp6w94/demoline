// JavaScript Document
// 格式化金額
function moneyFormat(num, n) {
    num = String(num.toFixed(n));
    var re = /(-?\d+)(\d{3})/;
    while (re.test(num))
        num = num.replace(re, "$1,$2")
    return num;
}
function AutoSaveTextToInput(InputId, Url) {
    $("." + InputId).hover(function () {
        $(this).css("border", "1px solid #06C");
    }, function () {
        $(this).css("border", "");
    });
    $("." + InputId).click(function () {
        // 預設值
        var _objId = $(this);
        var _oldText = $.trim(_objId.text());													// 紀錄原始文字
        var _input = $('<input type="text" />');									// 產生 input
        var _inputWidth = _objId.width() + 8;													// 欄位寬
        _objId.html(_input);																	// 更新指定區塊為 input
        _input.css("width", _inputWidth);
        _objId.css("border", "");
        // 設定 input 的點擊事件無效
        _input.click(function () {
            return false;
        });
        _input.focus();
        _input.val(_oldText);
        // keydown
        _input.keydown(function () {

        });
        // keyup
        _input.keyup(function () {
            $("#temptext").text($(this).val());
            $(this).css("width", $("#temptext").width() + 8);
        });
        // 設定 input 失去焦點時重新變為文字
        _input.blur(function () {
            var _newText = $(this).val();														// 取得新文字
            _objId.html(_newText);																// 更新指定區塊為文字
            var _DataId = _objId.attr("C08Id");													// 取得更新序號
            //_newText = escape(_newText);														// 將文字轉碼
            //var _url = Url + "?Id=" + _DataId + "&Val=" + _newText;								// 更新程式位置
            //$.get(_url, function(data) {});
            $.post(
                    Url,
                    {Id: _DataId, Val: _newText},
                    function (xml) {
                        parent.AddCC08();
                    }
            );
        });
    });
}

function SetMenunavBtn() {
    var DelType = false
    $("input:checkbox").each(function (i) {
        if ($(this).attr("checked") && $(this).attr("chktype") != 'false') {
            DelType = true
            return false;
        }
    });
    if (DelType) {
        $("#CmBtnDel, #CmBtnSave").show();
    } else {
        $("#CmBtnDel, #CmBtnSave").hide();
    }
}
function ViewWH(PageType) {
    var viewpage;
    var viewportwidth;
    var viewportheight;
    var el = document.getElementById("PageMian");
    switch (PageType) {
        case 'L': //列表頁
            viewpage = 110;
            break;
        case 'M': //新增, 修改頁
            viewpage = 95;
            break;
        default:
            viewpage = 95;
            break;
    }
    // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
    if (typeof window.innerWidth != 'undefined') {
        viewportwidth = window.innerWidth,
                viewportheight = window.innerHeight
    }
    // IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
    else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0) {
        viewportwidth = document.documentElement.clientWidth,
                viewportheight = document.documentElement.clientHeight
    }
    // older versions of IE
    else {
        viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
                viewportheight = document.getElementsByTagName('body')[0].clientHeight
    }
    el.style.height = viewportheight - viewpage + "px";
}

//關於畫面控制的FUNCTION
function convertUpper(obj) {
    obj.value = obj.value.toUpperCase();
}

function sbar(st, pColor) {
    st.style.backgroundColor = pColor;
}

function cbar(st) {
    st.style.backgroundColor = '';
}
function setBgColor(st, pColor) {
    st.style.backgroundColor = pColor;
}

function cleanBgColor(st) {
    st.style.backgroundColor = '';
}
function overFontColor(MyObj, MyColor) {
// onmouseover change font color
    MyObj.style.color = MyColor;
}
function outFontColor(MyObj) {
// onmouseout rechange font color
    MyObj.style.color = '';
}
//分頁用FUNCTION
function ChangePage(pPage) {
    wHref = document.location.href.split('?')[0]
    document.location.href = wHref + "?ToPage=" + pPage;
}
function ChangePageWithCondition(pPage, pCondition) {
    wHref = document.location.href.split('?')[0]
    document.location.href = wHref + "?ToPage=" + pPage + "&" + pCondition;
}

/////前端操作介面function
function DeleteFile() {
//0:顯示回傳值物件名稱 1:檔案名稱 2:檔案刪除路徑 3:上傳按鈕名稱 4:刪除按鈕名稱
    var pAry = DeleteFile.arguments;
    var wStr;
    var wAry = new Array(4);
    wStr = '/UpLoad/DeleteFile.aspx?';
    wAry[0] = 'ObjNM';
    wAry[1] = 'FileNM';
    wAry[2] = 'FilePath';
    wAry[3] = 'UpLoadBtnNM';
    wAry[4] = 'DeleteBtnNM';

    for (var i = 0; i < pAry.length; i++) {
        wStr += wAry[i] + '=' + escape(pAry[i]);
        if (i < pAry.length) {
            wStr += '&'
        }
    }
    OpenWin(wStr, 350, 150, 7, 'UpLoadPic');
}
function DeleteFileAsp() {
//0:顯示回傳值物件名稱 1:檔案名稱 2:檔案刪除路徑 3:上傳按鈕名稱 4:刪除按鈕名稱
    var pAry = DeleteFileAsp.arguments;
    var wStr;
    var wAry = new Array(4);
    wStr = '/UpLoad/DeleteFile.asp?';
    wAry[0] = 'ObjNM';
    wAry[1] = 'FileNM';
    wAry[2] = 'FilePath';
    wAry[3] = 'UpLoadBtnNM';
    wAry[4] = 'DeleteBtnNM';

    for (var i = 0; i < pAry.length; i++) {
        wStr += wAry[i] + '=' + escape(pAry[i]);
        if (i < pAry.length) {
            wStr += '&'
        }
    }
    OpenWin(wStr, 350, 150, 7, 'UpLoadPic');
}

function UpLoadPic() {
//0:顯示回傳值物件名稱 1:檔案上傳路徑 2:上傳按鈕名稱 3:刪除按鈕名稱 4:最大檔案SIZE限制(單位Kbytes) 5:檔案最大長寬 6:是否覆蓋同檔名檔案
    var pAry = UpLoadPic.arguments;
    var wStr;
    var wAry = new Array(6);
    wStr = '/UpLoad/UpLoadPic.aspx?';
    wAry[0] = 'ObjNM';
    wAry[1] = 'FilePath';
    wAry[2] = 'UpLoadBtnNM';
    wAry[3] = 'DeleteBtnNM';
    wAry[4] = 'MaxFileSize';
    wAry[5] = 'MaxSize';
    wAry[6] = 'IsCover';
    for (var i = 0; i < pAry.length; i++) {
        wStr += wAry[i] + '=' + pAry[i];
        if (i < pAry.length) {
            wStr += '&'
        }
    }
    OpenWin(wStr, 350, 180, 7, 'UpLoadPic');
}

function UpLoadFile() {
//0:顯示回傳值物件名稱 1:檔案上傳路徑 2:上傳按鈕名稱 3:刪除按鈕名稱 4:最大檔案SIZE限制(單位Kbytes) 5:可上傳的檔案格式設定 6:是否覆蓋同檔名檔案
    var pAry = UpLoadFile.arguments;
    var wStr;
    var wAry = new Array(6);
    wStr = '/UpLoad/UpLoad.aspx?';
    wAry[0] = 'ObjNM';
    wAry[1] = 'FilePath';
    wAry[2] = 'UpLoadBtnNM';
    wAry[3] = 'DeleteBtnNM';
    wAry[4] = 'MaxFileSize';
    wAry[5] = 'FileType';
    wAry[6] = 'IsCover';
    for (var i = 0; i < pAry.length; i++) {
        wStr += wAry[i] + '=' + pAry[i];
        if (i < pAry.length) {
            wStr += '&'
        }
    }
    OpenWin(wStr, 350, 180, 2, 'UpLoadFile');
    //OpenWin(wStr,350,180,7,'UpLoadFile');
}
function UpLoadFileAsp() {
//0:顯示回傳值物件名稱 1:檔案上傳路徑 2:上傳按鈕名稱 3:刪除按鈕名稱 4:最大檔案SIZE限制(單位Kbytes) 5:可上傳的檔案格式設定 6:是否覆蓋同檔名檔案
    var pAry = UpLoadFileAsp.arguments;
    var wStr;
    var wAry = new Array(6);
    wStr = '/UpLoad/UpLoad.asp?';
    wAry[0] = 'ObjNM';
    wAry[1] = 'FilePath';
    wAry[2] = 'UpLoadBtnNM';
    wAry[3] = 'DeleteBtnNM';
    wAry[4] = 'MaxFileSize';
    wAry[5] = 'FileType';
    wAry[6] = 'IsCover';
    for (var i = 0; i < pAry.length; i++) {
        wStr += wAry[i] + '=' + pAry[i];
        if (i < pAry.length) {
            wStr += '&'
        }
    }
    OpenWin(wStr, 350, 180, 7, 'UpLoadFile');
}
//處理上傳及刪除按鈕的disable狀態,1:檔案名稱,2:上傳按鈕名稱,3:刪除按鈕名稱
function IsUpload(pObj1, pObj2, pObj3) {
    if (pObj1.value == "") {
        pObj2.disabled = false;
        pObj3.disabled = true;
    } else {
        pObj2.disabled = true;
        pObj3.disabled = false;
    }
}

function checkBeforeDelete(pForm) {//刪除前檢查
//pForm 傳入的form物件
//檢查checkbox 是否有選取 
//有選取則詢問是否確定要刪除
//將傳進來的form submit出去
    wFlg = false;
    for (var i = 0; i <= pForm.elements.length - 1; i++) {
        if (pForm.elements[i].type == "checkbox") {
            if (pForm.elements[i].checked == true) {
                wFlg = true;
                break;
            }
        }
    }
    if (wFlg == true) {
        if (confirm('確定要刪除選取的資料嗎？')) {
            pForm.submit();
        }
    } else {
        alert('請先選取要刪除的資料');
    }
}

function checkBeforeSubmit(pForm, pFldName, pMsg) {//前檢查 (包含刪除前檢查)
//pForm 傳入的form物件
//pFldName 要檢查的checkbox物件名稱字串
//檢查checkbox 是否有選取 
//有選取則詢問是否確定要執行
//將傳進來的form submit出去
    wFlg = false;
    for (var i = 0; i <= pForm.elements.length - 1; i++) {
        if (pForm.elements[i].type == "checkbox") {
            if (pForm.elements[i].name.indexOf(pFldName) >= 0) {
                if (pForm.elements[i].checked == true) {
                    wFlg = true;
                    break;
                }
            }
        }
    }
    if (wFlg == true) {
        if (confirm('確定要' + pMsg + '選取的項目嗎?')) {
            pForm.submit();
        }
    } else {
        alert('請先選取要' + pMsg + '的資料');
    }
}

function checkBeforeNextStep(pForm, pFldName, pMsg) {//前檢查 (包含刪除前檢查)
//pForm 傳入的form物件
//pFldName 要檢查的checkbox物件名稱字串
//檢查checkbox 是否有選取 
//有選取則詢問是否確定要執行
    wFlg = false;
    for (var i = 0; i <= pForm.elements.length - 1; i++) {
        if (pForm.elements[i].type == "checkbox") {
            if (pForm.elements[i].name.indexOf(pFldName) >= 0) {
                if (pForm.elements[i].checked == true) {
                    wFlg = true;
                    break;
                }
            }
        }
    }
    if (wFlg == true) {
        if (confirm('確定要' + pMsg + '選取的項目嗎?')) {
            return true;
        }
    } else {
        alert('請先選取要' + pMsg + '的資料');
        return false;
    }
}

function changeSelect(pForm) {//全選全不選   不正常請部要使用本function
//pForm 傳入的form物件
// from 裡面的所有 checkbox 名稱不可相同
//將form中所有的checkbox物件全部設為與第一個checkbox相同選取狀態
    for (var i = 0; i <= pForm.elements.length - 1; i++) {
        if (pForm.elements[i].type == "checkbox") {
            pForm.elements[i].checked = !pForm.elements[i].checked
            changAllCheckbox(pForm, pForm.elements[i].checked);
            break;
        }
    }
}
function changAllCheckbox(pForm, pBoolean) {// 設定checkbox的值
//pForm 傳入的form物件
//pBoolean 傳入的布林值
    if (pBoolean) {
        $("#CmBtnDel, #CmBtnSave").show();
    } else {
        $("#CmBtnDel, #CmBtnSave").hide();
    }
    for (var i = 0; i <= pForm.elements.length - 1; i++) {
        if (pForm.elements[i].type == "checkbox" && pForm.elements[i].disabled == false)
            pForm.elements[i].checked = pBoolean;
    }
}
//////////////////////////////
function changCheckboxH(cCheckNM, pBoolean) {
// 設定checkbox的值，母帶子水平調整
//Peter,950927
//cCheckNM 傳入的要改變的子項目名稱
//pBoolean 傳入的布林值	
    var i
    //alert(cCheckNM.length);
    if (cCheckNM.length == undefined) {
        cCheckNM.checked = pBoolean;
    } else {
        for (i = 0; i < cCheckNM.length; i++) {
            cCheckNM[i].checked = pBoolean;
        }
    }
}
function changCheckboxV(pForm, OName, pBoolean) {// 設定checkbox的值
    //Peter,950927
//pForm 傳入的form物件
//OName 傳入的物件名稱
//pBoolean 傳入的布林值F
    if (pBoolean) {
        $("#CmBtnDel, #CmBtnSave").show();
    } else {
        $("#CmBtnDel, #CmBtnSave").hide();
    }
    for (var i = 0; i <= pForm.elements.length - 1; i++) {
        if (pForm.elements[i].type == "checkbox") {
            if (pForm.elements[i].name.substring(0, OName.length).toUpperCase() == OName.toUpperCase()) {
                if (!pForm.elements[i].disabled)
                    pForm.elements[i].checked = pBoolean;
            }
        }
    }
}
function OpenAutoWin(pPhotoSrc) {//開啟隨圖檔調整大小的視窗
    var mySrc = pPhotoSrc
    var Win = window.open("", "AutoWindow", "width=150,height=100,status=no,resizeable=no");
    Win.document.open();
    Win.document.write("<html>");
    Win.document.write("<head>");
    Win.document.write("<title>看圖視窗-(點圖自動關閉視窗)</title>");
    Win.document.write("</head>");
    Win.document.write('<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="javascript:window.resizeTo(myImg.width+10,myImg.height+30);" onblur="window.close();">');
    Win.document.write('<img src="' + mySrc + '" id="myImg" title="點圖關閉視窗"  onclick="window.close();" alt="圖檔遺失..">');
    Win.document.write("</body>");
    Win.document.write("</html>");
    Win.document.close();
}
function OpenWin(theURL, x, y, MyType, MyWinName) {
//  var x_screen=screen.availWidth;
//  var y_screen=screen.availHeight;
    var x_screen = screen.Width;
    var y_screen = screen.Height;
    var wk_left = (x_screen - x) / 2;
    var wk_top = (y_screen - y) / 2;
    var features;
    switch (MyType) {
        case 0: //固定大小的視窗，無狀態列無網址列無捲軸
            features = "Height=" + y + ",Width=" + x;
            features += ",top=" + wk_top + ",left=" + wk_left;
            features += ",status=no,resizable=no,scrollbars=no,location=no,menubar=no";
            features += ",channelmode=no,directories=no,fullscreen=no,titlebar=no";
            break;
        case 1: //一般的ie視窗
            features = "Height=" + y + ",Width=" + x;
            features += ",top=" + wk_top + ",left=" + wk_left;
            features += ",status=yes,resizable=yes,scrollbars=yes,location=yes,menubar=yes";
            features += ",channelmode=no,directories=yes,fullscreen=no,titlebar=yes";
            break;
        case 2: //無狀態列無網址列，可調整大小，捲軸自動
            features = "Height=" + y + ",Width=" + x;
            features += ",top=" + wk_top + ",left=" + wk_left;
            features += ",status=yes,resizable=yes,scrollbars=yes,location=no,menubar=no";
            features += ",channelmode=no,directories=no,fullscreen=no,titlebar=no";
            break;
        case 3: //全螢幕視窗
            features = "Height=" + y + ",Width=" + x;
            features += ",top=" + wk_top + ",left=" + wk_left;
            features += ",status=no,resizable=no,scrollbars=no,location=no,menubar=no";
            features += ",channelmode=no,directories=no,fullscreen=yes,titlebar=no";
            break;
        case 4: //左上角視窗
            features = "Height=" + y + ",Width=" + x;
            features += ",top=0,left=0";
            features += ",status=yes,resizable=yes,scrollbars=yes,location=no,menubar=no";
            features += ",channelmode=no,directories=no,fullscreen=no,titlebar=no";
            break;
        case 5: //左上角視窗(圖片用)
            features = "Height=" + y + ",Width=" + x;
            features += ",top=0,left=0";
            features += ",status=no,resizable=yes,scrollbars=no,location=no,menubar=no";
            features += ",channelmode=no,directories=no,fullscreen=no,titlebar=no";
            break;
        case 6: //右上角視窗
            var wRight = x_screen - x - 10;
            features = "Height=" + y + ",Width=" + x;
            features += ",top=0,left=" + wRight;
            features += ",status=no,resizable=no,scrollbars=no,location=no,menubar=no";
            features += ",channelmode=no,directories=no,fullscreen=no,titlebar=no";
            break;
        case 7://固定大小的視窗，無狀態列無網址列有捲軸
            features = "Height=" + y + ",Width=" + x;
            features += ",top=" + wk_top + ",left=" + wk_left;
            features += ",scrollbars=";
            break;
        default:
            alert("error");
            break;
    }
    window.open(theURL, MyWinName, features);
}
function OpenWinDialog(theURL, x, y, MyType) {
    var strStatus;
    var features;
    window.status = "";
    switch (MyType) {
        case 0:
            features = "dialogHeight=" + y + "px;dialogWidth=" + x + "px";
            features += ";status=no;resizable=no;scrollbars=auto;help=no;center=yes";
            break;
        case 1:
            features = "dialogHeight=" + y + "px;dialogWidth=" + x + "px";
            features += ";status=no;resizable=yes;scrollbars=auto;help=no;center=yes";
            break;
        case 2: //無狀態列無網址列，可調整大小，捲軸自動
            features = "dialogHeight=" + y + "px;dialogWidth=" + x + "px";
            //features+=",top=0" + wk_top + ",left=" + wk_left;
            features += ",status=yes,resizable=yes,scrollbars=yes,location=no,menubar=no";
            features += ",channelmode=no,directories=no,fullscreen=no,titlebar=no";
            break;
        default:
            alert("error");
            break;
    }
    return window.showModalDialog(theURL, "winName", features);
    //  strStatus=window.Modeless(theURL,winName,features);
}
function imageProtect(mousebutton) {
    if (navigator.appName == "Microsoft Internet Explorer") {
        if (mousebutton == 2 || mousebutton == 3 || mousebutton == 6 || mousebutton == 7) {
            alert('如果您有任何操作上的問題請向系統維護人員反映謝謝！');
            return false;
        }
    } else if (navigator.appName == "Netscape") {
        if (mousebutton == 3) {
            alert('如果您有任何操作上的問題請向系統維護人員反映謝謝！');
            return false;
        }
    } else
        return true;
}

function imageProtect(mousebutton) { //3.0
    if (navigator.appName == "Microsoft Internet Explorer") {
        if (mousebutton == 2 || mousebutton == 3 || mousebutton == 6 || mousebutton == 7) {
            alert('如果您有任何操作上的問題請向系統維護人員反映謝謝！');
            return false;
        }
    } else if (navigator.appName == "Netscape") {
        if (mousebutton == 3) {
            alert('如果您有任何操作上的問題請向系統維護人員反映謝謝！');
            return false;
        }
    } else
        return true;
}
function J_reload() {
    location.reload();
}

function IsDate(obj, myType) {
    var MyDate = /[0-9]{8}/;
    var tmpStr = obj.value;
    var tmpStr1 = tmpStr.toString();
    var tmpYr = tmpStr1.substr(0, 4);
    var tmpMon = tmpStr1.substr(4, 2);
    var tmpDay = tmpStr1.substr(6, 2);
    var intYr = Number(tmpYr);
    var intMon = Number(tmpMon);
    var intDay = Number(tmpDay);
    var maxDay = 0;
    switch (myType) {
        case 0:
            if (tmpStr.length != 0) {
                if (!MyDate.test(tmpStr)) {
                    alert("請輸入8位數西元年月日，例如2001年9月11日，請輸入'20010911'");
                    obj.focus();
                    obj.select();
                    //date_birthday();
                    return false;
                }
                if (intMon < 1 || intMon > 12) {
                    alert("月份錯誤，請輸入月份介於 1 月至 12 月！");
                    obj.focus();
                    obj.select();
                    return false;
                }
                if (intMon == 2)
                    if ((intYr % 400 == 0) || (intYr % 100 != 0) && (intYr % 4 == 0))
                        maxDay = 29;
                    else
                        maxDay = 28;
                else
                if (((intMon * 6 / 7) % 2) <= 1)
                    maxDay = 31; //alert (tmpMon + "  大月");
                else
                    maxDay = 30; //alert (tmpMon + "  小月");

                if (intDay < 1 || intDay > maxDay) {
                    alert("日期錯誤，請輸入" + tmpMon + "月份日期介於 1 至 " + maxDay.toString() + "！");
                    obj.focus();
                    obj.select();
                    return false;
                }
                return true;
            } else
                return true;
            break;
        case 1:
            if (!MyDate.test(tmpStr)) {
                alert("請輸入8位數西元年月日，例如2001年9月11日，請輸入'20010911'");
                obj.focus();
                obj.select();
                //date_birthday();
                return false;
            }
            if (intMon < 1 || intMon > 12) {
                alert("月份錯誤，請輸入月份介於 1 月至 12 月！");
                obj.focus();
                obj.select();
                return false;
            }
            if (intMon == 2)
                if ((intYr % 400 == 0) || (intYr % 100 != 0) && (intYr % 4 == 0))
                    maxDay = 29;
                else
                    maxDay = 28;
            else
            if (((intMon * 6 / 7) % 2) <= 1)
                maxDay = 31; //alert (tmpMon + "  大月");
            else
                maxDay = 30; //alert (tmpMon + "  小月");

            if (intDay < 1 || intDay > maxDay) {
                alert("日期錯誤，請輸入" + tmpMon + "月份日期介於 1 至 " + maxDay.toString() + "！");
                obj.focus();
                obj.select();
                return false;
            }
            return true;
            break;
        default:
            alert('error');
            return false;
            break;
    }
}
function chk_date(obj, myType) {
    var MyDate = /[0-9]{8}/;
    var tmpStr = obj.value;
    var tmpStr1 = tmpStr.toString();
    var tmpYr = tmpStr1.substr(0, 4);
    var tmpMon = tmpStr1.substr(4, 2);
    var tmpDay = tmpStr1.substr(6, 2);
    var intYr = Number(tmpYr);
    var intMon = Number(tmpMon);
    var intDay = Number(tmpDay);
    var maxDay = 0;
    switch (myType) {
        case 0:
            if (tmpStr.length != 0) {
                if (!MyDate.test(tmpStr)) {
                    alert("請輸入8位數西元年月日，例如2001年9月11日，請輸入'20010911'");
                    obj.focus();
                    obj.select();
                    //date_birthday();
                    return false;
                }
                if (intMon < 1 || intMon > 12) {
                    alert("月份錯誤，請輸入月份介於 1 月至 12 月！");
                    obj.focus();
                    obj.select();
                    return false;
                }
                if (intMon == 2)
                    if ((intYr % 400 == 0) || (intYr % 100 != 0) && (intYr % 4 == 0))
                        maxDay = 29;
                    else
                        maxDay = 28;
                else
                if (((intMon * 6 / 7) % 2) <= 1)
                    maxDay = 31; //alert (tmpMon + "  大月");
                else
                    maxDay = 30; //alert (tmpMon + "  小月");

                if (intDay < 1 || intDay > maxDay) {
                    alert("日期錯誤，請輸入" + tmpMon + "月份日期介於 1 至 " + maxDay.toString() + "！");
                    obj.focus();
                    obj.select();
                    return false;
                }
                return true;
            } else
                return true;
            break;
        case 1:
            if (!MyDate.test(tmpStr)) {
                alert("請輸入8位數西元年月日，例如2001年9月11日，請輸入'20010911'");
                obj.focus();
                obj.select();
                //date_birthday();
                return false;
            }
            if (intMon < 1 || intMon > 12) {
                alert("月份錯誤，請輸入月份介於 1 月至 12 月！");
                obj.focus();
                obj.select();
                return false;
            }
            if (intMon == 2)
                if ((intYr % 400 == 0) || (intYr % 100 != 0) && (intYr % 4 == 0))
                    maxDay = 29;
                else
                    maxDay = 28;
            else
            if (((intMon * 6 / 7) % 2) <= 1)
                maxDay = 31; //alert (tmpMon + "  大月");
            else
                maxDay = 30; //alert (tmpMon + "  小月");

            if (intDay < 1 || intDay > maxDay) {
                alert("日期錯誤，請輸入" + tmpMon + "月份日期介於 1 至 " + maxDay.toString() + "！");
                obj.focus();
                obj.select();
                return false;
            }
            return true;
            break;
        default:
            alert('error');
            return false;
            break;
    }
}

function chk_Year(obj) {
    var MyYYMM = /[0-9]{4}/;
    var tmpStr = obj.value;
    if (!MyYYMM.test(tmpStr)) {
        alert("請輸入4位數西元年度，例如2001年，請輸入'2001'");
        obj.focus();
        obj.select();
        return false;
    }
    return true;
}
function chk_YYMM(obj) {
    var MyYYMM = /[0-9]{6}/;
    var tmpStr = obj.value;
    if (!MyYYMM.test(tmpStr)) {
        alert("請輸入6位數西元年月，例如2001年11月，請輸入'200111'");
        obj.focus();
        obj.select();
        return false;
    }
    var tmpStr1 = tmpStr.toString();
    var tmpYr = tmpStr1.substr(0, 4);
    var tmpMon = tmpStr1.substr(4, 2);
    var intYr = Number(tmpYr);
    var intMon = Number(tmpMon);
    if (intMon < 1 || intMon > 12) {
        alert("月份錯誤，請輸入月份介於 1 月至 12 月！");
        obj.focus();
        obj.select();
        return false;
    }
    return true;
}

function IsEN(obj) {
    var MyStr = /[^a-zA-Z0-9_\-]{1,}/;
    obj.value = obj.value.replace(' ', '')
    if (!MyStr.test(obj.value)) {
        return true;
    } else {
        alert("請勿輸入英文數字以及底線減號符號以外的字母");
        obj.focus();
        obj.select();
        return false;
    }
}

function IsChinese(str) {//檢查是否有中文字
    for (var k = 0; k < str.length; k++) {
        if (escape(str.charAt(k)).length >= 4) {
            return true;
        }
    }
    return false;
}

function birthday(BY, BM, BD) {
    now = new Date();
    year = now.getFullYear();
    if (BY.value == "" || BM.value == "" || BD.value == "") {
        alert("請填寫【出生日期】 !");
        BY.focus();
        BY.select();
        return false;
    }
    if (!(parseInt(BY.value) <= year && parseInt(BY.value) > 1900)) {
        alert("【出生日期】年份不正確！");
        BY.focus();
        BY.select();
        return false;
    }
    var fmonval = BM.value;
    if (parseInt(fmonval) == 0) {
        fmonval = fmonval.substr(1, 1);
    }
    if (!(parseInt(fmonval) < 13 && parseInt(fmonval) > 0)) {
        alert("【出生日期】月份不正確！");
        BM.focus();
        return false;
    }
    var fdayval = BD.value;
    if (parseInt(fdayval) == 0) {
        fdayval = fdayval.substr(1, 1);
    }
    var maxday = MaxDay(parseInt(BY.value), parseInt(fmonval));

    if (!(parseInt(fdayval) <= maxday && parseInt(fdayval) > 0)) {
        alert("【出生日期】日期不正確！");
        BD.focus();
        return false;
    }
    return true;
}

function MaxDay(tmpyear, tmpmonth) {
    SolarCal = new Array(12);
    tmpmonth = tmpmonth - 1;
    if (GetLeap(tmpyear)) {
        SolarCal = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    } else {
        SolarCal = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    }
    return SolarCal[tmpmonth];
}

function GetLeap(year) {
    if (year % 400 == 0)
        return 1;
    else if (year % 100 == 0)
        return 0;
    else if (year % 4 == 0)
        return 1;
    else
        return 0;
}



//取中文字數字串值
//傳入物件,中文字長度
//maxWordCHT(obj,10)
//會把物件值取代
function maxWordCHT(obj, len) {
    var NotNumber = /[^0-9]+/;
    var Chinese = /^[\u4E00-\u9FA5]+$/;
    var maxLen
    var currLen = 0;
    var tmpStr = "";
    if (obj.length <= 0)	//無值
        return;
    if (NotNumber.test(len)) //非數字
        return;

    maxLen = len * 2;

    for (var i = 0; i < obj.value.length; i++) {
        if (Chinese.test(obj.value.charAt(i))) {	//中文
            currLen = currLen + 2;
        } else {
            currLen = currLen + 1;
        }
        //
        if (currLen > maxLen) {	///
            alert("字數超過" + len + "個中文字");
            break;
        } else {
            tmpStr = tmpStr + obj.value.charAt(i);
        }
    }

    obj.value = tmpStr;
}

function chk_account(obj) {
    var MyStr = /[a-zA-Z]{1}[a-zA-Z0-9_]{3}/;
    var MyStr2 = /[\s]+/;
    if (MyStr.test(obj.value)) {
        //alert("aaa");
        if (MyStr2.test(obj.value)) {
            alert("請輸入正確帳號\n第一個字必須是英文字，至少4個字，且不可空白");
            return false;
        } else
            return true;
    } else {
        alert("請輸入正確帳號\n第一個字必須是英文字，至少4個字");
        obj.focus();
        obj.select();
        return false;
    }
}
/*
 function chk_pwd(obj1,obj2){
 if (IsEmpty(obj1,"密碼")){
 obj1.focus();
 obj1.select();
 return false;}
 else 
 if(IsEmpty(obj2,"密碼確認")){
 obj2.focus();
 obj2.select();
 return false;}
 else
 if (obj1.value==obj2.value)
 return true;
 else{
 alert("密碼確認不正確!!請再輸入一次!!");
 obj2.focus();
 obj2.select();
 return false;}
 }
 */

function chk_LoginPwd(obj) { //檢查登入密碼
    var MyStr = /[a-zA-Z]{1,}[a-zA-Z0-9_]{7,14}/;
    var MyStr1 = /[a-zA-Z]{1}/;
//var MyStr2=/[a-zA-Z0-9_]{5,}/;
    var MySymbol = /[\~\?\!\@\#\$\%\^\&\*\(\)\/\\\n\r\t\}\{\|\:\"\>\<\-\'\.\`\+\=]{1,}/;

    if (obj.value == "") {
        alert("請填入密碼");
        obj.focus();
        obj.select();
        return false;
    } else
    if (MySymbol.test(obj.value)) {
        alert("密碼不可以包含特殊字元");
        obj.focus();
        obj.select();
        return false;
    } else
    if (obj.value.length < 6) {
        alert("密碼長度必須大於6個位元(含6個)");
        obj.focus();
        obj.select();
        return false;
    } else
        return true;
}

function chk_pwd(obj1, obj2) {
    var MyStr = /[a-zA-Z]{1,}[a-zA-Z0-9_]{7,14}/;
    var MyStr1 = /[a-zA-Z]{1}/;
//var MyStr2=/[a-zA-Z0-9_]{5,}/;
    var MySymbol = /[\~\?\!\@\#\$\%\^\&\*\(\)\/\\\n\r\t\}\{\|\:\"\>\<\-\'\.\`\+\=]{1,}/;

    if (IsEmpty(obj1, "密碼")) {
        obj1.focus();
        obj1.select();
        return false;
    } else
    if (IsEmpty(obj2, "密碼確認")) {
        obj2.focus();
        obj2.select();
        return false;
    } else
    if (obj1.value == obj2.value)
//				return true;
///////////////////////////////////////////////////////////////再比對密碼是否符合				
        if (MySymbol.test(obj1.value)) {
            alert("密碼不可以包含特殊字元");
            obj1.focus();
            obj1.select();
            return false;
        } else
        if (obj1.value.length < 6) {
            alert("密碼長度必須大於6個位元(含6個)");
            obj1.focus();
            obj1.select();
            return false;
        } else
            return true;

    else {
        alert("密碼確認不正確!!請再輸入一次!!");
        obj2.focus();
        obj2.select();
        return false;
    }


}



function chk_email(obj, myType) {
//var MyStr=/[a-zA-Z_0-9\.]{2,}@[\w]{3,}\.[\w]{3,}/
    var MyStr = /@/					//只檢查@
    if (myType == "0") //可以空白
        if (obj.value != "")
            if (MyStr.test(obj.value))
                return true;
            else {
                alert("請輸入正確的電子郵件信箱");
                obj.focus();
                obj.select();
                return false;
            }
        else
            return true;
    else
    if (MyStr.test(obj.value))
        return true;
    else {
        alert("請輸入正確的電子郵件信箱");
        obj.focus();
        obj.select();
        return false;
    }
}
function chk_phone(obj, myType) {
    var MyPhone = /[0-9]{7,10}/;
    var NotMyPhone = /[^0-9]{1,}/;
    var tmpStr = obj.value;
    switch (myType) {
        case 0://可以空白
            if (tmpStr != "")
                if (NotMyPhone.test(tmpStr)) {
                    alert("不可以有數字以外的字元");
                    obj.focus();
                    obj.select();
                    return false;
                } else
                if (!MyPhone.test(tmpStr) || NotMyPhone.test(tmpStr)) {
                    alert("最少輸入7碼數字");
                    obj.focus();
                    obj.select();
                    return false;
                } else
                    return true;
            else
                return true;
            break;
        case 1://一定要輸入
            if (NotMyPhone.test(tmpStr)) {
                alert("不可以有數字以外的字元");
                obj.focus();
                obj.select();
                return false;
            } else
            if (!MyPhone.test(tmpStr) || NotMyPhone.test(tmpStr)) {
                alert("最少輸入7碼數字");
                obj.focus();
                obj.select();
                return false;
            } else
                return true;
            break;
        case 2://特殊用法
            if (tmpStr.length == 0) {
                return false;
            } else
                return true;
            break;
        default:
            alert("error");
            return false;
            break;
    }
}

function chk_cell(obj, myType) {
    var MyPhone = /[0-9]{10,}/;
    var NotMyPhone = /[^0-9]{1,}/;
    var tmpStr = obj.value;
    switch (myType) {
        case 0://可以空白
            if (tmpStr != "")
                if (NotMyPhone.test(tmpStr)) {
                    alert("不可以有數字以外的字元");
                    obj.focus();
                    obj.select();
                    return false;
                } else
                if (!MyPhone.test(tmpStr) || NotMyPhone.test(tmpStr)) {
                    alert("最少輸入10碼數字");
                    obj.focus();
                    obj.select();
                    return false;
                } else
                    return true;
            else
                return true;
            break;
        case 1://一定要輸入
            if (NotMyPhone.test(tmpStr)) {
                alert("不可以有數字以外的字元");
                obj.focus();
                obj.select();
                return false;
            } else
            if (!MyPhone.test(tmpStr) || NotMyPhone.test(tmpStr)) {
                alert("最少輸入10碼數字");
                obj.focus();
                obj.select();
                return false;
            } else
                return true;
            break;
        case 2://特殊用法
            if (tmpStr.length == 0) {
                return false;
            } else
                return true;
            break;
        default:
            alert("error");
            return false;
            break;
    }
}
function chk_empty(obj, msg) {
    if (IsEmpty(obj, msg)) {
        event.returnValue = false;
    } else
        event.returnValue = true;
}
function chk_number(obj, str) {
    if (!IsNumber(obj, str)) {
        event.returnValue = false;
    } else
        event.returnValue = true;
}
function IsFloat(obj, myType) {
    var Float = /^[0-9]+(\.[0-9]+)?$/;
    var tmpStr = obj.value;
    switch (myType) {
        case 1:
            if (tmpStr != "") {
                if (!Float.test(tmpStr)) {
                    obj.focus();
                    obj.select();
                    alert("請輸入正確的數字");
                    return false;
                } else {
                    return true;
                }
            } else {
                obj.focus();
                alert("請輸入數字");
                return false;
            }
            break;
        case 0: //可以空白
            if (tmpStr != "") {
                if (!Float.test(tmpStr)) {
                    obj.focus();
                    obj.select();
                    alert("請輸入正確的數字");
                    return false;
                } else
                    return true;
            } else {
                return true;
            }
            break;
        default:
            alert("error")
            return true;
            break;

    }

}
function IsAmount(obj, myType) {
//檢查金額
    var Amount = /^\-{0,1}[0-9]+(\.[0-9]+)?$/;
    var tmpStr = obj.value;
    switch (myType) {
        case 1:
            if (tmpStr != "") {
                if (!Amount.test(tmpStr)) {
                    obj.focus();
                    obj.select();
                    alert("請輸入正確的數字");
                    return false;
                } else {
                    return true;
                }
            } else {
                obj.focus();
                alert("請輸入數字");
                return false;
            }
            break;
        case 0: //可以空白
            if (tmpStr != "") {
                if (!Amount.test(tmpStr)) {
                    obj.focus();
                    obj.select();
                    alert("請輸入正確的數字");
                    return false;
                } else
                    return true;
            } else {
                return true;
            }
            break;
        default:
            alert("error")
            return true;
            break;

    }
}

function IsNumber(obj, myType) {
    var NotNumber = /[^0-9\-]+/;
    var tmpStr = obj.value;
    switch (myType) {
        case 1:
            if (tmpStr != "") {
                if (NotNumber.test(tmpStr)) {
                    obj.focus();
                    obj.select();
                    alert("不可以有數字以外的字元");
                    return false;
                } else {
                    return true;
                }
            } else {
                obj.focus();
                alert("請輸入數字");
                return false;
            }
            break;
        case 0: //可以空白
            if (tmpStr != "") {
                if (NotNumber.test(tmpStr)) {
                    obj.focus();
                    obj.select();
                    alert("不可以有數字以外的字元");
                    return false;
                } else
                    return true;
            } else {
                return true;
            }
            break;
        default:
            alert("error")
            return true;
            break;
    }

}
function IsEmptyNoMsg(obj) {
    var MyType = obj.type;
    switch (MyType) {
        case "text":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                return true;
                break;
            } else {
                return false;
                break;
            }
        case "password":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                return true;
                break;
            } else {
                return false;
                break;
            }
        case "textarea":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                return true;
                break;
            } else {
                return false;
                break;
            }
        case 'checkbox':
            if (!obj.checked) {
                return true;
            } else {
                return false;
            }
        case 'radio':
            if (!obj.checked) {
                return true;
            } else {
                return false;
            }
        case "select-one":
            if (obj.options[0].selected) {
                return true;
                break;
            } else {
                return false;
                break;
            }
        case "select-multiple":
            if (!obj.selected) {
                return true;
                break;
            } else {
                return false;
                break;
            }
        case "hidden":
            obj.value.replace(' ', '');
            if (obj.value.length == 0) {
                return true;
                break;
            } else {
                return false;
                break;
            }
        case "file":
            if (obj.value.length == 0) {
                return true;
                break;
            } else {
                return false;
                break;
            }
        default:
            if (obj.length > 0) {
                MyType = obj[0].type;
                switch (MyType) {
                    case 'checkbox':
                        for (var i = 0; i < obj.length; i++) {
                            if (obj[i].checked) {
                                return false;
                                break;
                            }
                        }
                        return true;
                        break;
                    case 'radio':
                        for (var i = 0; i < obj.length; i++) {
                            if (obj[i].checked) {
                                return false;
                                break;
                            }
                        }
                        return true;
                        break;
                    default:
                        return true;
                        break;
                }
                return true;
            } else {
                alert("不支援此輸入元件");
                return true;
            }
            break;
    }

}

function IsEmpty(obj, msg) {
    var MyType = obj.type;
    switch (MyType) {
        case "text":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                alert("請填寫" + msg);
                obj.focus();
                return true;
            } else
                return false;
            break;
        case "password":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                alert("請填寫" + msg);
                obj.focus();
                return true;
            } else
                return false;
            break;
        case "textarea":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                alert("請填寫" + msg);
                obj.focus();
                return true;
            } else
                return false;
            break;
        case "select-one":
            if (obj.value == '') {
                alert("請選取" + msg);
                obj.focus();
                return true;
            } else
                return false;
            break;
        case "select-multiple":
            if (!obj.selected) {
                alert("未完成");
                obj.focus();
                return true;
            } else
                return false;
            break;
        case "hidden":
            obj.value.replace(' ', '');
            if (obj.value.length == 0) {
                alert("請輸入" + msg);
                return true;
            } else
                return false;
            break;
        case "file":
            if (obj.value.length == 0) {
                alert("請輸入" + msg);
                return true;
            } else
                return false;
            break;
        case 'checkbox':
            if (!obj.checked) {
                alert("請選取" + msg);
                return true;
            } else {
                return false;
            }
        case 'radio':
            if (!obj.checked) {
                alert("請選取" + msg);
                return true;
            } else {
                return false;
            }
        default:
            if (obj.length > 0) {
                MyType = obj[0].type;
                switch (MyType) {
                    case 'checkbox':
                        for (var i = 0; i < obj.length; i++) {
                            if (obj[i].checked) {
                                return false;
                                break;
                            }
                        }
                        alert("請選取" + msg);
                        return true;
                        break;
                    case 'radio':
                        for (var i = 0; i < obj.length; i++) {
                            if (obj[i].checked) {
                                return false;
                                break;
                            }
                        }
                        alert("請選取" + msg);
                        return true;
                        break;
                    default:
                        return true;
                        break;
                }
                return true;
            } else {
                alert("不支援此輸入元件");
                return true;
            }
            break;
    }

}

function IsChecked(obj, msg) {
    var wLen = obj.length;
    if (typeof (wLen) == 'undefined') {
        if (obj.checked) {
            return true;
        }
    } else {
        for (i = 0; i < wLen; i++) {
            if (obj[i].checked) {
                return true;
            }
        }
    }
    alert("請選擇" + msg);
    return false;
}

function IsCheckedDiffNM(pForm, pFldName, pMsg) {//前檢查 (包含刪除前檢查)
//pForm 傳入的form物件
//pFldName 要檢查的checkbox物件名稱字串
//檢查checkbox 是否有選取
//有選取則詢問是否確定要執行
//將傳進來的form submit出去
    wFlg = false;
    for (var i = 0; i <= pForm.elements.length - 1; i++) {
        if (pForm.elements[i].type == "checkbox") {
            if (pForm.elements[i].name.indexOf(pFldName) >= 0) {
                if (pForm.elements[i].checked == true) {
                    wFlg = true;
                    break;
                }
            }
        }
    }
    if (wFlg == true) {
        return true;
    } else {
        alert(pMsg);
        return false;
    }
}



function maxWords(obj, pNum) {

    if (obj.value.length > pNum) {
        alert("字數超過" + pNum + "字" + "\n目前字數為" + obj.value.length + "字");
        obj.focus();
        obj.select();
        return false;
    } else
        return true;
}

function chk_gui(pa_gui_no_obj) {
    var pa_gui_no = pa_gui_no_obj.value;
    var I = 0;
    var j = 0;
    var get_str = new Array(8);
    var times_num = new Array(8);
    var tot_num = new Number;
    times_num[1] = 1;
    times_num[2] = 2;
    times_num[3] = 1;
    times_num[4] = 2;
    times_num[5] = 1;
    times_num[6] = 2;
    times_num[7] = 4;
    times_num[8] = 1;
    tot_num = 0;
    if (pa_gui_no.length == 0) {
        return true;
    }
    if (pa_gui_no.length != 8) {
        alert("請輸入正確的統一編號");
        pa_gui_no_obj.focus();
        pa_gui_no_obj.select();
        return false;
    }
    var tmpStr = new String;
    for (I = 1; I <= 8; ++I) {
        get_str[I] = parseInt(pa_gui_no.substr(I - 1, 1)) * times_num[I];
        tmpStr = get_str[I].toString();
        for (j = 0; j < tmpStr.length; j++) {
            tot_num = tot_num + parseInt(tmpStr.substr(j, 1));
        }
    }
    if ((tot_num % 10) == 0) {
        return true;
    } else {
        if (pa_gui_no.substr(6, 1) != "7") {
            alert("請輸入正確的統一編號");
            pa_gui_no_obj.focus();
            pa_gui_no_obj.select();
            return false;
        } else {
            get_str[7] = 1;
            tot_num = 0;
            for (I = 1; I <= 8; ++I) {
                tmpStr = get_str[I].toString();
                for (j = 0; j < tmpStr.length; j++) {
                    tot_num = tot_num + parseInt(tmpStr.substr(j, 1));
                }
            }
            alert(tot_num);
            if (tot_num % 10 == 0) {
                return true;
            } else {
                get_str[7] = 0;
                tot_num = 0;
                for (I = 1; I <= 8; ++I) {
                    tmpStr = get_str[I].toString();
                    for (j = 0; j < tmpStr.length; j++) {
                        tot_num = tot_num + parseInt(tmpStr.substr(j, 1));
                    }
                }
                alert(tot_num);
                if (tot_num % 10 == 0) {
                    return true;
                } else {
                    alert("請輸入正確的統一編號");
                    pa_gui_no_obj.focus();
                    pa_gui_no_obj.select();
                    return false;
                }
            }
        }
    }
}//End Function


// 處理前端互動的基本function
function FindElem(ElemName)
{
    var Elem;
    if (ns4)
    {
        Elem = eval("document." + ElemName);
    } else if (ns6)
    {
        Elem = document.getElementById(ElemName);
    } else
    {
        Elem = document.all[ElemName];
    }
    return(Elem);
}

function SetElementValue(ElemName, Value)
{
    var obj = FindElem(ElemName);
    if (obj)
        obj.value = Value;
}

function InnerHtml(ElemName, Value)
{
    var obj = FindElem(ElemName);
    try
    {
        if (obj)
            obj.innerHTML = Value;
    } catch (e)
    {
    }
}

//=========================						==========
//Exp: iniFormSet('rcvdate', 'm', 7, 'd', 1, 's', 8, 'v', '0920702', 'a', 1)	--> 收鍵日期的條件為長度最多七碼、為日期型態、size為8、預設值為0920702、可自動切換Tab

//Exp: iniFormSet('value', 'dc', 10.3)	--> value十碼,七碼整數,三碼小數,包含負號及小數點 最多12碼
//Exp: iniFormSet('value', 'dc1', 10.3)	--> value十碼,七碼整數,三碼小數,不可輸入負號,包含小數點 最多11碼

//初始化 Input 物件
//傳入參數 objName --> Input Name
//m --> MaxLength(num)			r --> ReadOnly(boolean)		n --> Is Number(boolean)
//d --> Disable(boolean)		s --> Size(num)			A --> AutoTab(boolean)
//V --> Value(str or num)		u --> toUpperCase(boolean)	l --> toLowerCase(boolean)
//dt --> Is Date(boolean)		h --> Is Hour(boolean)		cm--> unicode max length
//n1 --> Is Number正(boolean)		f --> FillZero(int)		fc--> focus(boolean)
//en --> Is English & Number(boolean)	fs--> Full String
//DC --> 可輸入負值float		DC1 --> 只可輸入正浮點數
//TA --> 輸入欄位值的位置 ex 'TA','r' 靠右 
//=============================================
function iniFormSet()
{
    try
    {
        var objName = arguments[0];

        for (var i = 1; i < arguments.length; i += 2)
        {
            switch (arguments[i].toUpperCase())
            {
                case 'FC'://yes
                    if (isNaN(document.forms[0].elements[objName].length))
                        canFocus(document.forms[0].elements[objName]);
                    break;
                case 'M'://yes
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (document.forms[0].elements[objName].type == 'textarea')
                        {
                            document.forms[0].elements[objName].maxLength = arguments[i + 1];
                            document.forms[0].elements[objName].attachEvent("onkeypress", chkTextAreaMaxNumber);
                            document.forms[0].elements[objName].attachEvent("onblur", chkTextAreaMaxNumber);
                        } else {
                            document.forms[0].elements[objName].maxLength = arguments[i + 1];
                        }
                    }
                    break;
                case 'R'://yes
                    if (isNaN(document.forms[0].elements[objName].length))
                        document.forms[0].elements[objName].readOnly = arguments[i + 1];
                    break;
                case 'D'://yes
                    document.forms[0].elements[objName].disabled = arguments[i + 1];
                    break;
                case 'S'://yes
                    if (isNaN(document.forms[0].elements[objName].length) && document.forms[0].elements[objName].size == '20')
                        document.forms[0].elements[objName].size = arguments[i + 1];
                    break;
                case 'S1'://yes
                    if (isNaN(document.forms[0].elements[objName].length) && document.forms[0].elements[objName].size == '20')
                        document.forms[0].elements[objName].style.width = arguments[i + 1] * 17;
                    break;
                case 'A'://no
                    if (isNaN(document.forms[0].elements[objName].length))
                        document.forms[0].elements[objName].attachEvent("onkeyup", autoTab)
                    break;
                case 'V':
                    if (isNaN(document.forms[0].elements[objName].length) || document.forms[0].elements[objName].type == 'select-one') {
                        if (document.forms[0].elements[objName].type == 'select-one') {
                            for (var jj = 0; jj < document.forms[0].elements[objName].length; jj++) {
                                if (document.forms[0].elements[objName].options[jj].value == arguments[i + 1]) {
                                    document.forms[0].elements[objName].value = arguments[i + 1];
                                    break;
                                }
                            }
                        } else {
                            document.forms[0].elements[objName].value = arguments[i + 1];
                        }
                    } else
                    {
                        for (j = 0; j < document.forms[0].elements[objName].length; j++)
                        {
                            if (document.forms[0].elements[objName][j].value == arguments[i + 1]) {
                                document.forms[0].elements[objName][j].checked = true;
                            }
                        }
                    }
                    break;
                case 'TA':
                    if (document.forms[0].elements[objName].type == 'text') {
                        if (arguments[i + 1] == 'r')
                            document.forms[0].elements[objName].style.textAlign = 'right';
                        if (arguments[i + 1] == 'c')
                            document.forms[0].elements[objName].style.textAlign = 'center';
                        if (arguments[i + 1] == 'l')
                            document.forms[0].elements[objName].style.textAlign = 'left';

                    }
                    break;
                case 'U':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (arguments[i + 1])
                            document.forms[0].elements[objName].attachEvent("onkeyup", upperCase);
                    }
                    break;
                case 'L':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (arguments[i + 1])
                            document.forms[0].elements[objName].attachEvent("onkeyup", lowerCase);
                    }
                    break;
                case 'FS':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (arguments[i + 1])
                            document.forms[0].elements[objName].attachEvent("onblur", toFullStr);
                    }
                    break;
                case 'DC':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        document.forms[0].elements[objName].style.imeMode = "disabled";
                        document.forms[0].elements[objName].decmalLength = arguments[i + 1];
                        document.forms[0].elements[objName].maxLength = arguments[i + 1] + 2;
                        document.forms[0].elements[objName].attachEvent("onkeyup", checkDecimal);
                        document.forms[0].elements[objName].attachEvent("onkeypress", onlyAllowNumPress);
                    }
                    break;
                case 'DC1':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        document.forms[0].elements[objName].style.imeMode = "disabled";
                        document.forms[0].elements[objName].decmalLength = arguments[i + 1];
                        document.forms[0].elements[objName].maxLength = arguments[i + 1] + 1;
                        document.forms[0].elements[objName].attachEvent("onkeyup", checkDecimal);
                        document.forms[0].elements[objName].attachEvent("onkeypress", lockFloat);
                    }
                    break;
                case 'NR':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        document.forms[0].elements[objName].numberRange = arguments[i + 1];
                        document.forms[0].elements[objName].attachEvent("onblur", numberRngeCheck);
                    }
                    break;
                case 'NA':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (arguments[i + 1])
                            document.forms[0].elements[objName].attachEvent("onblur", toFullStrCheck);
                    }
                    break;
                case 'DT':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (arguments[i + 1])
                            document.forms[0].elements[objName].attachEvent("onblur", chkDate);
                    }
                    break;
                case 'T':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (arguments[i + 1])
                            document.forms[0].elements[objName].attachEvent("onblur", chkTime);
                    }
                    break;
                case 'I':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (arguments[i + 1])
                            document.forms[0].elements[objName].attachEvent("onblur", checkID);
                    }
                    break;
                case 'F':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        document.forms[0].elements[objName].fillZero = arguments[i + 1];
                        document.forms[0].elements[objName].attachEvent("onblur", fillZero);
                    }
                    break;
                case 'N':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (arguments[i + 1])
                        {
                            document.forms[0].elements[objName].style.imeMode = "disabled";
                            document.forms[0].elements[objName].attachEvent("onkeypress", onlyAllowNumPress);
                            document.forms[0].elements[objName].attachEvent("onkeyup", onlyAllowNumUp);
                        }
                    }
                    break;
                case 'N1':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (arguments[i + 1])
                        {
                            document.forms[0].elements[objName].style.imeMode = "disabled";
                            document.forms[0].elements[objName].attachEvent("onkeypress", onlyAllowNumPress1);
                            document.forms[0].elements[objName].attachEvent("onkeyup", onlyAllowNumUp);
                        }
                    }
                    break;
                case 'EN':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (arguments[i + 1])
                        {
                            document.forms[0].elements[objName].style.imeMode = "disabled";
                            document.forms[0].elements[objName].attachEvent("onkeydown", lockAlphaNum);
                        }
                    }
                    break;
                case 'SE':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (arguments[i + 1])
                        {
                            document.forms[0].elements[objName].style.imeMode = "disabled";
                            document.forms[0].elements[objName].attachEvent("onkeydown", lockAlphaNum1);
                        }
                    }
                    break;
                case 'SU':
                    if (isNaN(document.forms[0].elements[objName].length))
                    {
                        if (arguments[i + 1])
                        {
                            document.forms[0].elements[objName].style.imeMode = "disabled";
                            document.forms[0].elements[objName].attachEvent("onkeydown", lockAlphaNum1);
                            document.forms[0].elements[objName].attachEvent("onkeyup", upperCase);
                        }
                    }
                    break;
                case 'BC':
                    if (isNaN(document.forms[0].elements[objName].length))
                        document.forms[0].elements[objName].style.backgroundColor = arguments[i + 1];
                    break;
                default:
                    break;
            }
        }
    } catch (e)
    {
        throw new Error(1, "<br>" + errorLineStr_ + "<br>/script/form.js." + getFunctionName(arguments) + " Exception:<br>Arguments : " + getArgsContent(arguments) + "<br>Message : " + (e.number & 0xFFFF) + " : " + e.description);
    }
}
//=============================================
//function onlyAllowNumPress1()
//desc:		只允許數字型態(Keypress)(Event)
//input:	null
//output:	null
//=============================================
function onlyAllowNumPress1()
{
    try
    {
        if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 13)
            event.returnValue = false;
    } catch (e)
    {
        throw new Error("<br>" + errorLineStr_ + "<br>/script/form.js." + getFunctionName(arguments) + " Exception:<br>Arguments : " + getArgsContent(arguments) + "<br>Message : " + e.name + " : " + e.description);
    }
}
//=============================================
//function onlyAllowNumUp()
//desc:		只允許數字型態(Keyup)(Event)
//input:	null
//output:	null
//=============================================
function onlyAllowNumUp()
{
    try
    {
        if (isNaN(event.srcElement.value) && event.srcElement.value != '-')
            event.srcElement.value = event.srcElement.value.substr(0, event.srcElement.value.length - 1);
    } catch (e)
    {
        throw new Error("<br>" + errorLineStr_ + "<br>/script/form.js." + getFunctionName(arguments) + " Exception:<br>Arguments : " + getArgsContent(arguments) + "<br>Message : " + e.name + " : " + e.description);
    }
}


//=============================================
//function chkForm()
//Exp: chkForm('rcvdate', '接收日期', 'd');	--> 檢核接收日期為空白
//檢核處理動作
//傳入參數 objName --> Input Name
//e --> Is Empty	n --> Is Number		D --> Is Date
//t --> Is Time		u --> toUpperCase	l --> toLowerCase
//ym--> yyymm		yy--> yyy		cm --> Chinese MaxLength 最大不可超過
//cs --> Chinese MinLength 至少         ce --> Chinese Equal Length
//=============================================
function chkForm()
{
    try
    {
        var objName = arguments[0];
        var reMark = arguments[1];

        for (var i = 2; i < arguments.length; i++)
        {
            switch (arguments[i].toUpperCase())
            {
                case 'CM':
                    var limitLen = arguments[i + 1];
                    if (eval(getLength(document.forms[0].elements[objName].value.replace(/\s*/g, ''))) > eval(limitLen))
                        return setFail(objName, '[' + reMark + '] ' + '所輸入的內容長度不可大於' + limitLen + '位');
                    break;
                case 'CE':
                    var limitLen = arguments[i + 1];
                    if (eval(getLength(document.forms[0].elements[objName].value.replace(/\s*/g, ''))) != eval(limitLen))
                        return setFail(objName, '[' + reMark + '] ' + '所輸入的內容長度必須為' + limitLen + '位');
                    break;
                case 'CS':
                    var limitLen = arguments[i + 1];
                    if (eval(getLength(document.forms[0].elements[objName].value.replace(/\s*/g, ''))) < eval(limitLen))
                        return setFail(objName, '[' + reMark + '] ' + '所輸入的內容長度不可小於' + limitLen + '位');
                    break;
                case 'E':
                    if (document.forms[0].elements[objName].value.replace(/\s*/g, '') == '')
                        return setFail(objName, '[' + reMark + '] ' + getMessage('101'));
                    break;
                case 'I':
                    if (!chkID(document.forms[0].elements[objName].value))
                        return setFail(objName, '[' + reMark + '] ' + getMessage('101'));
                    break;
                case 'N':
                    if (isNaN(document.forms[0].elements[objName].value))
                        return setFail(objName, '[' + reMark + '] ' + getMessage('103'));
                    break;
                case 'D':
                    if (!checkCHDate(fillStr(document.forms[0].elements[objName].value.replace(/\s*/g, ''), 7, '0')) && document.forms[0].elements[objName].value != '')
                        return setFail(objName, '[' + reMark + '] ' + getMessage('105'));
                    else if (document.forms[0].elements[objName].value != '')
                        document.forms[0].elements[objName].value = fillStr(document.forms[0].elements[objName].value.replace(/\s*/g, ''), 7, '0');
                    break;
                case 'T':
                    if (!checkTime(document.forms[0].elements[objName].value.replace(/\s*/g, '')) && document.forms[0].elements[objName].value != '')
                        return setFail(objName, '[' + reMark + '] ' + getMessage('106'));
                    break;
                case 'U':
                    document.forms[0].elements[objName].value = document.forms[0].elements[objName].value.toUpperCase();
                    break;
                case 'L':
                    document.forms[0].elements[objName].value = document.forms[0].elements[objName].value.toLowerCase();
                    break;
                case 'YM':
                    if (!checkCHDate(fillStr(document.forms[0].elements[objName].value.replace(/\s*/g, ''), 5, '0') + '' + '01') && document.forms[0].elements[objName].value != '')
                        return setFail(objName, '[' + reMark + '] ' + getMessage('116'));
                    else if (document.forms[0].elements[objName].value != '')
                        document.forms[0].elements[objName].value = fillStr(document.forms[0].elements[objName].value.replace(/\s*/g, ''), 5, '0');
                    break;
                case 'YY':
                    if (!checkCHDate(fillStr(document.forms[0].elements[objName].value.replace(/\s*/g, ''), 3, '0') + '' + '0101') && document.forms[0].elements[objName].value != '')
                        return setFail(objName, '[' + reMark + '] ' + getMessage('117'));
                    else if (document.forms[0].elements[objName].value != '')
                        document.forms[0].elements[objName].value = fillStr(document.forms[0].elements[objName].value.replace(/\s*/g, ''), 3, '0');
                    break;
                default:
                    break;
            }
        }
        return true;
    } catch (e)
    {
        throw new Error("<br>" + errorLineStr_ + "<br>/script/ComFun.js." + getFunctionName(arguments) + " Exception:<br>Arguments : " + getArgsContent(arguments) + "<br>Message : " + e.name + " : " + e.description);
    }
}
//========================================
//function setFail(objName, errMsg)
//設定檢核錯誤處理動作
//=============================================
function setFail(objName, errMsg)
{
    try
    {
        showMessage(errMsg);
        if (!canFocus(document.forms[0].elements[objName]))
            if (!canFocus(document.forms[0].elements["Q_" + objName + "_0"]))
                canFocus(document.forms[0].elements[objName + "_0"]);
        return false;
    } catch (e)
    {
        throw new Error("<br>" + errorLineStr_ + "<br>/script/form.js." + getFunctionName(arguments) + " Exception:<br>Arguments : " + getArgsContent(arguments) + "<br>Message : " + e.name + " : " + e.description);
    }
}
function getFormValue(form) {
    var str = '', ft, fv;

    for (var i = 0; i < form.elements.length; i++) {
        fv = form.elements[i];
        ft = fv.type.toLowerCase();

        switch (ft) {
            case 'select-one':
                str += fv.name + '=' + escape(fv.value) + '&';
                break;
            case 'radio':
                if (fv.checked) {
                    str += fv.name + '=' + escape(fv.value) + '&';
                }
                break;
            case 'checkbox':
                if (fv.checked) {
                    str += fv.name + '=' + escape(fv.value) + '&';
                }
                break;
            case 'text':
                str += fv.name + '=' + escape(fv.value) + '&';
                break;
            case 'password':
                str += fv.name + '=' + escape(fv.value) + '&';
                break;
            case 'hidden':
                str += fv.name + '=' + escape(fv.value) + '&';
                break;
            case 'textarea':
                str += fv.name + '=' + escape(fv.value) + '&';
                break;
            default:
                break;
        }
    }

    return str.split(/\s/).join('')
}

function GetMyPos(wObj) {
    //取得傳入物件所在文件中的實際位置
    var MyPos = new Object();
    MyPos.top = wObj.offsetTop;
    MyPos.left = wObj.offsetLeft;
    if (wObj.offsetParent != null) {
        var ParentPos = GetMyPos(wObj.offsetParent)
        MyPos.top += ParentPos.top;
        MyPos.left += ParentPos.left;
    }
    return MyPos;
}

function GetIdx(wName) {
    //取得目前所在行號(當名稱如:delVal1=>1,delVal30=>30)
    var idx;
    var parten = /\S+\D+(\d+)\b/;
    var result = wName.match(parten);
    if (result != null) {
//			idx=0;
//			var teststr='';
//			for (var i=0;i<result.length;i++){
//				teststr+=';'+result[i];
//			}
//			alert(teststr);
        idx = result[result.length - 1];
    } else {
        idx = -1;
    }
    return idx;
}

//建立動態物件
// 這是經過調整的 html 元素建立方式，以後每次要建立 html 元素時就呼叫它。
/*範例
 var newElement = CreateElement(‘div’, 
 {class: ‘newDivClass’, id: ‘newDiv’, name: ‘newDivName’},
 {width: ‘300px’, height:‘200px’, margin:‘0 auto’, border:‘1px solid #DDD’},
 ‘這是存在於在新建立標籤 div 中的文字。’);
 */
CreateElement = function (TagName, Attribute, Style, Text) {
    var Obj = document.createElement(TagName);
    if (Attribute) {
        for (var each in Attribute) {
            //alert(Attribute[each]);
            //if (each == 'class'){ Obj.className = Attribute[each];}
            //else 
            if (each == 'id')
                Obj.id = Attribute[each];
            else
                Obj.setAttribute(each, Attribute[each]);
        }
    }
    if (Style) {
        for (var each in Style)
            Obj.style[each] = Style[each];
    }
    if (Text) {
        Obj.appendChild(document.createTextNode(Text));
    }
    return Obj;
}

function getElementsByClassName(ClassName) {
    /*獲取 className 相同的元素*/
    var el = [], _el = document.getElementsByTagName('*');
    for (var i = 0; i < _el.length; i++) {
        if (_el[i].className == ClassName) {
            el[el.length] = _el[i];
        }
    }
    return el;
}

function GetVal(wObjNM) {
    /*
     取得物件值。
     當該物件存在多筆值時，該值以,串起傳回
     */
    var obj = document.getElementsByName(wObjNM);
    var objType, objCount, valStr;
    if (obj != null) {
        valStr = '';
        objCount = obj.length;
        if (objCount > 0) {
            switch (obj[0].type) {
                case 'checkbox':
                    for (var i = 0, j = 0; i < objCount; i++) {
                        if (obj[i].checked) {
                            if (j > 0)
                                valStr = valStr + ',';
                            valStr = valStr + obj[i].value;
                            j += 1;
                        }
                    }
                    break;
                case 'radio':
                    for (var i = 0, j = 0; i < objCount; i++) {
                        if (obj[i].checked) {
                            if (j > 0)
                                valStr = valStr + ',';
                            valStr = valStr + obj[i].value;
                            j += 1;
                        }
                    }
                    break;
                default :
                for (var i = 0; i < objCount; i++) {
                    if (i > 0)
                        valStr = valStr + ',';
                    valStr = valStr + obj[i].value;
                }
            }
        }
        return valStr;
    } else {
        return '';
    }
}
function GetMonthNM(M) {
    var MNM
    /*轉換月份名稱*/
    switch (M) {
        case 1:
            MNM = 'JAN';
            break;
        case 2:
            MNM = 'FEB';
            break;
        case 3:
            MNM = 'MAR';
            break;
        case 4:
            MNM = 'APR';
            break;
        case 5:
            MNM = 'MAY';
            break;
        case 6:
            MNM = 'JUN';
            break;
        case 7:
            MNM = 'JUL';
            break;
        case 8:
            MNM = 'AUG';
            break;
        case 9:
            MNM = 'SEP';
            break;
        case 10:
            MNM = 'OCT';
            break;
        case 11:
            MNM = 'NOV';
            break;
        case 12:
            MNM = 'DEC';
            break;
    }
    return MNM;
}
function GetMonthNM_LONG(M) {
    var MNM
    /*轉換月份名稱*/
    switch (M) {
        case 1:
            MNM = 'January';
            break;
        case 2:
            MNM = 'February';
            break;
        case 3:
            MNM = 'March';
            break;
        case 4:
            MNM = 'April';
            break;
        case 5:
            MNM = 'May';
            break;
        case 6:
            MNM = 'June';
            break;
        case 7:
            MNM = 'July';
            break;
        case 8:
            MNM = 'August';
            break;
        case 9:
            MNM = 'September';
            break;
        case 10:
            MNM = 'October';
            break;
        case 11:
            MNM = 'November';
            break;
        case 12:
            MNM = 'December';
            break;
    }
    return MNM;
}
function GetMonthNM_CH(M) {
    var MNM
    /*轉換月份名稱*/
    switch (M) {
        case 1:
            MNM = '一月';
            break;
        case 2:
            MNM = '二月';
            break;
        case 3:
            MNM = '三月';
            break;
        case 4:
            MNM = '四月';
            break;
        case 5:
            MNM = '五月';
            break;
        case 6:
            MNM = '六月';
            break;
        case 7:
            MNM = '七月';
            break;
        case 8:
            MNM = '八月';
            break;
        case 9:
            MNM = '九月';
            break;
        case 10:
            MNM = '十月';
            break;
        case 11:
            MNM = '十一月';
            break;
        case 12:
            MNM = '十二月';
            break;
    }
    return MNM;
}

function getRelativeOption(mSel, sSel, DataSrc, para, keep1stOpt, defaultOptText, str) {

    //取得相關選項

    //主要用在兩個關聯性的Select上，如縣市與區域...


    var omSel = $('#' + mSel);

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
        //alert(JSON.stringify(p));

        p.mcode = omSel.val();

        $.post(
                DataSrc,
                p,
                function (xml) {

                    if ($('resu', xml).text() == "1") {
                        osSel.attr('disabled', false);

                        $('option' + (keep1stOpt ? ':gt(0)' : ''), osSel).remove();


                        if ($('option:first', osSel).length > 0) {

                            //osSel.append('<option value="">'+(defaultOptText==''?str:defaultOptText)+'</option>');


                        } else {

                            osSel.append('<option value="">' + (defaultOptText == '' ? str : defaultOptText) + '</option>');

                        }

                        $('data', xml).each(function () {
                            //alert($('desc',this).text());
                            osSel.append('<option value="' + $('code', this).text() + '">' + $('desc', this).text() + '</option>');

                        });
                        //alert($('option',osSel).length);

                        if ($('option', osSel).length == 1) {
                            osSel.attr('disabled', true);
                        }

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
function formatNumber(obj) {
    //電話號碼僅能輸入數字與-
    n = obj.val();
    n = n.toString().replace(/[^0-9-]/g, '')
    n = n.toString().replace(/\$|\,/g, '');
    //alert(n);
    n += "";
    obj.val(n);
}

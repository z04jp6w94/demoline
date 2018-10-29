function checkAll(FormName) {
    if ($("#checkAll").prop("checked")) {
//console.log(FormName.elements);
//console.log(FormName.elements.length);
        for (var i = 0; i < FormName.elements.length; i++) {
//console.log(FormName.elements[i]);
            if (FormName.elements[i].type == "checkbox")
                FormName.elements[i].checked = true;
        }
        $("#CmBtnDel, #CmBtnSave").show();
    } else {
        for (var i = 0; i < FormName.elements.length; i++) {
//console.log(FormName.elements[i]);
            if (FormName.elements[i].type == "checkbox")
                FormName.elements[i].checked = false;
        }
        $("#CmBtnDel, #CmBtnSave").hide();
    }
}
function checkAll2(ChkAll_id, ChkName) {
    if ($("#" + ChkAll_id).prop("checked")) {
        $("input[name='" + ChkName + "[]']").each(function () {
            $(this).prop("checked", true);
        })
    } else {
        $("input[name='" + ChkName + "[]']").each(function () {
            $(this).prop("checked", false); //把所有的核方框的property都取消勾選
        })
    }
}
function checkSingle(FormName) {
    var cmBtnFlag = false;
    var checkAllFlag = true;
    for (var i = 0; i < FormName.elements.length; i++) {
        if (FormName.elements[i].type == "checkbox") {
            if (FormName.elements[i].checked == true) {
                cmBtnFlag = true;
            }
            if (FormName.elements[i].checked == false) {
                checkAllFlag = false;
            }
        }
    }
    if (cmBtnFlag) {
        $("#CmBtnDel, #CmBtnSave").show();
    } else {
        $("#CmBtnDel, #CmBtnSave").hide();
    }
    if (checkAllFlag) {
        $("#checkAll").prop("checked", true);
    } else {
        $("#checkAll").prop("checked", false);
    }
}

function isFormDataEmpty(obj, msg) {
    var dataType = obj.type;
    switch (dataType) {
        case "text":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                alert("請填寫" + msg);
                obj.focus();
                return true;
            }
            return false;
        case "password":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                alert("請填寫" + msg);
                obj.focus();
                return true;
            }
            return false;
        case "textarea":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                alert("請填寫" + msg);
                obj.focus();
                return true;
            }
            return false;
        case "select-one":
            if (obj.value == '') {
                alert("請選取" + msg);
                obj.focus();
                return true;
            }
            return false;
        case "select-multiple":
            if (!obj.selected) {
                alert("未完成" + msg);
                obj.focus();
                return true;
            }
            return false;
        case "hidden":
            obj.value.replace(' ', '');
            if (obj.value.length == 0) {
                alert("請輸入" + msg);
                return true;
            }
            return false;
        case "file":
            if (obj.files.length == 0) {
                alert("請選擇" + msg);
                return true;
            }
            return false;
        case 'checkbox':
            if (!obj.checked) {
                alert("請選取" + msg);
                return true;
            }
            return false;
        case 'radio':
            if (!obj.checked) {
                alert("請選取" + msg);
                return true;
            }
            return false;
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
function isNumberFormatCorrect(obj, msg) {
    if (!Number(obj.value)) {
        alert("請輸入正確的數字格式(" + msg + ")");
        obj.focus();
        return true;
    }
    return false;
}
function isNumberRangeCorrect(obj, msg, low, high) {
    if (obj.value < low || obj.value > high) {
        alert("請輸入正確的數字範圍(" + msg + ")");
        obj.focus();
        return true;
    }
    return false;
}
function chkFormBeforeSubmit(formName, chkboxName, msg) {
    var chkFlg = false;
    for (var i = 0; i < formName.elements.length; i++) {
        if (formName.elements[i].name.indexOf(chkboxName) >= 0) {
            if (formName.elements[i].checked == true) {
                chkFlg = true;
                break;
            }
        }
    }
    if (chkFlg == true) {
        if (confirm("確定要" + msg + "選取的項目嗎?")) {
            formName.submit();
        }
    } else {
        alert("請先選取要" + msg + "的資料");
    }
}
/**
 * 格式化
 * @param   num 要轉換的數字
 * @param   pos 指定小數第幾位做四捨五入
 */
function format_float(num, pos) {
    var size = Math.pow(10, pos);
    return Math.round(num * size) / size;
}
/*
 *  
 */
function check_mail(obj) {
    var email = obj.value;
    if (email == '') {
        alert('請輸入電子信箱');
        obj.focus();
        return true;
    } else {
        var emailRegxp = /^([\w]+)(.[\w]+)*@([\w]+)(.[\w]{2,3}){1,2}$/;
        if (emailRegxp.test(email) != true) {
            alert('電子信箱格式錯誤');
            obj.focus();
            return true;
        }
        return false;
    }
}
/**
 * 預覽圖
 * @param   input 輸入 input[type=file] 的 this
 */
function uploadFilePreviewIMG(input, size = "") {
// 若有選取檔案
    if (input.files && input.files[0]) {
// 建立一個物件，使用 Web APIs 的檔案讀取器(FileReader 物件) 來讀取使用者選取電腦中的檔案
        var reader = new FileReader();
        // 事先定義好，當讀取成功後會觸發的事情
        reader.onload = function (event) {
            //console.log(event);
            //console.log(event.target.result);
            //這裡看到的 e.target.result 物件，是使用者的檔案被 FileReader 轉換成 base64 的字串格式，
            //在這裡我們選取圖檔，所以轉換出來的，會是如 『data:image/jpeg;base64,.....』這樣的字串樣式。
            //我們用它當作圖片路徑就對了。
            var ImgInfo = event.target.result;
            var ImgType = ImgInfo.split(";")[0].substr(5);
            if (ImgType.indexOf("jpeg") === -1 && ImgType.indexOf("png") === -1 && ImgType.indexOf("gif") === -1) {
                $('#uploadFileDelete').html("<span class='btn btn-danger' id='uploadFileDeleteButton'>刪除</span>");
                $('#uploadFilePreview').html("<img src=''><img>");
                $('#uploadFileMsg').text("請選擇正確的檔案格式(jpg/png/gif)");
                return;
            } else {
                $('#uploadFileDelete').html("<span class='btn btn-danger' id='uploadFileDeleteButton'>刪除</span>");
                $('#uploadFilePreview').html("<img src='" + ImgInfo + "' style='width:300px;'><img>");
                //檔案大小，把 Bytes 轉換為 KB//$('#uploadFileMsg').text("檔案大小：" + format_float(event.total / 1024, 2) + " KB");
                $('#uploadFileMsg').text("");
                if (format_float((event.total / 1024) / 1024, 2) > 1) { //MB
                    $('#uploadFileMsg').text("請上傳不超過1MB的圖片!");
                }
            }
        }
        // 因為上面定義好讀取成功的事情，所以這裡可以放心讀取檔案
        reader.readAsDataURL(input.files[0]);
}
}
/**
 * LINE Menu Use
 * @param   input 輸入 input[type=file] 的 this
 */
function uploadFileLineRichMenuIMG(input) {
// 若有選取檔案
    if (input.files && input.files[0]) {
// 建立一個物件，使用 Web APIs 的檔案讀取器(FileReader 物件) 來讀取使用者選取電腦中的檔案
        var reader = new FileReader();
        // 事先定義好，當讀取成功後會觸發的事情
        reader.onload = function (event) {
            //console.log(event);
            //console.log(event.target.result);
            //這裡看到的 e.target.result 物件，是使用者的檔案被 FileReader 轉換成 base64 的字串格式，
            //在這裡我們選取圖檔，所以轉換出來的，會是如 『data:image/jpeg;base64,.....』這樣的字串樣式。
            //我們用它當作圖片路徑就對了。
            var ImgInfo = event.target.result;
            var ImgType = ImgInfo.split(";")[0].substr(5);
            if (ImgType.indexOf("jpeg") === -1 && ImgType.indexOf("png") === -1) {
                $('#uploadFileDelete').html("<span class='btn btn-danger' id='uploadFileDeleteButton'>刪除</span>");
                $('#uploadFilePreview').html("<img src=''><img>");
                $('#uploadFileMsg').text("請選擇正確的檔案格式(jpg/png)");
                return;
            } else {
                $('#uploadFileDelete').html("<span class='btn btn-danger' id='uploadFileDeleteButton'>刪除</span>");
                $('#uploadFilePreview').html("<img src='" + ImgInfo + "' style='width:300px;border:1px black solid;'><img>");
                //檔案大小，把 Bytes 轉換為 KB//$('#uploadFileMsg').text("檔案大小：" + format_float(event.total / 1024, 2) + " KB");
                $('#uploadFileMsg').text("");
                if (format_float((event.total / 1024) / 1024, 2) > 1) { //MB
                    $('#richmenu_img_status').val('N');
                    $('#uploadFileMsg').text("請上傳不超過1MB的圖片!");
                } else {
                    /* 限制長寬 */
                    var img = new Image;
                    img.onload = function () {
                        var img_width = img.width;
                        var img_height = img.height;
                        if (img_width != '2500' && img_height != '1686') {
                            $('#richmenu_img_status').val('N');
                            $('#uploadFileMsg').text("請上傳寬度: 2500px 高度: 1686px圖檔");
                        } else {
                            $('#richmenu_img_status').val('Y');
                            $('#uploadFileMsg').text("");
                        }

                    };
                    img.src = reader.result;
                }
            }
        }
// 因為上面定義好讀取成功的事情，所以這裡可以放心讀取檔案
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * 預覽圖
 * @param   input 輸入 input[type=file] 的 this
 */
function uploadFilePreviewIMGS(input) {
// 若有選取檔案
    if (input.files && input.files[0]) {
// 建立一個物件，使用 Web APIs 的檔案讀取器(FileReader 物件) 來讀取使用者選取電腦中的檔案
        var reader = new FileReader();
        // 事先定義好，當讀取成功後會觸發的事情
        reader.onload = function (event) {
            var number = '';
            number = input.id.replace(/lrcm_img/, "");
            //console.log(event);
            //console.log(event.target.result);
            //這裡看到的 e.target.result 物件，是使用者的檔案被 FileReader 轉換成 base64 的字串格式，
            //在這裡我們選取圖檔，所以轉換出來的，會是如 『data:image/jpeg;base64,.....』這樣的字串樣式。
            //我們用它當作圖片路徑就對了。
            var ImgInfo = event.target.result;
            var ImgType = ImgInfo.split(";")[0].substr(5);
            if (ImgType.indexOf("jpeg") === -1 && ImgType.indexOf("png") === -1 && ImgType.indexOf("gif") === -1) {
                $('#uploadFileDelete' + number).html("<span class='btn btn-danger uploadFileDeleteButton' id='uploadFileDeleteButton" + number + "'>刪除</span>");
                $('#uploadFilePreview' + number).html("<img src=''><img>");
                $('#uploadFileMsg' + number).text("請選擇正確的檔案格式(jpg/png/gif)");
                return;
            } else {
                $('#uploadFileDelete' + number).html("<span class='btn btn-danger uploadFileDeleteButton' id='uploadFileDeleteButton" + number + "'>刪除</span>");
                $('#uploadFilePreview' + number).html("<img src='" + ImgInfo + "' style='width:300px;height:199px;'><img>");
                //檔案大小，把 Bytes 轉換為 KB//$('#uploadFileMsg').text("檔案大小：" + format_float(event.total / 1024, 2) + " KB");
                $('#uploadFileMsg' + number).text("");
            }
        }
// 因為上面定義好讀取成功的事情，所以這裡可以放心讀取檔案
        reader.readAsDataURL(input.files[0]);
    }
}
/**
 * 上傳影片mp4
 * @param   input 輸入 input[type=file] 的 this
 */
function uploadFilePreviewVideo(input) {
// 若有選取檔案
    if (input.files && input.files[0]) {
// 建立一個物件，使用 Web APIs 的檔案讀取器(FileReader 物件) 來讀取使用者選取電腦中的檔案
        var reader = new FileReader();
        // 事先定義好，當讀取成功後會觸發的事情
        reader.onload = function (event) {
            //console.log(event);
            //console.log(event.target.result);
            //這裡看到的 e.target.result 物件，是使用者的檔案被 FileReader 轉換成 base64 的字串格式，
            //在這裡我們選取圖檔，所以轉換出來的，會是如 『data:image/jpeg;base64,.....』這樣的字串樣式。
            //我們用它當作圖片路徑就對了。
            var ImgInfo = event.target.result;
            var ImgType = ImgInfo.split(";")[0].substr(5);
            if (ImgType.indexOf("mp4") === -1) {
                $('#uploadVideoDelete').html("<span class='btn btn-danger' id='uploadVideoDeleteButton'>刪除</span>");
                $('#uploadVideoPreview').html("");
                $('#uploadVideoMsg').text("請選擇正確的檔案格式(mp4)");
                return;
            } else {
                $('#uploadVideoDelete').html("<span class='btn btn-danger' id='uploadVideoDeleteButton'>刪除</span>");
                $('#uploadVideoPreview').html("");
                //檔案大小，把 Bytes 轉換為 KB//$('#uploadVideoMsg').text("檔案大小：" + format_float(event.total / 1024, 2) + " KB");
                $('#uploadVideoMsg').text("");
                if (format_float((event.total / 1024) / 1024, 2) > 10) { //MB
                    $('#uploadVideoMsg').text("上傳失敗!請上傳不超過10MB的影片!");                    
                }else{
                    $('#uploadVideoPreview').html("上傳成功!");
                    $('#uploadVideoMsg').text("");          
                }
            }
        }
        // 因為上面定義好讀取成功的事情，所以這裡可以放心讀取檔案
        reader.readAsDataURL(input.files[0]);
    }
}

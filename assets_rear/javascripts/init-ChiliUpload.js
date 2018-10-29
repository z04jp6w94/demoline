//¹Ï¤ùÁY¤p
function ImgWH() {
    var duration = 100;
    $("img").each(function (i) {
        if ($(this).attr("alumb") == "true") {
            $(this).css({"width": "", "height": ""});
            var imgW = $(this).width();
            var imgH = $(this).height();
            var _w = $(this).attr("_w");
            var _h = $(this).attr("_h");
            if (_w == undefined) {
                w = imgW;
            } else {
                if (imgW > _w) {
                    w = _w / imgW;
                } else {
                    w = 1;
                }
            }
            if (_h == undefined) {
                h = imgH;
            } else {
                if (imgH > _h) {
                    h = _h / imgH;
                } else {
                    h = 1;
                }
            }
            var pre = 1;
            if (w > h) {
                pre = h;
            } else {
                pre = w;
            }
            $(this).width(imgW * pre).height(imgH * pre);
            $(this).delay(i * duration).fadeIn(500);
            $(this).removeAttr("alumb");
        }
    });
}
function deldim(no) {
    no = no || "";
    $("#uploadzone" + no + " .loadingdel").find("a").live("click", function () {

        var _index = $(this).parent().parent().index();
        var _DelUrl = '/UpLoad/FileDelete.php';
        Shadowbox.open({
            player: 'iframe',
            content: '/CommonPage/PopupWarningWindow.php?CbFN=DeleteUpLoadFile&CbIndex=' + _index + '&CbDelUrl=' + _DelUrl + '&no=' + no,
            width: 390,
            height: 150
        });
    });

}
// - 上傳狀態切換	
function ChkRunning(id) {
    if (id == undefined) {
        id = '';
    }
    switch (UploadType) {
        case 1:
            if ($('#uploadzone' + id + ' li.img').length >= 1) {
                $("#btn_uploadfile" + id).hide();
                $("#btn_uploading" + id).hide();
                $("#FileUpLoad" + id).css({'visibility': 'hidden'});
            } else {
                $("#btn_uploadfile" + id).show();
                $("#btn_uploading" + id).hide();
                $("#FileUpLoad" + id).css({'visibility': 'visible'});
            }
            break;
        case 2:
            if (UploadCount <= 0) {
                UploadCount = 0;
                $("#btn_uploadfile" + id).show();
                $("#btn_uploading" + id).hide();
                $(".chiliupload").show();
                $("#FileUpLoad" + id).css({'visibility': 'hidden'});
            } else {
                $("#btn_uploadfile" + id).hide();
                $("#btn_uploading" + id).show();
                $(".chiliupload").hide();
                $("#FileUpLoad" + id).css({'visibility': 'visible'});
            }
            break;
    }
}
function GetFileTypeShowImg(filePath, fileName) {
    var fileEx = fileName.substr(fileName.lastIndexOf('.') + 1, fileName.length);				// 副檔名
    var fileView = "";																			// 顯示方式
    switch (fileEx.toLowerCase()) {
        case 'jpeg':
        case 'jpg':
        case 'gif':
        case 'png':
            fileView = filePath + fileName;
            break;
        case 'doc':
        case 'docx':
            fileView = '/Upload/BackOffice/UI/Icon_UploadWord.png';
            break;
        case 'xls':
        case 'xlsx':
            fileView = '/Upload/BackOffice/UI/Icon_UploadExcel.png';
            break;
        case 'pdf':
            fileView = '/Upload/BackOffice/UI/Icon_UploadPdf.png';
            break;
    }
    return fileView;
}

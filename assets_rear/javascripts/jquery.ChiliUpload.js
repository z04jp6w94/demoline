
var NowNum = '';
var UploadImgListH = 170;
(function ($) {
    $.fn.chiliupload = function (params) {
        if (typeof params == "object" && !params.length) {
            var settings = $.extend({
                flasID: 'FileUpLoad', // flash 物件 ID
                path: 'FileUpLoad.swf', // swf 檔案位置
                type: 1, // 上傳類型
                q: "", // 數量限制
                ex: "*.png;*.jpg;*.gif", // 檔案類型
                size: "", // 檔案大小限制
                cb: 1, // 上傳方式
                url: "", // 上傳程式位置
                htmlid: ""															// Html id
            }, params);
            return this.each(function () {

                var o = $(this);
                if (o.is(':visible')) {
                    ZeroClipboard.setMoviePath(settings.path);
                    var clip = new ZeroClipboard.Client();
                    clip.glue(o[0], o.parent()[0], settings.type, settings.q, settings.ex, settings.size, settings.cb, settings.url, settings.flasID, settings.htmlid);
                    //NowNum = settings.htmlid.replace('uploadzone',''); 
                    //alert(NowNum);
                    $(window).bind('load resize', function () {
                        clip.reposition();
                    });
                }
            });
        } else if (typeof params == "string") {
            return this.each(function () {
                var o = $(this);
                params = params.toLowerCase();
                var chiliuploadId = o.data('chiliuploadId');
                var clipElm = $('#' + chiliuploadId + '.chiliupload');
                if (params == "remove") {
                    clipElm.remove();
                    o.removeClass('active hover');
                } else if (params == "hide") {
                    clipElm.hide();
                    o.removeClass('active hover');
                } else if (params == "show") {
                    clipElm.show();
                }
            });
        }
    }
})(jQuery);

var ZeroClipboard = {
    version: "1.0.7",
    clients: {},
    moviePath: 'FileUpLoad.swf',
    nextId: 1,
    $: function (thingy) {
        if (typeof (thingy) == 'string')
            thingy = document.getElementById(thingy);
        if (!thingy.addClass) {
            thingy.hide = function () {
                this.style.display = 'none';
            };
            thingy.show = function () {
                this.style.display = '';
            };
            thingy.addClass = function (name) {
                this.removeClass(name);
                this.className += ' ' + name;
            };
            thingy.removeClass = function (name) {
                var classes = this.className.split(/\s+/);
                var idx = -1;
                for (var k = 0; k < classes.length; k++) {
                    if (classes[k] == name) {
                        idx = k;
                        k = classes.length;
                    }
                }
                if (idx > -1) {
                    classes.splice(idx, 1);
                    this.className = classes.join(' ');
                }
                return this;
            };
            thingy.hasClass = function (name) {
                return !!this.className.match(new RegExp("\\s*" + name + "\\s*"));
            };
        }
        return thingy;
    },

    setMoviePath: function (path) {
        // set path to ZeroClipboard.swf
        this.moviePath = path;
    },

    dispatch: function (id, eventName, args) {
        // receive event from flash movie, send to client		
        var client = this.clients[id];
        if (client) {
            client.receiveEvent(eventName, args);
        }
    },

    register: function (id, client) {
        // register new client to receive events
        this.clients[id] = client;
    },

    getDOMObjectPosition: function (obj, stopObj) {
        // get absolute coordinates for dom element
        var info = {
            left: 0,
            top: 0,
            width: obj.width ? obj.width : obj.offsetWidth,
            height: obj.height ? obj.height : obj.offsetHeight
        };

        if (obj && (obj != stopObj)) {
            info.left += obj.offsetLeft;
            info.top += obj.offsetTop;
        }
        return info;
    },

    Client: function (elem) {
        // constructor for new simple upload client
        this.handlers = {};

        // unique ID
        this.id = ZeroClipboard.nextId++;
        this.movieId = 'ZeroClipboardMovie_' + this.id;

        // register client with singleton to receive flash events
        ZeroClipboard.register(this.id, this);
        // create movie
        if (elem)
            this.glue(elem);
    }
};

ZeroClipboard.Client.prototype = {

    id: 0,
    glue: function (elem, appendElem, uptype, upq, upex, upsize, upcb, upurl, upflasID, uphtmlid) {
        // glue to DOM element
        // elem can be ID or actual DOM element object
        this.domElement = ZeroClipboard.$(elem);

        // float just above object, or zIndex 99 if dom element isn't set
        var zIndex = 99;
        if (this.domElement.style.zIndex) {
            zIndex = parseInt(this.domElement.style.zIndex, 10) + 1;
        }

        if (typeof (appendElem) == 'string') {
            appendElem = ZeroClipboard.$(appendElem);
        } else if (typeof (appendElem) == 'undefined') {
            appendElem = document.getElementsByTagName('body')[0];
        }

        // find X/Y position of domElement
        var box = ZeroClipboard.getDOMObjectPosition(this.domElement, appendElem);

        // create floating DIV above element
        this.div = document.createElement('div');
        this.div.className = "chiliupload";
        this.div.id = "chiliupload-" + this.movieId;
        $(this.domElement).data('chiliuploadId', 'chiliupload-' + this.movieId);
        var style = this.div.style;
        style.position = 'absolute';
        style.left = '' + box.left + 'px';
        style.top = '' + box.top + 'px';
        style.width = '' + box.width + 'px';
        style.height = '' + box.height + 'px';
        style.zIndex = zIndex;

        if (typeof (stylesToAdd) == 'object') {
            for (addedStyle in stylesToAdd) {
                style[addedStyle] = stylesToAdd[addedStyle];
            }
        }
        //style.backgroundColor = '#f00'; // debug
        appendElem.appendChild(this.div);

        this.div.innerHTML = this.getHTML(box.width, box.height, uptype, upq, upex, upsize, upcb, upurl, upflasID, uphtmlid);

    },

    getHTML: function (width, height, uptype, upq, upex, upsize, upcb, upurl, upflasID, uphtmlid) {
        // return HTML for movie
        var html = '';
        var flashvars = 'id=' + this.id + '&width=' + width + '&height=' + height;
        flashvars += '&ul_type=' + uptype + '&ul_q=' + upq + '&ul_ex=' + upex + '&ul_size=' + upsize + '&ul_cb=' + upcb + '&ul_url=' + upurl + '&htmlid=' + uphtmlid;


        if (navigator.userAgent.match(/MSIE/)) {
            // IE gets an OBJECT tag
            var protocol = location.href.match(/^https/i) ? 'https://' : 'http://';
            html += '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="' + protocol + 'download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="' + width + '" height="' + height + '" id="' + upflasID + '" align="middle"><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="false" /><param name="movie" value="' + ZeroClipboard.moviePath + '" /><param name="loop" value="false" /><param name="menu" value="false" /><param name="quality" value="best" /><param name="bgcolor" value="#ffffff" /><param name="flashvars" value="' + flashvars + '"/><param name="wmode" value="transparent"/></object>';
        } else {
            // all other browsers get an EMBED tag
            //html += '<embed id="' + this.movieId + '" src="' + ZeroClipboard.moviePath + '" loop="false" menu="false" quality="best" bgcolor="#ffffff" width="' + width + '" height="' + height + '" name="' + this.movieId + '" align="middle" allowScriptAccess="always" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="' + flashvars + '" wmode="transparent"></embed>';
            html += '<div id="' + upflasID + '"><embed id="' + upflasID + '" name="' + upflasID + '" src="' + ZeroClipboard.moviePath + '" loop="false" menu="false" quality="best" bgcolor="#ffffff" width="' + width + '" height="' + height + '" align="middle" allowScriptAccess="always" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="' + flashvars + '" wmode="transparent"></embed></div>';
        }

        return html;
    }
}

function getFlashMovieObject(movieName) {
    if (window.document[movieName]) {
        return window.document[movieName];
    }
    if (navigator.appName.indexOf("Microsoft Internet") == -1) {
        if (document.embeds && document.embeds[movieName])
            return document.embeds[movieName];
    } else {
        return document.getElementById(movieName);
    }
}
// - upload items
function ul_cb_select(Sort, Name, Size, S, htmlid) {
    switch (parseInt(S)) {
        case 1:
            var _html = '';
            var Id = 'li' + Sort;
            _html += '<li class="img imgbg" id="' + Id + '" data-size="' + Size + '" data-path="" data-name="" data-no="" data-num="' + htmlid.replace('uploadzone', '') + '">';
            _html += '<img src="" alumb="true" _h="' + UploadImgListH + '" />';
            //_html += '<div class="loadingbar" id="loadingbar' + Id + '"></div>';
            _html += '<div class="loadingbar"><div class="loadingbarcolor" id="loadingbar' + Sort + '"></div></div>';
            _html += '<div class="loadingdel" id="del' + htmlid.replace('uploadzone', '') + '"><a class="LFFFFFF SetCursor">刪除</a></div>';
            _html += '<input name="upText' + htmlid.replace('uploadzone', '') + '[]" id="upText' + Id + '" type="hidden" value="" />';
            _html += '</li>';
            //alert("#"+htmlid+" .uploadnav");
            /*MOMO新增：兩個上傳檔按鈕進度條會衝突，故多一層uploadzone判斷*/
            if (htmlid != '') {
                $("#" + htmlid + " .uploadnav").append(_html);
                ChkRunning(htmlid.replace('uploadzone', ''));
            } else {
                $(".uploadnav").append(_html);
            }
            UploadCount = UploadCount + 1;
            break;
    }
}
// - upload status
function ul_cb_status(fileArray, S) {
    switch (parseInt(S)) {
        //上傳中
        case 1:
            break;
            //上傳完成
        case 2:
            var fileArray = fileArray.split(",");
            var fileSort = fileArray[0].replace(/\s/g, '');												// 序號
            var filePath = fileArray[1];																// 路徑
            var fileName = fileArray[2];																// 檔名
            var fileNo = fileArray[3];																	// 資料序號
            var fileEx = fileName.substr(fileName.lastIndexOf('.') + 1, fileName.length);				// 副檔名
            //var Id = 'li' + fileSort;																	// ID
            var Id = 'li' + fileSort;																	// ID
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
                    fileView = '/UpLoad/BackOffice/UI/Icon_UploadWord.png';
                    break;
                case 'xls':
                case 'xlsx':
                    fileView = '/UpLoad/BackOffice/UI/Icon_UploadExcel.png';
                    break;
                case 'pdf':
                    fileView = '/UpLoad/BackOffice/UI/Icon_UploadPdf.png';
                    break;
                case 'rar':
                case 'zip':
                    fileView = '/UpLoad/BackOffice/UI/Icon_UploadZip.png';
                    break;
                default:
                    fileView = '/UpLoad/BackOffice/UI/Icon_UploadFile.jpg';
            }
            $("#" + Id + " img").removeAttr("width").removeAttr("height").attr("src", fileView).load(function () {
                //ImgWH();
                //$("#loadingbar" + fileSort).hide();															// 關閉上傳進度條
                /*MOMO新增：兩個上傳檔按鈕進度條會衝突，故多一層uploadzone判斷*/
                $("#uploadzone" + $("#" + Id).attr("data-num") + " #loadingbar" + fileSort).hide();															// 關閉上傳進度條
                $("#" + Id).delay(500).removeClass("imgbg");											// 移除背景預設圖
                $("#" + Id).attr("data-path", filePath).attr("data-name", fileName);					// 設定檔案資訊
                $("#" + Id).attr("data-no", fileNo);													// 設定檔案資訊
                $("#" + Id + " .loadingdel a").fadeIn();												// 顯示刪除鈕
                $("#upText" + Id).val(fileName);														// input 欄位值
                $("#" + Id + ", #loadingbar" + Id + ", #upText" + Id).attr("id", "");					// 移除 Id 資訊
            });
            UploadCount = UploadCount - 1;
            if (UploadCount === 0) {
                setTimeout('ImgWH()', 500);
            }
            //alert(fileSort);
            break;
            //上傳錯誤
        case 3:
            UploadCount = UploadCount - 1;
            break;
            //訊息
        case 4:
            UploadCount = UploadCount - 1;
            break;
    }

    ChkRunning();
}
// - upload callback
function ul_cb_kb(Sort, kb, per) {
    var Id = 'li' + Sort;
    imgkb = $("#" + Id).attr("data-size");
    /*
     barsize = imgkb / 80;
     cbkb = kb / barsize;
     if(cbkb > 80){
     cbkb = 80;
     }
     $("#loadingbar" + Id).css("width", cbkb);
     */
    barsize = imgkb / 100;
    cbkb = kb / barsize;
    if (cbkb > 100) {
        cbkb = 100;
    }
    $("#loadingbar" + Id).css("width", cbkb + "%");
}
// - upload over msg
function upload_over_msg() {
    alert("您的上傳檔案數量超過限制，將會上傳前" + UploadLimit + "個檔案。");
}
// - 移除上傳檔案
function DeleteUpLoadFile(CbIndex, CbDelUrl, no) {
    //alert("#uploadzone"+no+" .uploadnav li");
    $("#uploadzone" + no + " .uploadnav li").eq(CbIndex).fadeOut(250, function () {
        Shadowbox.close();
        var _this = $(this);
        var _path = _this.attr("data-path");
        var _name = _this.attr("data-name");
        var _no = _this.attr("data-no");
        $.post(
                CbDelUrl,
                {path: _path, name: _name, no: _no},
                function (xml) {
                    if ($('resu', xml).text() == '1') {
                        _this.remove();
                        ChkRunning(no);
                    } else {
                        alert("刪除失敗");
                    }
                }
        );
    });
}




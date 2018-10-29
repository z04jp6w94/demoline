// 載入需要套件
        $.getScript("/assets_rear/javascripts/blur.js");
// document ready
        var _UsWidth = "";
	var _UsHeight = "";
	var _UsDocWidth = "";
	var _UsDocHeight = "";
	$(document).ready(function(){
		_UsWidth = $(window).width();														//User �ù��e
		_UsHeight = $(window).height();														//User �ù���
		_UsDocWidth = $(document).width();													//Html �e
		_UsDocHeight = $(document).height();												//Html ��
		SetHW();
    //移除連結虛線
		$("a").focus(function(){
			$(this).blur();
		});
    //設定treeview高度
		$("#FrameLineImg").click(function(){
			MoveW();
		}),$("#FrameLine").click(function(){
			MoveW();
		});
    //TreeView - 顯示控制	
		$numberSpan = $("#TreeView").find('.TreeviewSpan');
		$numberSysFile = $("#TreeView").find('.TreeviewSysFile');
		$numberFncFile = $("#TreeView").find('.TreeviewFncFile');
    //TreeView - 設定功能選項點選狀態
		$numberSpan.click(function(){
			$(".TreeviewSpan").css({background: ''});
			$numberSpan.eq($(".TreeviewSpan").index(this)).css({background: 'url(/assets_rear/images/Bg_02.gif)'});
		});
    //TreeView - 設定系統資料夾開啟關閉
                $numberSysFile.click(function(){
			$TreeId = $("#TreeView").find($(this).attr('treeid'));
			$TreeImgId = $("#TreeView").find($(this).attr('treeimgid'));
			if($(this).attr('class') == "TreeviewSysFile"){
				if($TreeId.css('display') == 'none'){
					$TreeId.stop().slideDown(250);
//					$TreeId.css({display: "block"});
					$TreeImgId.attr({src: "/assets_rear/images/s4-14.gif"});
				}else{
					$TreeId.stop().slideUp(250);
//					$TreeId.css({display: "none"});
					$TreeImgId.attr({src: "/assets_rear/images/s4-15.gif"});
				}
			}
		});
    //TreeView - 設定功能資料夾開啟關閉
                $numberFncFile.click(function(){
			$TreeId = $("#TreeView").find($(this).attr('treeid'));
			$TreeImgId = $("#TreeView").find($(this).attr('treeimgid'));
                        $TreeFileimgid = $("#TreeView").find($(this).attr('treeFileimgid'));                    
			if($TreeId.css('display') == 'none'){
//				$TreeId.css({display: "block"});
				$TreeId.stop().slideDown(250);
				$TreeImgId.attr({src: "/assets_rear/images/s4-20.gif"});
				$TreeFileimgid.attr({src: "/assets_rear/images/s4-18.gif"});
			}else{
//				$TreeId.css({display: "none"});
				$TreeId.stop().slideUp(250);
				$TreeImgId.attr({src: "/assets_rear/images/s4-16.gif"});
				$TreeFileimgid.attr({src: "/assets_rear/images/s4-17.gif"});
			}
		});
    //TreeView - 功能選項移過
		$numberSpan.hover(function(){
			if($numberSpan.eq($(".TreeviewSpan").index(this)).css('background-image') == 'none'){
				$numberSpan.eq($(".TreeviewSpan").index(this)).css({background: '#e9eef5'});
			}
		}, function(){
			if($numberSpan.eq($(".TreeviewSpan").index(this)).css('background-image') == 'none'){
				$numberSpan.eq($(".TreeviewSpan").index(this)).css({background: ''});
			}
		});
    //TreeView - 系統資料夾移過
		$numberSysFile.hover(function(){
			$(".TreeviewSysFile").css({background: ''});
	//		$numberSysFile.eq($(".TreeviewSysFile").index(this)).css({background: '#f6f6f6'});
			$numberSysFile.eq($(".TreeviewSysFile").index(this)).css({background: 'url(/assets_rear/images/Bg_01.gif)'});
		}, function(){
			$(".TreeviewSysFile").css({background: ''});
		});
    //TreeView - 功能資料夾移過
		$numberFncFile.hover(function(){
			$(".TreeviewFncFile").css({background: ''});
			//$numberFncFile.eq($(".TreeviewFncFile").index(this)).css({background: '#f6f6f6'});
			$numberFncFile.eq($(".TreeviewFncFile").index(this)).css({background: 'url(/assets_rear/images/Bg_01.gif)'});
		}, function(){
			$(".TreeviewFncFile").css({background: ''});
		});
    //一般畫面控制
    //取得 #MainTopMenu 及其 top 值
    var $MainTopMenu = $('#MainTopMenu'),
            _top = $MainTopMenu.offset().top,
//			_ShHeight = $("#MainTip").height(),
            $MainTitle = $('#MainTitle');
    //網頁捲軸捲動時
    var $win = $(window).scroll(function () {
        // 如果現在的 scrollTop 大於原本 #MainTopMenu 的 top 時
        if ($win.scrollTop() > _top) {
            // 如果 $cart 的座標系統不是 fixed 的話
            if ($MainTopMenu.css('position') != 'fixed') {
                // 設定座標系統為 fixed
                $MainTopMenu.css({
                    position: 'fixed',
                    top: 0
                }),
                        $MainTitle.css({
                            position: 'fixed',
                            top: 46
                        });
                // 設定 #MainTip 座標系統為 fixed
//					if($('#MainTip').css('display') == 'block'){
//						$('#MainTip').css({
//							position: 'fixed',
//							top: 76							
//						}),
//						$('#MainDesc').css({
//							'padding-top': 76 + _ShHeight
//						});
//					}
            }
        } else {
            // 還原 #cart 的座標系統為 absolute
            $MainTopMenu.css({
                position: 'absolute'
            }),
                    $MainTitle.css({
                        position: 'absolute'
                    }),
//				$('#MainTip').css({
//					position: 'relative'
//				}),
                    $('#MainDesc').css({
                'padding-top': 76
            });
        }
    });
    //查詢 UI
    $('#MainTip').css("left", (_UsWidth - $('#MainTip').width()) / 2);
    $('#MainTip').css("top", -($('#MainTip').height() + 80));
    $("body").append('<div id="MainTipBg"></div>');
    var _opacity = .6;
    $("#MainTipBg").css({
        height: _UsDocHeight,
        width: _UsWidth,
        opacity: _opacity,
        display: 'none'
    });
    $('#CmBtnSearch').click(function () {
        openTipUI();
    });
    $("#MainTipBg, #TxtTipBtnCnl").click(function () {
        closeTipUI();
    });
    //選單 UI
    $(".TypeMenu-Title").css("background", "url(/Images/TypeMenuBtnBg-A.png) 0 0 repeat-x");
    $(".TypeMenu-Title").hover(function () {
        $(".TypeMenu-Title").css("background", "url(/Images/TypeMenuBtnBg-Hover.png) 0 0 repeat-x");
        $("ul.TypeMenu .TypeMenu-Top").css("background", "url(/Images/Btn.png) -16px -87px no-repeat");
        $("ul.TypeMenu .TypeMenu-Bottom").css("background", "url(/Images/Btn.png) -24px -87px no-repeat");
    }, function () {
        $(".TypeMenu-Title").css("background", "url(/Images/TypeMenuBtnBg-A.png) 0 0 repeat-x");
        $("ul.TypeMenu .TypeMenu-Top").css("background", "url(/Images/Btn.png) 0 -87px no-repeat");
        $("ul.TypeMenu .TypeMenu-Bottom").css("background", "url(/Images/Btn.png) -8px -87px no-repeat");
    });
    var TypeMenu = false;
    $(".TypeMenu-Title").click(function () {
        $(".TypeMenu-Content").slideDown(150, function () {
            TypeMenu = true;
        });
    });
    $("html").click(function () {
        if (TypeMenu) {
            $(".TypeMenu-Content").slideUp(150);
            TypeMenu = false;
        }
    });
    //頁籤 UI
    var lilength = $(".Tag").find("li").length;
    $(".Tag").find("li").click(function (i) {
        var lieq = $(this).index();
        $(".Tag").find("a").removeClass().addClass("SetCursor");
        $(this).find("a").addClass("Select");
        if (lilength - 1 == lieq) {
            $(this).find("a").addClass("Selectlast");
        }
        $(".Tag li").find("div").hide().eq(lieq).show();
        $(".Tag").find("a").eq(lilength - 1).addClass("last");

        $(".TagContent").find("li.TagContent").hide();
        $(".TagContent").find("li.TagContent").eq(lieq).show();
    }).hover(function () {
        if ($(this).find(".Select").length <= 0) {
            $(".Tag li").find("div").eq($(this).index()).fadeIn(100);
        }
    }, function () {
        if ($(this).find(".Select").length <= 0) {
            $(".Tag li").find("div").eq($(this).index()).fadeOut();
        }
    });
    $(".TagContent").find("li.TagContent").hide().eq(0).show();
    $(".Tag li").find("div").hide().eq(0).show();
});
//window load
$(window).load(function () {
});
//window resize
$(window).resize(function () {
    _UsWidth = $(window).width();														//User 螢幕寬
    _UsHeight = $(window).height();														//User 螢幕高
    _UsDocWidth = $(document).width();													//Html 寬
    _UsDocHeight = $(document).height();												//Html 高
    SetHW();
});
//document keydown
$(document).keydown(function (event) {
    switch (event.keyCode) {
        //Enter
        case 13:
            var position = $('#MainTip').position();
            if (position.top == 0) {
                $("#TxtTipBtnSrh").click();
            }
            return false;
            break;
            //Esc
        case 27:
            closeTipUI();
            return false;
            break;
            //F
        case 70:
            if (event.ctrlKey) {
                $('#CmBtnSearch').click();
                return false;
            }
            break;
    }
});
// function zone
// + 開啟提示視窗
function openTipUI() {
    var position = $('#MainTip').position();
    if (position.top < 0) {
        $('#MainTip').stop().animate({top: 0}, 250);
    } else {
        //$('#MainTip').stop().animate({top: -($('#MainTip').height() + 30)}, 250);
        $('#MainTip').stop().animate({top: 0}, 250);
    }
    //$("#MainTipBg").fadeIn();
    $("#MainTipBg").show();

    $("#MainTip").find("input").eq(0).focus();
    //隱藏 select Object 
    $("#Main").find("select").hide();
    //blurjs
    $('#Main').blurjs({
        overlay: 'rgba(255,255,255,0.1)',
        radius: 5
    });
}
// + 關閉提示視窗
function closeTipUI() {
    $("#Main").find("select").show();
//		$("#MainTipBg").fadeOut();
    $("#MainTipBg").hide();
    $("#MainTip").stop().animate({top: -($('#MainTip').height() + 30)}, 250);
    $.blurjs('reset');
}
// + TreeView 隱藏時調整頁面寬度
function MoveW() {
    if ($("#Left").width() == 0) {
        MoveInt = 208;
    } else {
        MoveInt = 0;
    }
    $("#FrameLeft").stop().animate({
        width: MoveInt
    }, 150);
    $("#FrameLine").stop().animate({
        left: MoveInt
    }, 150);
    $("#FrameRight").stop().animate({
        left: MoveInt + 8,
        width: $(window).width() - MoveInt - 8
    }, 150);
    $("#main").stop().animate({
        width: $(window).width() - MoveInt - 8
    }, 150);
    $("#FrameLineImg").stop().animate({
        left: MoveInt + 2
    }, 150);
    $("#Left").stop().animate({
        width: MoveInt
    }, 150);
}
// + 設定頁面寬高
function SetHW() {
    //Set Index
    $("#DivMain").height(_UsHeight),
            $("#FrameLeft").height(_UsHeight - 30),
            $("#FrameLine").height(_UsHeight - 30),
            $("#FrameRight").height(_UsHeight - 30),
            $("#main").height(_UsHeight - 30),
            $("#Left").height(_UsHeight - 30),
            $("#FrameLineImg").css({top: (_UsHeight / 2) - 15});
    if ($("#FrameLeft").width() == 0) {
        $("#FrameRight").width(_UsWidth),
                $("#main").width(_UsWidth);
    } else {
        $("#FrameRight").width(_UsWidth - 216),
                $("#main").width(_UsWidth - 216),
                $("#FrameLeft").width(208);
    }
    //Set TreeView
    $("#TreeView").height(_UsHeight - 38);
    //Set Other
    $('#MainTip').css("left", (_UsWidth - $('#MainTip').width()) / 2);
    $("#MainTipBg").css({
        height: _UsDocHeight,
        width: _UsWidth
    });
}
// + td color
function sbar(st) {
    st.style.backgroundColor = '#FFF1F7';
}
function cbar(st) {
    st.style.backgroundColor = '';
}

/*
 //預設主畫面
 $(document).ready(function(){
 SetHW();
 $("#FrameLineImg, #FrameLine").click(function(){
 MoveW();
 });
 });
 $(window).resize(function(){
 SetHW();
 });
 //TreeView 隱藏時調整頁面寬度
 function MoveW(){
 if($("#Left").width() == 0){
 MoveInt = 208;
 }else{
 MoveInt = 0;
 }
 $("#FrameLeft").stop().animate({
 width: MoveInt
 },150);
 $("#FrameLine").stop().animate({
 left: MoveInt
 },150);
 $("#FrameRight").stop().animate({
 left: MoveInt + 8,
 width: $(window).width() - MoveInt - 8
 },150);
 $("#main").stop().animate({
 width: $(window).width() - MoveInt - 8
 },150);
 $("#FrameLineImg").stop().animate({
 left: MoveInt + 2
 },150);
 $("#Left").stop().animate({
 width: MoveInt
 },150);
 }
 //設定頁面寬高
 function SetHW(){
 //Set Index
 $("#DivMain").height($(window).height()),
 $("#FrameLeft").height($(window).height() - 30),
 $("#FrameLine").height($(window).height() - 30),
 $("#FrameRight").height($(window).height() - 30),
 $("#main").height($(window).height() - 30),
 $("#Left").height($(window).height() - 30),
 $("#FrameLineImg").css({top: ($(window).height() / 2) - 15});
 if($("#FrameLeft").width() == 0){
 $("#FrameRight").width($(window).width()),
 $("#main").width($(window).width());
 }else{
 $("#FrameRight").width($(window).width() - 216),
 $("#main").width($(window).width() - 216),
 $("#FrameLeft").width(208);
 }
 //Set TreeView
 $("#TreeView").height($(window).height() - 38);
 }
 
 //TreeView顯示控制	
 $(document).ready(function(){
 $numberSpan = $("#TreeView").find('.TreeviewSpan');
 $numberSysFile = $("#TreeView").find('.TreeviewSysFile');
 $numberFncFile = $("#TreeView").find('.TreeviewFncFile');
 //設定功能選項點選狀態
 $numberSpan.click(function(){
 $(".TreeviewSpan").css({background: ''});
 $numberSpan.eq($(".TreeviewSpan").index(this)).css({background: 'url(Images/Bg_02.gif)'});
 });
 //設定系統資料夾開啟關閉
 $numberSysFile.click(function(){
 $TreeId = $("#TreeView").find($(this).attr('treeid'));
 $TreeImgId = $("#TreeView").find($(this).attr('treeimgid'));
 if($(this).attr('class') == "TreeviewSysFile"){
 if($TreeId.css('display') == 'none'){
 $TreeId.stop().slideDown(250);
 //					$TreeId.css({display: "block"});
 $TreeImgId.attr({src: "Images/s4-14.gif"});
 }else{
 $TreeId.stop().slideUp(250);
 //					$TreeId.css({display: "none"});
 $TreeImgId.attr({src: "Images/s4-15.gif"});
 }
 }
 });
 //設定功能資料夾開啟關閉
 $numberFncFile.click(function(){
 $TreeId = $("#TreeView").find($(this).attr('treeid'));
 $TreeImgId = $("#TreeView").find($(this).attr('treeimgid'));
 $TreeFileimgid = $("#TreeView").find($(this).attr('treeFileimgid'));
 if($TreeId.css('display') == 'none'){
 //				$TreeId.css({display: "block"});
 $TreeId.stop().slideDown(250);
 $TreeImgId.attr({src: "Images/s4-20.gif"});
 $TreeFileimgid.attr({src: "Images/s4-18.gif"});
 }else{
 //				$TreeId.css({display: "none"});
 $TreeId.stop().slideUp(250);
 $TreeImgId.attr({src: "Images/s4-16.gif"});
 $TreeFileimgid.attr({src: "Images/s4-17.gif"});
 }
 });
 //功能選項移過
 $numberSpan.hover(function(){
 if($numberSpan.eq($(".TreeviewSpan").index(this)).css('background-image') == 'none'){
 $numberSpan.eq($(".TreeviewSpan").index(this)).css({background: '#e9eef5'});
 }
 }, function(){
 if($numberSpan.eq($(".TreeviewSpan").index(this)).css('background-image') == 'none'){
 $numberSpan.eq($(".TreeviewSpan").index(this)).css({background: ''});
 }
 });
 //系統資料夾移過
 $numberSysFile.hover(function(){
 $(".TreeviewSysFile").css({background: ''});
 //		$numberSysFile.eq($(".TreeviewSysFile").index(this)).css({background: '#f6f6f6'});
 $numberSysFile.eq($(".TreeviewSysFile").index(this)).css({background: 'url(Images/Bg_01.gif)'});
 }, function(){
 $(".TreeviewSysFile").css({background: ''});
 });
 //功能資料夾移過
 $numberFncFile.hover(function(){
 $(".TreeviewFncFile").css({background: ''});
 //$numberFncFile.eq($(".TreeviewFncFile").index(this)).css({background: '#f6f6f6'});
 $numberFncFile.eq($(".TreeviewFncFile").index(this)).css({background: 'url(Images/Bg_01.gif)'});
 }, function(){
 $(".TreeviewFncFile").css({background: ''});
 });
 });
 */

<?php
	//暫時使用
	$strlevel = $_REQUEST["btnlevel"];
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
    <head>
        <!--
        [1. Meta Tags]
        -->
        <meta charset="utf-8" />
        <title>LINE CRM</title>
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <link rel="shortcut icon" href="/assets/img/favicon.ico" type="image/x-icon"/>
        <!--
        [2. Css References]
        -->
        <link rel="stylesheet" href="/assets/css/bootstrap.min.css" type="text/css" id="link-bootstrap" />
        <link rel="stylesheet" href="/assets/css/animate.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/app.min.css" type="text/css" id="link-app" />
        <link rel="stylesheet" href="/assets/css/demo.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/font-awesome.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/pe-icon-7-stroke.css" type="text/css" />
				<link rel="stylesheet" href="/assets/css/shadowboxByBackOffice.css" type="text/css" />
				<link rel="stylesheet" href="/assets/css/Maintain.css" type="text/css" />
        <style>
					ul{
						margin: 10px 0px;
						list-style-type: none;
					}
					#MainDesc{
						height: 420px;
					}
					#opt1, #opt2{
						display: none;
					}
					input{
						width: 380px;
						margin-left: 5px;
					}
					textarea{
						width: 370px;
					}
					table tr td img{
						cursor: pointer;
					}
        </style>
    </head>
    <body>
      <div id="Main">
				<form name="DataForm" id="DataForm" method="post" action="xxx.php">
					<div id="MainTopMenu">
						<span class="MenuLeft">
							<ul class="Menunav">
								<li class="red"><a class="LFFFFFF SetCursor">儲存</a></li>
								<li class="gray"><a onclick="window.parent.Shadowbox.close()" class="L333333 SetCursor">取消</a></li>
							</ul>
						</span>
					</div>
					<div id="MainDesc">
						<ul>
							<li>
								<label data-optid="1"><input name="rst_type_1" class="radio_check" value="3" type="radio"><span class="text"> 關鍵字</span></label>
								<label data-optid="2"><input name="rst_type_1" class="radio_check" value="3" type="radio"><span class="text"> 網址</span></label>
								<label data-optid="3"><input name="rst_type_1" class="radio_check" value="3" type="radio"><span class="text"> 不要設定</span></label>
								<label data-optid="4"><input name="rst_type_1" class="radio_check" value="3" type="radio"><span class="text"> 分享換好康</span></label>
							</li>
							<li id="opt1">
								<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 300px;">
								  <option value="[abc]">[abc]</option>
								  <option value="[test]">[test]</option>
								  <option value="[麻將牌]">[麻將牌]</option>
								  <option value="[灣聲樂團]">[灣聲樂團]</option>
								  <option value="[圖片-連結型態]">[圖片-連結型態]</option>
								  <option value="[Fish_文字]">[Fish_文字]</option>
								  <option value="[Fish_麻將牌]">[Fish_麻將牌]</option>
								  <option value="[圖片不設定]">[圖片不設定]</option>
								  <option value="[227]">[227]</option>
								  <option value="[777]">[777]</option>
								  <option value="[4455]">[4455]</option>
								  <option value="[麻將牌2]">[麻將牌2]</option>
								  <option value="[長輩文]">[長輩文]</option>
								  <option value="[麻將]">[麻將]</option>
								  <option value="[0313建立的]">[0313建立的]</option>
								  <option value="[sssss]">[sssss]</option>
								  <option value="[gogo]">[gogo]</option>
								  <option value="[234123]">[234123]</option>
								  <option value="[testaaa]">[testaaa]</option>
								  <option value="[abcwwq]">[abcwwq]</option>
								  <option value="[12345]">[12345]</option>
								  <option value="[這是一個有關聯的關鍵字]">[這是一個有關聯的關鍵字]</option>
								</select>
							</li>
							<li id="opt2">
								<input type="text" id="rst_url" name="rst_url[]"  maxlength="500">
							</li>
						</ul>
					</div>
				</from>
			</div>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/jquery.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/bootstrap.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/modernizr.custom.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/slimscroll/jquery.slimscroll.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/animsition/animsition.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/main.js"></script>
	    <script type="text/javascript" src="https://social-crm-2.chiliman.com.tw/assets_rear/javascripts/ComFun.js"></script>
			<script type="text/javascript" src="https://social-crm-2.chiliman.com.tw/assets_rear/javascripts/jcomFunc.js"></script>
			<script type="text/javascript" src="https://social-crm-2.chiliman.com.tw/assets_rear/javascripts/jquery.ChiliUpload.js"></script>
			<script type="text/javascript" src="https://social-crm-2.chiliman.com.tw/assets_rear/javascripts/init-ChiliUpload.js"></script>
			<script type="text/javascript" src="https://social-crm-2.chiliman.com.tw/assets_rear/javascripts/BackEndJSForWork.js"></script>
			<script type="text/javascript" src="https://social-crm-2.chiliman.com.tw/assets_rear/javascripts/shadowboxByBackOffice.js"></script>
			<script type="text/javascript" src="https://social-crm-2.chiliman.com.tw/assets_rear/javascripts/BootstrapJSForWork.js"></script>
	    <script src="/assets_front/javascripts/FBMenu/FBMenu.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/select2/select2.full.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/tagsinput/bootstrap-tagsinput.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/datepicker/bootstrap-datepicker.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/timepicker/bootstrap-timepicker.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/moment/moment.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/daterangepicker/daterangepicker.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/autosize/jquery.autosize.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/spinbox/spinbox.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/knob/jquery.knob.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/colorpicker/jquery.minicolors.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/slider/ion.rangeSlider.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/dropzone/dropzone.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/rating/jquery.rateit.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/mockjax/jquery.mockjax.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/lib/xeditable/bootstrap-editable.min.js"></script>
	    <script src="https://social-crm-2.chiliman.com.tw/assets/js/pages/formadvancedinputs.js"></script>
			<script language="JavaScript">
			//document ready
				$(document).ready(function(){
					$('ul').find('label').click(function(i){
						$('#opt1, #opt2').hide();
						$('#opt' + $(this).attr('data-optid')).show();
					});
					$('li.red').click(function(){
						parent.UpdateMenu('<?php echo $strlevel;?>', '很多字很多字很多字很多字很多字很多字很多字很多字很多字很多字很多字很多字很多字很多字很多字很多字');
						window.parent.Shadowbox.close();
					});
				});
			</script>
    </body>
</html>

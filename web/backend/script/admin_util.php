<?php
/**
 * [TQBlog] (C)2008-2028 tqtqtq.com
 * @author TQBlog Team
 * This is NOT a freeware, use is subject to license terms
 * $Id: admin_util.php 33828 2008-02-22 09:25:26Z team $
 */
 
header('Content-Type: application/x-javascript; Charset=utf-8');

require '../function/function_base.php';

$tqb->CheckGzip();
$tqb->StartGzip();
?>
var bloghost="<?php echo $bloghost; ?>";
var cookiespath="<?php echo $cookiespath; ?>";



//*********************************************************
// 目的：    全选
// 输入：    无
// 返回：    无
//*********************************************************
function BatchSelectAll() {
	$("input[name='id[]']").click();
}
//*********************************************************




//*********************************************************
// 目的：
// 输入：    无
// 返回：    无
//*********************************************************
function BatchDeleteAll(objEdit) {

	objEdit=document.getElementById(objEdit);
	objEdit.value="";
	var aryChecks = document.getElementsByTagName("input");
	for (var i = 0; i < aryChecks.length; i++){
		if((aryChecks[i].type=="checkbox")&&(aryChecks[i].id.indexOf("edt")!==-1)){
			if(aryChecks[i].checked){
				objEdit.value=aryChecks[i].value+","+objEdit.value;
			}
		}
	}

}
//*********************************************************








//*********************************************************
// 目的：    ActiveLeftMenu
// 输入：    无
// 返回：    无
//*********************************************************
function ActiveLeftMenu(name){

	name="#"+name;
	$("#leftmenu li").removeClass("on");
	$(name).parent().addClass("on");
	var s=$(name).children("span").css("background-image");
	if(s!==undefined){
		s=s.replace("1.png","2.png");
		$(name).children("span").css("background-image",s);
	}

}
//*********************************************************




//*********************************************************
// 目的：    ActiveTopMenu
// 输入：    无
// 返回：    无
//*********************************************************
function ActiveTopMenu(name){

	name="#"+name;
	$("#topmenu li").removeClass("on");
	$(name).addClass("on");

}
//*********************************************************





//*********************************************************
// 目的：    表格斑马线
// 输入：    无
// 返回：    无
//*********************************************************
function bmx2table(){
	var class_=new Array("color2","color3","color4");
	var j=$("table[class!='nobmx'] tr:has(th)").addClass("color1");
    $("table[class!='nobmx']").each(function(){
 		if(j.length==0){class_[1]="color2";class_[0]="color3";}
		$(this).find("tr:not(:has(th)):even").removeClass(class_[0]).addClass(class_[1]);
		$(this).find("tr:not(:has(th)):odd").removeClass(class_[1]).addClass(class_[0]);
	})
	$("table[class!='nobmx']").find("tr:not(:has(th))").mouseover(function(){$(this).addClass(class_[2])}).mouseout(function(){$(this).removeClass(class_[2])});
};
//*********************************************************





//*********************************************************
// 目的：    批量操作提醒
// 输入：    无
// 返回：    无
//*********************************************************
function Batch2Tip(s){$("#batch p").html(s)}
function BatchContinue(){$("#batch p").before("<iframe style='width:20px;height:20px;' frameborder='0' scrolling='no' src='<?php echo $bloghost ?>admin/admin.php?act=batch'></iframe>");$("#batch img").remove();}
function BatchBegin(){};
function BatchEnd(){};
function BatchNotify(){notify($("#batch p").html())}
function BatchCancel(){$("#batch iframe").remove();$("#batch p").before("<iframe style='width:20px;height:20px;' frameborder='0' scrolling='no' src='<?php echo $bloghost ?>admin/admin.php?act=batch&cancel=true'></iframe>");};
//*********************************************************




//*********************************************************
// 目的：    CheckBox
// 输入：    无
// 返回：    无
//*********************************************************
function ChangeCheckValue(obj){

	$(obj).toggleClass('imgcheck-on');

	if($(obj).hasClass('imgcheck-on')){
		$(obj).prev('input').val('1');
	}else{
		$(obj).prev('input').val('0');
	}

}
//*********************************************************




//*********************************************************
// 目的：    Notifications
// 输入：    无
// 返回：    无
//*********************************************************
function notify(s){
	if (window.webkitNotifications) {
		if (window.webkitNotifications.checkPermission() == 0) {
			var zb_notifications = window.webkitNotifications.createNotification('<?php echo $bloghost; ?>admin/image/admin/logo-16.png', '<?php echo $lang['msg']['notify'];?>', s);
			zb_notifications.show();
			zb_notifications.onclick = function() {top.focus(),this.cancel();}
			zb_notifications.replaceId = 'Meteoric';
			setTimeout(function(){zb_notifications.cancel()},5000);
		} else {
			window.webkitNotifications.requestPermission(notify);
		}
	}
}
//*********************************************************



function statistic(s){
	$("#statloading").show();
	$("#updatatime").hide();
	$.get("<?php echo $bloghost; ?>admin/admin.php"+s,{},
		function(data){
			$("#tbStatistic tr:first ~ tr").remove();
			$("#tbStatistic tr:first").after(data);
			bmx2table();
			$("#statloading").hide();
			$("#updatatime").show();
		}
	);
}

function updateinfo(s){
	$("#infoloading").show();
	$.get("<?php echo $bloghost; ?>admin/admin.php"+s,{},
		function(data){
			$("#tbUpdateInfo tr:first ~ tr").remove();
			$("#tbUpdateInfo tr:first").after(data);
			$("#infoloading").hide();
		}
	);
}


function AddHeaderIcon(s){
$("div.divHeader,div.divHeader2").first().css({"padding-left":"38px","background":"url('"+s+"') 1px 8px no-repeat","background-size":"32px"});
}



//*********************************************************
// 目的：
//*********************************************************
$(document).ready(function(){

	// Content box tabs:
	$('.content-box .content-box-content div.tab-content').hide(); // Hide the content divs
	$('ul.content-box-tabs li a.default-tab').addClass('current'); // Add the class "current" to the default tab
	$('.content-box-content div.default-tab').show(); // Show the div with class "default-tab"

	$('.content-box ul.content-box-tabs li a').click( // When a tab is clicked...
		function() {
			$(this).parent().siblings().find("a").removeClass('current'); // Remove "current" class from all tabs
			$(this).addClass('current'); // Add class "current" to clicked tab
			var currentTab = $(this).attr('href'); // Set variable "currentTab" to the value of href of clicked tab
			$(currentTab).siblings().hide(); // Hide all content divs
			$(currentTab).show(); // Show the content div with the id equal to the id of clicked tab
			return false;
		}
	);

	//斑马线化表格
	bmx2table();

	if($('.SubMenu').find('span').length>0){
		//if($('#leftmenu').find('li.on').length>0){
		//	$('#leftmenu li.on').after('<li class="sub">'+$('.SubMenu').html()+'</li>');
		//}else{
			$('.SubMenu').show();
		//}
	}

	//checkbox
	$('input.checkbox').css("display","none");
	$('input.checkbox[value="1"]').after('<span class="imgcheck imgcheck-on"></span>');
	$('input.checkbox[value!="1"]').after('<span class="imgcheck"></span>');


	$('span.imgcheck').click(function(){ChangeCheckValue(this)})

	//batch
	$("#batch a").bind("click", function(){ BatchContinue();$("#batch p").html("<?php echo $lang['msg']['batch_operation_in_progress']; ?>");});

	$(".SubMenu span.m-right").parent().css({"float":"right"});


	$("img[width='16']").each(function(){if($(this).parent().is("a")){$(this).parent().addClass("button")}});

	if($("p.hint:visible").length>0){
		$("p.hint:visible").delay(3500).hide(1500,function(){});
	}

	$("input[type='file']").click(function(){
		if(/IEMobile|WPDesktop/g.test(navigator.userAgent)&&$(this).val()==""){
			alert('<?php echo $lang['error'][65]?>')
		}
	})

	if (!$.support.leadingWhitespace) {
		<?php
			if($option['CFG_ADMIN_HTML5_ENABLE']){
				echo 'alert("' . $lang['error']['74'] . '");';
			}else{
				echo 'if($("div.divHeader,div.divHeader2").first().css("background").indexOf("admin")==-1){AddHeaderIcon("'. $bloghost .'admin/image/admin/plugin_32.png");}';
			}
		?>
	}

	SetCookie("timezone",(new Date().getTimezoneOffset()/60)*(-1));
});



<?php
foreach ($GLOBALS['Filter_Plugin_Admin_Util'] as $fpname => &$fpsignal) {$fpname();}
die();
?>
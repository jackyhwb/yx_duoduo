<?php
/**
 * ============================================================================
 * 版权所有 多多科技，保留所有权利。
 * 网站地址: http://soft.duoduo123.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/

if(!defined('INDEX')){
	exit('Access Denied');
}

include(TPLPATH.'/inc/header.tpl.php');

?>
<style>
html { overflow-x:hidden; }
</style>
<script>
  	function ScrollImgLeft(){
		var speed=30
		var scroll_begin = document.getElementById("scroll_begin");
		var scroll_end = document.getElementById("scroll_end");
		var scroll_div = document.getElementById("scroll_div");		
			
		if(scroll_begin.offsetWidth < scroll_div.offsetWidth){
				
      	return false;	
    	}else{
		  scroll_end.innerHTML=scroll_begin.innerHTML;
		  function Marquee(){
			if(scroll_end.offsetWidth-scroll_div.scrollLeft<=0)
			  scroll_div.scrollLeft-=scroll_begin.offsetWidth
			else
			  scroll_div.scrollLeft++
		  }
		  var MyMar=setInterval(Marquee,speed)
		  scroll_div.onmouseover=function() {clearInterval(MyMar)}
		  scroll_div.onmouseout=function() {MyMar=setInterval(Marquee,speed)}
				
		}
			
	  }
	function Autohide(){
		$('#ajax_goods_loading').hide();
	}
	setTimeout('Autohide()',3000);
</script>
<link rel="stylesheet" href="<?=TPLURL?>/inc/css/gametask/offer_css.css" />
<div class="mainbody" style="min-height:1900px; width:1020px">
    <div class="wrap-task">
        <div class="ad-banner" style="height:230px; margin-bottom:10px; margin-top:10px; border:0 none'">
           <div class="l aside">
            <div class="ad2" style="width:750px; padding:0px;">
                <a <?php if($offer['url']!=''){?>href="<?=$offer['url']?>"<?php }?> target="_blank"><img src="<?=$offer['offer']?>" width="750" height="230" alt="<?=$offer['title']?>"/></a>
            </div>
            <div class="statue">
                <div class="ver_top">
                    <img width="48" height="48" alt="<?=$user['name']?>" style="background:#FFF; border:1px solid #CCC; padding:3px; float:left;" src="<?=a($user['id'],'middle')?>">
                    <div class="user-mse six">
                        <span style="float:left;">欢迎您</span>
                        <a style="float:right;" href="<?=u('user','index')?>">[会员中心]</a>
                    </div>
                    <div class="user-mse six"><span class="user"><?=$dduser['name']?></span></div>
                 </div>
                <div style="float:left;" class="fanli-mse">
                	<p class="first"><span class="xinxi">我的信息</span></p>
                    <p>我的余额：<span class="price"><?=(float)$dduser['money']?>&nbsp;&nbsp;元</span></p>
                    <p>已获任务奖励：<span class="price"><?=(float)$total?>&nbsp;&nbsp;元</span></p>
                </div>
            </div>
        </div>
        </div>
        <div class="otherss">
                <p class="six" style="float:left;">他们正在玩游戏拿返利</p>
                    <div class="scroll_div" id="scroll_div" style="height:42px; line-height:42px;">
                    <?php if(!empty($info)){?>
                    <div id="scroll_begin" style="height:42px; line-height:42px;">
                    <?php foreach($info as $r){?>
                    	<?=utf_substr($r['ddusername'],2).'***'?>完成<em><?=$r['programname']?></em>获得<span><?=(float)$r['point']?></span>元任务奖励&nbsp;&nbsp;    
                     <?php }?>
                     </div>
                      <?php }else{?>
                      <div id="scroll_begin" style="height:42px; line-height:42px;">&nbsp;&nbsp;暂时没有数据哦！赶紧行动吧！</div>
                      <?php }?>
                      <div id="scroll_end"></div>
                    </div>
                <script type="text/javascript">
                  ScrollImgLeft();
                </script>
            </div>
            <div class="yuans">
            	<h3 style="color:#bb0504;">注意</h3>
                <p style="line-height:20px;">1、状态"已参与" 的，表示点击了，需等待最终完成任务并确认后获得"佣金"。请注意关注每个游戏的审核时间。状态"已完成"，说明"佣金"已发放，请自行查收！</p>
                <p style="line-height:20px;">2、若已超过游戏的审核时间，游戏还是待审状态，请联系游戏返利QQ客服详细咨询。<span style="color:#bb0504; font-weight:bold;"> QQ：1787430654</span></p>
            </div>
        <div class="r main" style="margin-left:-10px; width:1000px;">
            <div id="ajax_goods_loading" style="background:#999; color:#000;filter:alpha(Opacity=30);width:1000px; height:1370px;padding:15px 0;margin:auto;text-align:center; z-index:555; position:absolute; font-size:16px; font-weight:bolder"><img src="<?=TPLURL?>/inc/images/ajax_loader.gif" style="margin-bottom:-2px" />&nbsp;&nbsp;正在拼命加载中</div>
            <div class="iframe">
                <iframe src="http://u.fanlicheng.cn/Public/html/v83/wami.html?userid=<?=$memberid?>" style='width:1020px; min-height:1680px;' frameborder='no' border='0' scrolling="no"></iframe>
            </div>
        </div>
    </div>
</div>
<!--新的U站插件版本20140225-->
<?php include(TPLPATH.'/inc/footer.tpl.php');?>
<?php
if (!defined('INDEX')) {
	exit ('Access Denied');
}

$parameter=act_wap_goods();
extract($parameter);
include(TPLPATH.'/inc/header_2.tpl.php');?>
<script>
var floorStep=2;
var ajaxTag=0;
$(function(){
	$(window).bind('scroll',function(){
		if($(window).scrollTop()+$(window).height()>=$(document).height()-400){
			if(ajaxTag==0){
				ajaxTag=1;
				jiazai(floorStep);
				floorStep++;
			}
		}
	});
	$('.shuju_load img.lazy').lazyload({
		threshold:20,
		failure_limit:50
	});
});

function index_tpl(){/*<li><a href="{$url}"> <span class="mu-tmb lazyload"><img data-original="{$img}_200x200.jpg" title="{$title}" class="lazy" src="<?=$dd_tpl_data['loading_img']?>" style="width:100px; height:100px; margin:0 auto;display: block;"> </span> <span style=" font-size:1em; max-height:3em; overflow:hidden; overflow:hidden; display:block">{$title}</span>
        <span class="pricecolor1"><del>￥{$price}元</del></span>
        <span class="pricecolor2">￥{$discount_price}元 <span style=" background:#09F;">{$bankuai_title}</span></span>
      </a></li>*/;}

function jiazai(floorStep){	
	$('.chakan_more').text('数据获取中……');
	var url='<?=wap_l(MOD,ACT)?>&code='+'<?=$code?>'+'&page='+floorStep;
	$.getJSON(url,function(data){
		ajaxTag=0;
		if(data.s=='1'){
			if(data.r=='' || data.r=='null' || data.r==null){
				alert('没有了');
				ajaxTag=1;
				$('.chakan_more').hidden();
			}
			else{
				for(i in data.r){
					row=data.r[i];
					var html=getTplObj(index_tpl,row);
					$('.shuju_load').append(html);
				}
				$('.chakan_more').text('点击更多');
				$('.shuju_load img.lazy').lazyload({
					threshold:20,
					failure_limit:50
				});
			}
		}
	});
}
</script>
<div class="listHeader">
  <p> <b><?=$title?></b><a href="javascript:;" onclick="history.back()" class="left">返回</a> <a href='<?=wap_l()?>' class="right">首页</a> </p>
</div>

  <p class="line"></p>
<div class="mc radius">
	<ul class="mu-l2w shuju_load">
		 <?php foreach($shuju_data as $k=>$row){?>
		<li> <a href="<?=$row['url']?>"> <span class="mu-tmb"><img data-original="<?=tb_img($row['img'],200)?>" title="<?=$row['title']?>" src="<?=$dd_tpl_data['loading_img']?>" class="lazy" style="width:100px; height:100px; margin:0 auto;display: block;"> </span> <span style=" font-size:1em; max-height:3em;overflow:hidden; overflow:hidden; display:block"><?=$row['title']?></span>
        <?php if($row['price']>0){?><span class="pricecolor1">原价：<del>￥<?=$row['price']?>元</del></span><?php }?>
        <span class="pricecolor2">现价：￥<?=$row['discount_price']?>元 <span style=" background:#09F;"><?=$row['bankuai_title']?></span></span>
      </a> </li>
      <?php }?>
       
        
			</ul>
</div>
<a class="chakan_more">下拉加载更多</a>
<?php include(TPLPATH.'/inc/footer.tpl.php');?>
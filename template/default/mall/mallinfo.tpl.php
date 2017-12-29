<?php
if(!defined('INDEX')){
	exit('Access Denied');
}
?>
<div class="mall2xxjj">
<div class="mall2xxjjl"> 
<ul>
  <li><img src="<?=$mall['img']?>" /></li>
  <li id="mall2jianju">最高返：<span><?=$mall['fan']?><?=$fanli_type[$mall['type']]?></span></li>
  <li><DIV class=fxshjbutton><A href="<?=$jump?>" target=_blank><DIV class=fxshj_button1 onMouseOver="this.className='fxshj_button1_h';" onmouseout="this.className='fxshj_button1';"></DIV></A></DIV></li>
  <?php if($webset['jiaocheng']['mall']!=''){?><li><a target="_blank" id="jiaocheng" href="<?=$webset['jiaocheng']['mall']?>">如何购物拿返利？</a></li><?php }?>
</ul>
</div>
<div class="mall2xxjjr">
<DIV class=shjname><?=$mall['title']?><?php if($mall['renzheng']==1){?>&nbsp;&nbsp;<IMG alt='网站认证' src="<?=TPLURL?>/inc/images/renzheng.gif"><?php }?></DIV>
<DIV class=des><?=$mall['des']?></DIV>
<DIV class=shjkoubei>
<DIV class=title>口碑指数：</DIV>
<DIV class=xx3>
<DIV style="WIDTH: 100px; BACKGROUND: url(<?=TPLURL?>/inc/images/5bx.gif) no-repeat 0px 0px; FLOAT: left; HEIGHT: 19px" title="<?=$fen?>分">
<DIV style="WIDTH: 100px; BACKGROUND: url(<?=TPLURL?>/inc/images/5hx.gif) 0px 0px; HEIGHT: 19px; width:<?=(19.8*$fen)?>px"></DIV></DIV></DIV>
<DIV class=pinglun>已有<SPAN><?=$mall_comment_total?></SPAN>人参与评分 【<a style="color:#333; text-decoration:underline" href="<?=u('mall','view',array('id'=>$mall['id']))?>#pjdf" <?php if(MOD=='huodong'){?>target="_blank"<?php }?>>评价</a>】</DIV>
<DIV class=clear></DIV></DIV>
<DIV class=shjyunzhengce>
<UL>
  <LI class=colm1><?=$mall['fuwu']?></LI>
  <LI class=colm12><SPAN style="color:#666">注意：如果没有跟单，请清除cookies后再登录下单！<a target="_blank" id="jiaocheng" href="<?=$webset['jiaocheng']['cookie']?>">如何清除cookie</a></SPAN> 
</LI></UL></DIV>
</div>
<div style="clear:both"></div>
</div>
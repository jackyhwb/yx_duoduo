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
<link rel="stylesheet" href="<?=TPLURL?>/inc/css/offer/offer_css.css" />
<div class="mainbody">
    <div class="wrap-task">
        <div class="ad-banner">
           <a <?php if($offer['url']!=''){?>href="<?=$offer['url']?>"<?php }?> target="_blank"><img src="<?=$offer['img']?>" alt="<?=$offer['title']?>" width="960" height="128"></a>
        </div>
        <div class="r main">
            <div class="iframe">
                <iframe src="<?=DD_U_URL?>/Public/html/v83/offer.html?styleIndex=<?=$offer['type']?>&memberid=<?=$memberid?>" style='width:720px; height:535px;' scrolling='no' frameborder='no' border='0'></iframe>
            </div>
        </div>
        <div class="l aside">
            <div class="status">
                <p class="user-msg six" title="新插件">您好，<?=$dduser['name']?></p>
                <div class="fanli-msg">
                    <em style="text-indent:1em;">返利余额：<?=(float)$dduser['money']?>元</em>
                    <p>（已获任务奖励：<?=(float)$total?>元）</p>
                </div>
            </div>
            <div class="others">
                <p class="six">
                </p>
                <div class="jCarouselLite" style="position:relative;">
                    <ul id="gundong" style="position:absolute">
                    <?php if(!empty($task)){?>
						<?php foreach($task as $r){?>
                            <li><?=utf_substr($r['ddusername'],2).'***'?>获得<span><?=(float)$r['point']?></span>元任务奖励<br />
                            (<em><?=$r['programname']?></em>)
                            </li>
                        <?php }?>
                    <?php }else{?>
                    	暂时没有数据哦！赶紧行动吧！
                    <?php }?>
                    </ul>
                </div>
            </div>
            <div class="ad2">
                <img src="<?=TPLURL?>/inc/images/rwfl.png" width="217" height="140"/>
            </div>
        </div>
    </div>
</div>
<!--新的U站插件版本20140225-->
<?php include(TPLPATH.'/inc/footer.tpl.php');?>
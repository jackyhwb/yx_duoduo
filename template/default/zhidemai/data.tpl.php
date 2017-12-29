<?php
if(!defined('INDEX')){
	exit('Access Denied');
}

if(!class_exists('zhidemai')){
	include(DDROOT.'/comm/zhidemai.class.php');
	$zhidemai_class=new zhidemai($duoduo);
}
if(isset($shuju_code) && $shuju_code!=''){
	$code=$shuju_code;
}
else{
	$shuju_code=$code=MOD;
}

$pagesize=$webset['ddgoodsnum'][$code]?$webset['ddgoodsnum'][$code]:10;
$cid=(int)$_GET['cid'];
$page=(int)$_GET['page'];
$do=$_GET['do'];
$q=$_GET['q'];
if($page==0) $page=1;
$page=($page-1)*($ajax_load_num+1)+1;
if($page==0) $page=1;
$url_arr=array();
$zhidemai_where='1';

$url_arr['cid']=$cid;
if($cid>0){
	$zhidemai_where.=' and cid="'.$cid.'"';
}
$url_arr['do']=$do;
if($do=='my'){
	$zhidemai_where.=' and uid="'.$dduser['id'].'"';
}
/*$url_arr['q']=$q;
if($q!=''){
	$zhidemai_where.=' and title like "%'.$q.'%"';
}*/

$data=$zhidemai_class->index_list($pagesize,$page,$zhidemai_where,$zhidemai_total);
$zhidemai_list=$data['data'];
$total=$data['total'];
?>
<?php if(empty($zhidemai_list) && VIEW_PAGE==1){include(TPLPATH.'/inc/nonetip.tpl.php');}?>
          
          <?php foreach($zhidemai_list as $row){ ?>
                      <div id="J_zdm_list" style="position:relative">
              <div class="zdm-list-item J-item-wrap item-no-expired" data-id="<?=$row['id']?>" id="post-<?=$row['id']?>">
              <?php if($row['shuxing_id']>0){?><i class='<?=$row['shuxing_class']?>'><?=$row['shuxing']?></i><?php }?>
                <h4 class="t"><a class="J-item-track nodelog" href="<?=$row['view']?>" target="_blank"><?=$row['title']?><span class="red t-sub"><?=$row['subtitle']?></span></a></h4>
                <div class="item-img">
                  <a href="<?=$row['view_jump']?>"class="J-item-track img nodelog" target="_blank"><?=dd_html_img($row['img'],$row['title'])?></a>
                  <a href="<?=$row['view_jump']?>" class="btn-zdm-goshop J-item-track nodelog" target="_blank">直接返利模式购买</a>
                </div>
                <div class="item-info J-item-info">
                  <div class="item-time"><?=date('m-d H:i:s',strtotime($row['starttime']))?> </div>
                  <div class="item-type">分类：<a href="<?=$row['caturl']?>" class="nine" target="_blank"><?=$row['catname']?></a> </div>
                  <?php if($row['ddusername']!=''){?><div class="item-user">推荐人：<?=$row['ddusername']?></div><?php }?>
                  <?php if($row['mallname']!=''){?><div class="item-type">所属：<a class="nine" <?php if($row['mallurl']!=''){?>href="<?=$row['mallurl']?>"<?php }?>><?=$row['mallname']?></a></div><?php }?>
                  <div class="item-content J-item-content nodelog-detail" data-id="<?=$row['id']?>">
                    <div class="item-content-inner J-item-content-inner"><?=$row['content']?></div>
                  </div>
                  
                  <div class="item-toggle" data-status="0" onclick="zhidemaiItemToggle($(this))"><a href="javascript:void(0);" class="blue J-item-toggle">展开全文 ∨</a></div>
                  
                </div>
                <div class="J-expend-wrap-before item-vote clearfix">
                  <em class="l item-vote-t">评价本文：</em>
                  <a href="javascript:void(0);" class="l item-vote-yes J-item-vote-yes vote" onclick="zhidemaiVote($(this))" data-type="1"data-id="<?=$row['id']?>"><?=$row['ding']?></a>
                  <a href="javascript:void(0);" class="l item-vote-no J-item-vote-no vote" onclick="zhidemaiVote($(this))" data-type="0"data-id="<?=$row['id']?>"><?=$row['cai']?></a>
                  <span class="l">大家在评论：</span>
                  <a href="javascript:void(0)" class="l item-view-comment J-expend-trigger" onclick="zhidemaiComment($(this))" data-id="<?=$row['id']?>"><?=$row['pinglun']?></a>
                  <div class="shopitem_main_r_6" style="float:right; width:auto;  margin-left:10px;">
                  			<span style="float:left; font-size:12px; line-height: 35px;">分享：</span>
                            <div class="bshares-custom">
                                <a title="分享到QQ空间" class="bshares-qzone" href="http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=<?=urlencode(SITEURL.'/index.php?mod=zhidemai&act=view&id='.$row['id'])?>&title=<?=$row['title']?>&pics=<?=preg_match('/^http/',$row['img'])?$row['img']:SITEURL.'/'.$row['img']?>&summary=<?=str_replace('"','',compact_html(strip_tags($row['content'])))?>" target=_blank>QQ</a>
                                <a title="分享到新浪微博" class="bshares-sinaminiblog" href="http://service.weibo.com/share/share.php?appkey=583395093&title=<?=$row['title']?>&url=<?=urlencode(SITEURL.'/index.php?mod=zhidemai&act=view&id='.$row['id'])?>&source=bshare&retcode=0&pic=<?=preg_match('/^http/',$row['img'])?$row['img']:SITEURL.'/'.$row['img']?>" target="_blank">新浪</a>
                                <a title="分享到人人网" class="bshares-renren" href="http://widget.renren.com/dialog/share?resourceUrl=<?=urlencode(SITEURL.'/index.php?mod=zhidemai&act=view&id='.$row['id'])?>&title=<?=$row['title']?>&images=<?=preg_match('/^http/',$row['img'])?$row['img']:SITEURL.'/'.$row['img']?>&description=<?=str_replace('"','',compact_html(strip_tags($row['content'])))?>" target="_blank">人人</a>
                                <a title="分享到腾讯微博" class="bshares-qqmb" href="http://share.v.t.qq.com/index.php?c=share&a=index&title=<?=$row['title']?>&site=http%3a%2f%2fwww.bshare.cn&pic=<?=preg_match('/^http/',$row['img'])?$row['img']:SITEURL.'/'.$row['img']?>&url=<?=urlencode(SITEURL.'/index.php?mod=zhidemai&act=view&id='.$row['id'])?>&title=<?=$row['title']?>&appkey=dcba10cb2d574a48a16f24c9b6af610c&assname=${RALATEUID}" target="_blank">腾讯</a>
                                <a title="分享到开心网" class="bshares-kaixin001" href="http://www.kaixin001.com/~repaste/repaste.php?rtitle=<?=$row['title']?>&rurl=<?=urlencode(SITEURL.'/index.php?mod=zhidemai&act=view&id='.$row['id'])?>" target="_blank">开心网</a>
                            </div>
                        </div>
                </div>
                
                <div class="J-expend-wrap yahei zdm-expand-wrap" style=" display:none">
  <i class="top-arrow"></i>
  <div class="yahei zdm-expand-comment">
    
  </div>
</div>
                

                
              </div>
              
              

              
            </div>
<?php }
if(AJAX==1){exit;}
?>    
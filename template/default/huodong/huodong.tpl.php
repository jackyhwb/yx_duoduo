<?php if($total==0){?>
             <p style=" width:150px; text-align:center; margin:auto; margin-top:20px; color:#666; font-size:16px"><b>暂无促销活动</b></p>
<?php }else{?>
<script src="js/jQuery.autoIMG.js"></script>
<script>
$(function(){
    $(".cxleft .imgborder").imgAutoSize(430,150); 
})
</script>
<?php foreach($huodong as $row){?>
    <div class="cxmain">
        <div class="cxleft"><div class="imgborder" style=" margin-left:10px; height:150px; width:430px; border:0 none;"><a  target="_blank" href="<?=u('jump','huodong',array('hid'=>$row['id']))?>"><img alt="<?=$row['title']?>" style="display:none;" src="<?=$row['img']?>" /></a></div></div>
        <div class="cxright" style="margin-top:-11px;">
            <!--<div class="cxright_logo">
                <a href="<?=u('mall','view',array('id'=>$row['mall_id']))?>"><img alt="<?=$row['mallname']?>" src="<?=$row['logo']?>" /></a>
                <div class="cxright_logo_2"><p><?=$row['mallname']?></p><p><span>最高返:<?=$row['fan']?></span></p> </div>
            </div>-->
            <div class="cxright_bt">  <a href="<?=$row['goto']?>"><?=$row['title']?></a></div>
            <div class="cxright_button">
                 <A href="<?=u('jump','huodong',array('hid'=>$row['id']))?>" target=_blank>
                 <DIV class=cx_button1 onMouseOver="this.className='cx_button1_h';" onmouseout="this.className='cx_button1';"></DIV>
                 </A>
            </div>
            <div class="cxright_time">活动时间：<?=date('Y-m-d',$row['sdate'])?> - <?=date('Y-m-d',$row['edate'])?></div>
            <div class="cxright_share">
                <div class="cxright_share_2" >
                    <div class="bshares-custom" style="width:auto;">
                    <a title="分享到QQ空间" class="bshares-qzone" href="http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=<?=urlencode(SITEURL.'/index.php?mod=huodong&act=view&id='.$row['mall_id'])?>&title=<?=$row['title']?>&pics=<?=preg_match('/^http/',$row['img'])?$row['img']:SITEURL.'/'.$row['img']?>&summary=<?=$row['title']?>" target=_blank>QQ</a>
                    <a title="分享到新浪微博" class="bshares-sinaminiblog" href="http://service.weibo.com/share/share.php?appkey=583395093&title=<?=$row['title']?>&url=<?=urlencode(SITEURL.'/index.php?mod=huodong&act=view&id='.$row['id'])?>&source=bshare&retcode=0&pic=<?=preg_match('/^http/',$row['logo'])?$row['logo']:SITEURL.'/'.$row['logo']?>" target="_blank">新浪</a>
                    <a title="分享到人人网" class="bshares-renren" href="http://widget.renren.com/dialog/share?resourceUrl=<?=urlencode(SITEURL.'/index.php?mod=huodong&act=view&id='.$row['id'])?>&title=<?=$row['title']?>&images=<?=preg_match('/^http/',$row['img'])?$row['img']:SITEURL.'/'.$row['img']?>&description=<?=$row['title']?>" target="_blank">人人</a>
                    <a title="分享到腾讯微博" class="bshares-qqmb" href="http://share.v.t.qq.com/index.php?c=share&a=index&title=<?=$row['title']?>&site=http%3a%2f%2fwww.bshare.cn&pic=<?=preg_match('/^http/',$row['img'])?$row['img']:SITEURL.'/'.$row['img']?>&url=<?=urlencode(SITEURL.'/index.php?mod=huodong&act=view&id='.$row['id'])?>&title=<?=$row['title']?>&appkey=dcba10cb2d574a48a16f24c9b6af610c&assname=${RALATEUID}" target="_blank">腾讯</a>
                    <a title="分享到开心网" class="bshares-kaixin001" href="http://www.kaixin001.com/~repaste/repaste.php?rtitle=<?=$row['title']?>&rurl=<?=urlencode(SITEURL.'/index.php?mod=huodong&act=view&id='.$row['id'])?>" target="_blank">开心网</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php }?>
    <script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/buttonLite.js#style=-1&amp;uuid=<?=$webset['bshare']['uuid']?>&amp;pophcol=2&amp;lang=zh"></script><script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/bshareC0.js"></script>
<div><div class="megas512"><?=pageft($total,$pagesize,$page_url,WJT)?></div></div>
<?php }?>
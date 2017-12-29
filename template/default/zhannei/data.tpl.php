<?php
if(!defined('INDEX')){
	exit('Access Denied');
}

if(!class_exists('ddgoods')){
	include_once(DDROOT.'/comm/ddgoods.class.php');
	$ddgoods_class=new ddgoods($duoduo);
}

$pagesize=60;
$page=(int)$_GET['page'];
if($page==0) $page=1;
$page=($page-1)*($ajax_load_num+1)+1;
$q=$_GET['q'];
$where='1';

if($q!=''){
	$where.=' and title like"%'.$q.'%"';
	$url_arr['q']=$q;
}

$data=$ddgoods_class->index_list($pagesize,$page,$where,$ddgoods_total);
if($ddgoods_total==1){
	$total=$data['total'];
	$ddgoods_list=$data['data'];
}
else{
	$ddgoods_list=$data;
}
?>
<?php 
if(empty($ddgoods_list) && VIEW_PAGE==1){
	//include(TPLPATH.'/inc/nonetip.tpl.php');
}
?>
<?php foreach($ddgoods_list as $row){
	if($row['code']=='zhuanxiang' && WEBTYPE==0){
		$row['view']=l('tao','view',array('id'=>$row['id'],'stop'=>1));
	}
	?>
<li <?php if($row['code']=='zhuanxiang'){?> class="cell" id="<?=$row['id']?>&uid=<?=$dduser['id']?>" url="<?=u('tao','view',array('id'=>$row['id']))?>" youhui="<?=$row['discount_price']-$row['shouji_price']?>"<?php }?>>
         <div class="hover_f">
                                    <h6 class="J_tklink_tmall">【<a href="<?=$row['lanmuurl']?>"><?=$row['lanmuname']?></a>】<a href="<?=$row['view']?>"  target="_blank"><?=$row['title']?></a></h6>
                                    <div class="tuan_img">
                                        <a href="<?=$row['view']?>" target="_blank">
                                            <?=dd_html_img($row['img'].'_210x210.jpg',$row['title'])?>
                                        </a>
                                    </div>
                                    <?php 
									if($row['code']=='zhuanxiang'){
										$row['discount_price']=$row['shouji_price'];
									}
									?>
                                    <div class="money clear">
                                        <i class="yerrow left number"><bdo class="font20">&yen;</bdo><b><?=$row['discount_price']?></b></i>
                                        <a class="right tuangotobuy J_tklink_tmall" href="<?=$row['view']?>" target="_blank">去购买</a>
                                    </div>
                                    <div class="time clear">
                                        <span class="green left"><s class="yuan"></s><?=$row['price']?>元</span>
                                        <span class="yerrow left"><s class="sale"></s><?=round($row['discount_price']/$row['price'],2)*10?>折</span>
                                        <span class="gray left"><s class="go" title="有返利"></s></span>
                                    </div>
                                </div>
                            </li>
                        <?php }if(!defined('VIEW_PAGE')){exit;}?>
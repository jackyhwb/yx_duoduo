<?php
if(!defined('INDEX')){
	exit('Access Denied');
}

$type=$_GET['type'];
if($type=='goods'){
	$iid=iid_decode($_GET['iid']);
	$ddTaoapi = new ddTaoapi();
	$a=$ddTaoapi->taobao_tbk_tdj_get($iid,1,1);
	$url=$a['ds_item_click'];
	$url=$url=set_tao_click_uid($url,$dduser['id']);
}
elseif($type=='shop'){
	$nick=$_GET['nick'];
	$ddTaoapi = new ddTaoapi();
	$shop=$ddTaoapi->taobao_tbk_shops_detail_get($nick);
	if(is_array($shop)){
		$user_id=$shop['user_id'];
	}
	if($user_id>0){
		$a= $ddTaoapi->taobao_tbk_tdj_get($user_id,2);
		$url=$a['shop_click_url'];
	}
	if($url==''){
		$url='http://store.taobao.com/shop/view_shop.htm?user_number_id='.$user_id;
	}
	$url=str_replace('&k=','%26unid%3D'.$dduser['id'].'&k=',$url);
}
elseif($type=='s8'){
	$q=$_GET['q'];
	$ddTaoapi = new ddTaoapi();
	$url=$ddTaoapi->taobao_taobaoke_listurl_get($q,$dduser['id']);
}
click_jump($url);
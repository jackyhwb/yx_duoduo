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
/**
* @name 淘宝商品详情
* @copyright duoduo123.com
* @example 示例tao_view();
* @param $field 字段
* @param $q 关键字
* @param $iid 商品ID
* @return $parameter 结果集合
*/

function dd_act(){
	global $duoduo,$phone_app_status;
	$webset = $duoduo->webset;
	$dduser = $duoduo->dduser;
	include(DDROOT.'/comm/goods.class.php');
	$goods_class=new goods($duoduo);
	$id=(int)$_GET['id'];
	$iid=iid_decode($_GET['iid']);
	if($id==0&&empty($iid)){
		error_html('参数错误');
	}
	if($id){
		
		if($webset['wap']['status']==1 && is_mobile()==1){
			$url=wap_l('goods','view',array('id'=>$id));
			jump($url);
		}
		
		$goods=$goods_class->good($id);
		if(empty($goods)){
			error_html('没有该商品！');
		}
		if($goods['is_starttime']){
			if(!isset($phone_app_status)){
				if(app_status()==0){
					$goods['phone_app_status']=0;
				}
				else{
					$goods['phone_app_status']=1;
				}
			}
		}
		$goods['_url']=l('goods','view',array('code'=>$goods['code'],'id'=>$goods['id']));
		$where_id = 'goods_id="'.$id.'"';
	}
	elseif($iid){
		jump(u('tao','view',array('iid'=>$iid)));
	}
	if(!$goods['is_starttime']){
		if(WEBTYPE==0){
			jump($goods['jump']);
		}
	}
	
	if($goods['type']<=2){
		if($webset['wap']['status']==1 && is_mobile()==1){
			$url=u('wap','goods_view',array('id'=>$goods['id']));
			jump($url);
		}
	}
	
	if(FANLI==1){
		if($goods['domain']=='jd.com'){
			$a=dd_get('http://c.duomai.com/track.php?site_id=527&aid=61&euid=&t='.urlencode($goods['item_url']),3600);
			if(strpos($a,"var u='http://item.jd.com/")!==false){
				$duoduo->update('goods',array('endtime'=>TIME),'id="'.$goods['id'].'"');
				del_ddcache('','sql/goods');
				error_html('该商品超级返利活动已结束，<a style=" font-size:16px" href="'.$goods['item_url'].'" target="_blank">继续前往购买</a>   <a style=" font-size:16px" href="'.SITEURL.'">返回首页！</a>');
			}
		}
	}
	
	$bankuai=dd_get_cache('bankuai');
	$bankuai_title=$bankuai[$goods['code']]['title'];

	if(REPLACE<3){
		$noword_tag='';
	}
	else{
		$noword_tag='3';
	}
	$nowords=dd_get_cache('no_words'.$noword_tag);
	$goods['title']=dd_replace($goods['title'],$nowords);
	
	if($dduser['id']>0){
		$shoucang_goods = $duoduo->select('record_goods','id','type=2 and '.$where_id.' and uid="'.$dduser['id'].'"');
		if($shoucang_goods>0){
			$goods['is_shoucang'] = 1;
		}
		$cun = $duoduo->select('record_goods','*','uid="'.$dduser['id'].'" and '.$where_id.' and type=1');
		if(empty($cun)){
			$goods_ruku = $duoduo->select('goods','*','id='.$id);
			if(empty($goods_ruku)){
				$goods_ruku=$goods_class->good_api($iid);
			}
			$copy_goods = array('type'=>1,'goods_id'=>$goods_ruku['id'],'data_id'=>$goods_ruku['data_id'],'uid'=>$dduser['id'],'laiyuan'=>$goods_ruku['laiyuan'],'laiyuan_type'=>$goods_ruku['laiyuan_type'],'cid'=>$goods_ruku['cid'],'code'=>$goods_ruku['code'],'title'=>$goods_ruku['title'],'img'=>$goods_ruku['img']
			,'discount_price'=>$goods_ruku['discount_price'],'price'=>$goods_ruku['price'],'shouji_price'=>$goods_ruku['shouji_price'],'starttime'=>$goods_ruku['starttime'],'endtime'=>$goods_ruku['endtime'],'fanli_bl'=>$goods_ruku['fanli_bl'],'fanli_ico'=>$goods_ruku['fanli_ico']
			,'price_man'=>$goods_ruku['price_man'],'price_jian'=>$goods_ruku['price_jian'],'goods_attribute'=>$goods_ruku['goods_attribute'],'sell'=>$goods_ruku['sell']);
			$duoduo->insert('record_goods',$copy_goods);
		}
	}
	$seelog=array('type'=>'goods','id'=>$goods['id'],'pic'=>tb_img($goods['img'],100),'title'=>$goods['title'],'price'=>$goods['discount_price']?$goods['discount_price']:$goods['price']);
	$parameter['goods']=$goods;
	$parameter['goods_class']=$goods_class;
	$parameter['bankuai_title']=$bankuai_title;
	$parameter['act_seelog']=$seelog;

	unset($duoduo);
	return $parameter;
}
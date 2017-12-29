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

function act_wap_view(){
	global $duoduo,$dd_tpl_data;
	$webset = $duoduo->webset;
	$dduser = $duoduo->dduser;

	$id=(int)$_GET['id'];
	
	include(DDROOT.'/comm/goods.class.php');
	$goods_class=new goods($duoduo);
	
	$goods=$goods_class->good($id);

	$share_url=wap_l('goods','view',array('id'=>$goods['id'],'rec'=>$dduser['id']));
	$webtitle=$goods['title'].'-'.$dd_tpl_data['title'];
	
	unset($duoduo);
	unset($webset);
	unset($dduser);
	unset($dd_tpl_data);
	$parameter['shuju_data']=$goods;
	$parameter['webtitle']=$webtitle;
	$parameter['share_url']=$share_url;
	return $parameter;
}
?>
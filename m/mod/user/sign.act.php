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

function act_wap_sign(){
	global $duoduo,$dd_tpl_data;
	$webset = $duoduo->webset;
	$dduser = $duoduo->dduser;
	
	if($_GET['sub']==1){
		$re=$duoduo->lianxu_sign($dd_tpl_data['sign_open'],$dd_tpl_data['sign']);
		exit(dd_json_encode($re));
	}
	
	$webtitle='签到有奖-'.$dd_tpl_data['title'];
	
	unset($duoduo);
	unset($webset);
	unset($dduser);
	unset($dd_tpl_data);
	$parameter['shuju_data']=$shuju_data;
	$parameter['webtitle']=$webtitle;
	return $parameter;
}
?>
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

if(!defined('ADMIN')){
	exit('Access Denied');
}

$name=ACT.'.tpl';

if($_POST['sub']!=''){
	unset($_POST['sub']);
	dd_set_cache($name,$_POST);
	jump('-1','保存成功');
}
else{
	$tag=dd_get_cache($name);
}
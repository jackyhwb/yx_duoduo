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
$offer=$webset['offer'];
$offer['siteid'] = $webset['siteid'];
$offer['key'] = defined('DDYUNKEY')?DDYUNKEY:'';
if($dduser['id']==0 || empty($dduser['id'])){
	jump(u('user','login',array('form'=>u(MOD,'index'))),'请先登录');
}
if($offer['siteid']=='' || $offer['key']==''){
	jump('task','index','请进入后台任务模块一开启');
}
if($offer['status']==0){
	jump(u('index'),'任务模块一未开启');
}
$memberid=urlencode($offer['siteid'].'|'.$dduser['id']);

$total=$duoduo->sum('task','point','memberid="'.$dduser['id'].'" and type="offer"');
$task=$duoduo->select_all('task as a,user as b','a.programname,a.point,b.ddusername','a.memberid=b.id  and a.type="offer" order by addtime desc limit 0,12');
?>
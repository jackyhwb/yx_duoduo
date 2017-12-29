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

function set_user_pwd($duoduo,$uid){
	$webset=$duoduo->webset;
	$old_pwd=trim($_GET['old_pwd']);
	$ddpwd=trim($_GET['ddpwd']);
	$pwd_confirm=trim($_GET['pwd_confirm']);
			
	if ($ddpwd != $pwd_confirm) {
		$json_data=array('s'=>0,'id'=>'2次密码不相同！');
		return $json_data;
	}
	if ($duoduo -> check_oldpass($old_pwd, $uid) == 'false') {
		$json_data=array('s'=>0,'id'=>'原密码错误！');
		return $json_data;
	} 
	if (reg_password($ddpwd) == 0) { // 密码格式
		$json_data=array('s'=>0,'id'=>'密码格式错误！');
		return $json_data;
	}
			
	$webset=$duoduo->webset;
	if ($webset['ucenter']['open'] == 1) {
		include DDROOT . '/comm/uc_define.php';
		include_once DDROOT . '/uc_client/client.php';
		$uc_name = iconv("utf-8", "utf-8//IGNORE", $dduser['ddusername']);
		$ucresult = uc_user_edit($uc_name, $old_pwd, $ddpwd);
	
		if ($ucresult == -1) {
			$json_data=array('s'=>0,'id'=>'密码错误！');
			return $json_data;
		}
	}
			
	$data=array('ddpassword'=>md5($ddpwd));
	$duoduo->update('user',$data,'id="'.$uid.'"');
	$json_data=array ("s" => 1);	
	user_login($uid, $data['ddpassword'], 3600*30*24); //重置登陆状态
	return $json_data;
}
		

function act_wap_user_pwd(){
	global $duoduo,$dd_tpl_data;
	$webset = $duoduo->webset;
	$dduser = $duoduo->dduser;
	
	if($dduser['id']==0){
		jump(wap_l('user','login'));
	}

	if($_GET['sub']!=''){
		$re=set_user_pwd($duoduo,$dduser['id']);
		exit(dd_json_encode($re));
	}
	
	$title='修改密码';
	$webtitle=$title.'-'.$dd_tpl_data['title'];
	
	unset($duoduo);
	unset($webset);
	unset($dduser);
	unset($dd_tpl_data);
	$parameter['webtitle']=$webtitle;
	return $parameter;
}
?>
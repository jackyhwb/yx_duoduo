<?php
if(!defined('INDEX')){
	exit('Access Denied');
}

include(DDROOT.'/comm/zhidemai.class.php');
$zhidemai_class=new zhidemai($duoduo);
$id=$_GET['id'];
$url=$zhidemai_class->jump($id);
jump($url);
?>
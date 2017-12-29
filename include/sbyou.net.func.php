<?php
/*-----------------
独立功能PHP程序
开发者：枯叶天
淘宝店铺：669977.TaoBao.com
演示站：www.SBYOU.net
官网：www.669977.net
QQ：1981255858

此页为全局函数，涉及整站功能，请谨慎修改
-----------------*/

//全局参数，勿改勿删
$sbyou_net_authorID='www.sbyou.net';

//版本相关数据处理，勿改勿删
if($VERSIONS_ID=='0'){
	$sbyou_net_id='0';
	$sbyou_net_chapter_id='0';
}
if($VERSIONS_ID=='50'){
	$sbyou_net_id='8688';
	$sbyou_net_chapter_id='9045';
}
if($VERSIONS_ID=='3000'){
	$sbyou_net_id='1486351';
	$sbyou_net_chapter_id='1487622';
}
//php 批量过滤post,get敏感数据
function stripslashes_array(&$array) {
  while(list($key,$var)=each($array)) {
	if ($key != 'argc' && $key != 'argv' && (strtoupper($key) != $key || ''.intval($key) == "$key")) {
	  if (is_string($var)) {
		$array[$key]=stripslashes($var);
	  }
	  if (is_array($var))  {
		$array[$key]=stripslashes_array($var);
	  }
	}
  }
  return $array;
} 
// 替换HTML尾标签,为过滤服务
function lib_replace_end_tag($str)
{
  if (empty($str)) return false;
  $str=htmlspecialchars($str);
  $str=str_replace( '/', "", $str);
  $str=str_replace("\\", "", $str);
  $str=str_replace("&gt", "", $str);
  $str=str_replace("&lt", "", $str);
  $str=str_replace("<SCRIPT>", "", $str);
  $str=str_replace("</SCRIPT>", "", $str);
  $str=str_replace("<script>", "", $str);
  $str=str_replace("</script>", "", $str);
  $str=str_replace("select","select",$str);
  $str=str_replace("join","join",$str);
  $str=str_replace("union","union",$str);
  $str=str_replace("where","where",$str);
  $str=str_replace("insert","insert",$str);
  $str=str_replace("delete","delete",$str);
  $str=str_replace("update","update",$str);
  $str=str_replace("like","like",$str);
  $str=str_replace("drop","drop",$str);
  $str=str_replace("create","create",$str);
  $str=str_replace("modify","modify",$str);
  $str=str_replace("rename","rename",$str);
  $str=str_replace("alter","alter",$str);
  $str=str_replace("cas","cast",$str);
  $str=str_replace("&","&",$str);
  $str=str_replace(">",">",$str);
  $str=str_replace("<","<",$str);
  $str=str_replace(" ",chr(32),$str);
  $str=str_replace(" ",chr(9),$str);
  $str=str_replace("    ",chr(9),$str);
  $str=str_replace("&",chr(34),$str);
  $str=str_replace("'",chr(39),$str);
  $str=str_replace("<br />",chr(13),$str);
  $str=str_replace("''","'",$str);
  $str=str_replace("css","'",$str);
  $str=str_replace("CSS","'",$str);
   
  return $str;
 
}
//网站参数
function sbyou_net_sysconfig($obj){
	global $dsql,$cfg_indexurl;
	$sbyou_net=$dsql->GetOne("select value from dede_sysconfig where varname=\"$obj\" limit 1");
	if($sbyou_net['value']){
		return $sbyou_net['value'];
	}
}
//单个栏目
function SBYOU_NET_catalog($id,$obj){
	global $dsql,$cfg_indexurl;
	$sbyou_NET=$dsql->GetOne("select $obj from dede_arctype where id=\"$id\" limit 1");
	if($sbyou_NET[$obj]){
		return $sbyou_NET[$obj];
	}
}
//错误处理
function SBYOU_net_error($style,$obj){
	if($style=='1'){
		echo '<center>'.$obj.'</center>';
		exit;
	}
	if($style=='2'){
		header('Location:'.$obj);
		exit;
	}
}
//热门搜索
function SByou_net_search_keywords(){
	global $dsql,$cfg_indexurl,$SEARCH_URL;
	$dsql->SetQuery("select * from dede_search_keywords order by aid desc limit 5");
	$dsql->Execute();
	while($www_669977_net=$dsql->GetObject())
	{
		$SByou_Net.='<a href="'.$SEARCH_URL.'?fuck=subject&searchword='.$www_669977_net->keyword.'" title="'.$www_669977_net->keyword.'" target="_blank">'.$www_669977_net->keyword.'</a>';
	}
	return $SByou_Net;
}
//最新章节
function SbYOU_Net_NEW($id,$typedir){
	global $dsql,$cfg_indexurl;
	$SbYou_Net=$dsql->GetOne("select * from dede_archives where typeid=\"$id\" order by id desc limit 1");
	if($SbYou_Net['id']){
		if($typedir=='wap'){
			$www_sbyou_net='<a href="archive.php?aid='.$SbYou_Net['id'].'" title="'.$SbYou_Net['title'].'" target="_blank">'.$SbYou_Net['title'].'</a>';
		}else{
			$www_sbyou_net='<a href="'.$cfg_indexurl.$typedir.'/'.$SbYou_Net['id'].'.html" title="'.$SbYou_Net['title'].'" target="_blank">'.$SbYou_Net['title'].'</a>';
		}
	}else{
		$www_sbyou_net='<a>暂无最新章节</a>';
	}
	return $www_sbyou_net;
}
//随机推荐
function SByou_Net_rand($entry,$id1,$id2){
	global $dsql,$cfg_indexurl,$cfg_df_style;
	$id1=str_replace(array('\'','or'),'',htmlspecialchars($id1));
	if($id1){
		$topid='where topid='.$id1;
	}else{
		$topid='where topid!=0 and topid!=45 and booksize!=0 and topid!=375 and topid!=376';
	}
	$dsql->SetQuery("select * from dede_arctype $topid order by rand() limit $id2");
	$dsql->Execute();
	while($www_669977_net=$dsql->GetObject())
	{
		$typename=$www_669977_net->typename;
		$url=$cfg_indexurl.ltrim($www_669977_net->typedir,'/').'/';
		$topid=$www_669977_net->topid;

		$typeimg=ltrim($www_669977_net->typeimg,'/');
		if(!$typeimg){
		  $randPICID=rand(1,50);
		  $typeimg="uploads/empty/".$randPICID.".jpg";
		}

		if($entry=='bwjp'){
			$SByou_Net.='
			<div class="bw_box">
				<div class="t"><a href="'.$url.'" title="'.$typename.'" target="_blank">'.$typename.'</a><span>（'.$www_669977_net->booksize.'字）</span></div>
				<div class="pic"><a href="'.$url.'" title="'.$typename.'" target="_blank"><img src="'.$cfg_indexurl.$typeimg.'" alt="'.$typename.'"></a></div>
				<div class="a_l">
					<div class="a"><span>作者:</span>'.$www_669977_net->zuozhe.'</div>
					<div class="l"><span>标签:</span><a href="'.$cfg_indexurl.SBYOU_NET_catalog($topid,'typedir').'.html" title="'.SBYOU_NET_catalog($topid,'typename').'">'.SBYOU_NET_catalog($topid,'typename').'</a></div>
				</div>
				<div class="info">
					<p><a href="'.$url.'" target="_blank">'.$www_669977_net->description.'</a></p>
				</div>
			</div>
			';
		}
		if($entry=='randBOX'){
			$SByou_Net='
			<div class="h">
			  <h2>精品小说 随机推荐</h2>
			</div>
			<div class="bw_box">
			  <div class="t"><a href="'.$url.'" title="'.$typename.'" target="_blank">'.$typename.'</a><span>（'.$www_669977_net->booksize.'字）</span></div>
			  <div class="pic"><a href="'.$url.'" title="'.$typename.'" target="_blank"><img src="'.$cfg_indexurl.$typeimg.'" alt="'.$typename.'"></a></div>
			  <div class="a_l">
				<div class="a"><span>作者:</span>'.$www_669977_net->zuozhe.'</div>
				<div class="l"><span>类型:</span><a href="'.$cfg_indexurl.SBYOU_NET_catalog($topid,'typedir').'.html" title="'.SBYOU_NET_catalog($topid,'typename').'">'.SBYOU_NET_catalog($topid,'typename').'</a></div>
			  </div>
			  <div class="info">
				<p><a href="'.$url.'" target="_blank">'.$www_669977_net->description.'</a></p>
			  </div>
			</div>
			<div class="btn"><img src="'.$cfg_indexurl.'templets/'.$cfg_df_style.'/images/loading_data.gif" id="btn_img">
			  <input type="button" value="试试手气" onclick="ajax_data(\'randBOX\',\''.$id1.'\',\'1\')">
			</div>
			';
		}
		//更新数据
		sbyOu_NET_comment_auto($www_669977_net->id);
	}
	return $SByou_Net;
}
//作者专访
function sBYou_net_zzzf($topid){
	if(!$topid){
		$topid='topid!=0';
	}else{
		$topid='topid='.$topid;
	}
	global $dsql,$cfg_indexurl;
	$dsql->SetQuery("select * from dede_arctype where $topid and topid!=45 and zuozhe!='' order by rand() limit 1");
	$dsql->Execute();
	while($sbyou_net_row=$dsql->GetObject())
	{
		$sbyou_topid=$sbyou_net_row->topid;
		$ca_typename=SBYOU_NET_catalog($sbyou_topid,'typename');	
		$sbyou_typeimg=ltrim($sbyou_net_row->typeimg,'/');
		if(!$sbyou_typeimg){
			$randPICID=rand(1,50);
			$sbyou_typeimg="uploads/empty/".$randPICID.".jpg";
		}
		$SByou_Net='
		<div class="head">
			<h2>作者专访</h2>
			<span><a href="'.$cfg_indexurl.'paihang.html" title="更多作者" target="_blank">更多作者&nbsp;&gt;&gt;</a></span>
		</div>
		<div class="pic"><a href="'.$cfg_indexurl.ltrim($sbyou_net_row->typedir,'/').'/" title="'.$sbyou_net_row->typename.'" target="_blank"><img src="'.$cfg_indexurl.$sbyou_typeimg.'" alt="'.$sbyou_net_row->typename.'" /></a></div>
		<div class="name">'.$sbyou_net_row->zuozhe.'：</div>
		<div class="words"><a href="'.$cfg_indexurl.ltrim($sbyou_net_row->typedir,'/').'/" target="_blank">'.$sbyou_net_row->description.'</a></div>
		';
	}
	return $SByou_Net;
}
//代表作
function sbYou_Net_dbz($topid,$num=2){
	if(!$topid){
		$topid='topid!=0';
	}else{
		$topid='topid='.$topid;
	}
	global $dsql,$cfg_indexurl;
	$dsql->SetQuery("select * from dede_arctype where $topid and topid!=45 order by rand() limit ".$num);
	$dsql->Execute();
	while($sbyou_net_row=$dsql->GetObject())
	{
		$sbyou_topid=$sbyou_net_row->topid;
		$ca_typename=SBYOU_NET_catalog($sbyou_topid,'typename');	
		$sbyou_typeimg=ltrim($sbyou_net_row->typeimg,'/');
		if(!$sbyou_typeimg){
			$randPICID=rand(1,50);
			$sbyou_typeimg="uploads/empty/".$randPICID.".jpg";
		}
		$SByou_Net.='
		<div class="pic"><a href="'.$cfg_indexurl.ltrim($sbyou_net_row->typedir,'/').'/" title="'.$sbyou_net_row->typename.'" target="_blank"> <img src="'.$cfg_indexurl.$sbyou_typeimg.'" alt="'.$sbyou_net_row->typename.'" /> </a></div>
		';
	}
	$SByou_Net='
	<div class="head">
		<h2>代表作</h2>
		<span><a href="'.$cfg_indexurl.'shuku.html" title="更多作品" target="_blank">更多作品&nbsp;&gt;&gt;</a></span>
	</div>
	'.$SByou_Net;
	return $SByou_Net;
}
//成名作推荐
function SBYoU_Net_cmztj($topid){
	if(!$topid){
		$topid='topid!=0';
	}else{
		$topid='topid='.$topid;
	}
	global $dsql,$cfg_indexurl;
	$dsql->SetQuery("select * from dede_arctype where $topid and topid!=45 and booksize!=0 order by rand() limit 10");
	$dsql->Execute();
	while($sbyou_net_row=$dsql->GetObject())
	{
		$cmztj_i++;
		$sbyou_topid=$sbyou_net_row->topid;
		$ca_typename=SBYOU_NET_catalog($sbyou_topid,'typename');
		$sbyou_typeimg=ltrim($sbyou_net_row->typeimg,'/');
		if(!$sbyou_typeimg){
			$randPICID=rand(1,50);
			$sbyou_typeimg="uploads/empty/".$randPICID.".jpg";
		}
		$SByou_Net.='
		<li><a href="'.$cfg_indexurl.ltrim($sbyou_net_row->typedir,'/').'/" title="'.$sbyou_net_row->typename.'-'.$sbyou_net_row->zuozhe.'作品" target="_blank">'.$sbyou_net_row->typename.'</a>&nbsp;<font style="color:green;">['.$sbyou_net_row->booksize.'字]</font><span>'.$sbyou_net_row->zuozhe.'</span></li>
		';          
		if($cmztj_i==1){
			$SByou_Net.='
			<li class="first_con">
				<div class="pic"><a href="'.$cfg_indexurl.ltrim($sbyou_net_row->typedir,'/').'/" title="'.$sbyou_net_row->typename.'" target="_blank"><img class="lazy" src="'.$cfg_indexurl.$sbyou_typeimg.'" alt="'.$sbyou_net_row->typename.'" /></a></div>
				<div class="a_l">
					<div class="a"><span>作者:</span><a href="'.$cfg_indexurl.ltrim($sbyou_net_row->typedir,'/').'/" target="_blank" title="'.$sbyou_net_row->zuozhe.'作品">'.$sbyou_net_row->zuozhe.'</a></div>
					<div class="l"><span>类型:</span><a href="'.$cfg_indexurl.SBYOU_NET_catalog($sbyou_topid,'typedir').'.html" target="_blank" title="'.$ca_typename.'" >'.$ca_typename.'</a></div>
				</div>
				<div class="info">
					<p><a href="'.$cfg_indexurl.ltrim($sbyou_net_row->typedir,'/').'/" target="_blank">'.$sbyou_net_row->description.'</a></p>
				</div>
			</li>
			';
		}
	}
	return $SByou_Net;
}
//热门完本推荐
function SbYoU_Net_rmwbtj($topid){
	if(!$topid){
		$topid='topid!=0';
	}else{
		$topid='topid='.$topid;
	}
	global $dsql,$cfg_indexurl;
	$dsql->SetQuery("select * from dede_arctype where $topid and topid!=45 and booksize!=0 and overdate!='' order by rand() limit 10");
	$dsql->Execute();
	while($sbyou_net_row=$dsql->GetObject())
	{
		$rmwbtj_i++;
		$sbyou_topid=$sbyou_net_row->topid;
		$ca_typename=SBYOU_NET_catalog($sbyou_topid,'typename');
		$sbyou_typeimg=ltrim($sbyou_net_row->typeimg,'/');
		if(!$sbyou_typeimg){
			$randPICID=rand(1,50);
			$sbyou_typeimg="uploads/empty/".$randPICID.".jpg";
		}
		$SByou_Net.='
		<li><a href="'.$cfg_indexurl.ltrim($sbyou_net_row->typedir,'/').'/" title="'.$sbyou_net_row->typename.'-'.$sbyou_net_row->zuozhe.'作品" target="_blank">'.$sbyou_net_row->typename.'</a>&nbsp;<font style="color:green;">['.$sbyou_net_row->booksize.'字]</font><span>'.$sbyou_net_row->zuozhe.'</span></li>
		';          
		if($rmwbtj_i==1){
			$SByou_Net.='
			<li class="first_con">
				<div class="pic"><a href="'.$cfg_indexurl.ltrim($sbyou_net_row->typedir,'/').'/" title="'.$sbyou_net_row->typename.'" target="_blank"><img class="lazy" src="'.$cfg_indexurl.$sbyou_typeimg.'" alt="'.$sbyou_net_row->typename.'" /></a></div>
				<div class="a_l">
					<div class="a"><span>作者:</span><a href="'.$cfg_indexurl.ltrim($sbyou_net_row->typedir,'/').'/" target="_blank" title="'.$sbyou_net_row->zuozhe.'作品">'.$sbyou_net_row->zuozhe.'</a></div>
					<div class="l"><span>类型:</span><a href="'.$cfg_indexurl.SBYOU_NET_catalog($sbyou_topid,'typedir').'.html" target="_blank" title="'.$ca_typename.'" >'.$ca_typename.'</a></div>
				</div>
				<div class="info">
					<p><a href="'.$cfg_indexurl.ltrim($sbyou_net_row->typedir,'/').'/" target="_blank">'.$sbyou_net_row->description.'</a></p>
				</div>
			</li>
			';
		}
	}
	return $SByou_Net;
}
//起点章节处理
function sbyou_NET_qidian($url){
	global $BOOK_URL,$cfg_webname;
	$site=substr($url,1,4);
	if($site=='fhhk'){$site='http://files.qidian.com/';}
	$url=$site.substr($url,6);
	$sbyou_net_body=substr(file_get_contents($url),16,-3);
	
	$encode=mb_detect_encoding($sbyou_net_body,array('ASCII','GB2312','GBK','UTF-8'));
	if($encode=='UTF-8'){
		$sbyou_net_body=iconv('UTF-8','GB2312//IGNORE',$sbyou_net_body);
	}
	
	$preg='/(<a.*?>)(.*?)(<\/a>)/';
	$replace='<a href="'.$BOOK_URL.'">'.$cfg_webname.' 真心服务于广大书友，汇聚各大网站热门小说，免费阅读！</a>';
	//输出内容
	return preg_replace($preg,$replace,$sbyou_net_body);
}
//封面页心情评分
function SbYOU_NeT_score($id,$scoreID){
	global $dsql;
	$score='score_'.$scoreID;
	$ok=$dsql->ExecuteNoneQuery("update dede_arctype set $score=$score+1,bookclick=bookclick+3 where id=$id limit 1");
	if($ok){
		return '1';
	}else{
		return '0';
	}
}
//封面页评论
function SbYoU_neT_comments($id,$page){
	global $dsql,$cfg_indexurl,$cfg_df_style;

	$num='8';	
	
	$where_sql="select * from dede_comments where aid=$id";
	
	//计算总页数
	$dsql->SetQuery($where_sql);
	$dsql->Execute();
	while($total_row=$dsql->GetObject())
	{
		$total++;
	}
	//页数
	$pagenum=ceil($total/$num);
	$page=min($pagenum,$page);
	$prepg=$page-1;
	$nextpg=($page==$pagenum?0:$page+1);
	$offset=($page-1)*$num;
	
	//快捷页码
	!$pagenum?$prepg="":"";
	if($prepg){
	  $pre='<a href="javascript:ajax_data(\'com_box\',\''.$id.'\',\''.$prepg.'\',\'yes\');" title="上一页">&lt;&lt;</a>';
	  $first='<a href="javascript:ajax_data(\'com_box\',\''.$id.'\',\'1\',\'yes\');" title="第一页">|&lt;</a>';
	}else{
	  $pre='<a class="no" title="已经是第一页了">|&lt;</a>';
	  $first='<a class="no" title="没有上一页">&lt;&lt;</a>';
	}
	if($nextpg){
	  $next='<a href="javascript:ajax_data(\'com_box\',\''.$id.'\',\''.$nextpg.'\',\'yes\');" title="下一页">&gt;&gt;</a>';
	  $last='<a href="javascript:ajax_data(\'com_box\',\''.$id.'\',\''.$pagenum.'\',\'yes\');" title="最后一页">&gt;|</a>';
	}else{
	  $next='<a class="no" title="没有下一页">&gt;&gt;</a>';
	  $last='<a class="no" title="已经是最后一页了">&gt;|</a>';
	}
	//页码字符串
	$pageNav='
	<div class="bot_more">
	  <div class="page_info">每页显示<b>&nbsp;8&nbsp;</b>条评论，共&nbsp;<b>'.$total.'</b>&nbsp;条</div>
	  <div class="page_num">
		<div><img src="'.$cfg_indexurl.'templets/'.$cfg_df_style.'/images/loading_data.gif" id="cIMG"></div>
		<div><a class="info">第<b>'.$page.'</b>页/共'.$pagenum.'页</a>'.$first.$pre.'</div>
		<div>'.$next.$last.'</div>
	  </div>
	</div>
	';

	$dsql->SetQuery($where_sql." order by cid desc limit $offset,$num");
	$dsql->Execute();
	while($sbyou_net_row=$dsql->GetObject())
	{

		$randPICID=rand(1,100);
		$sbyou_typeimg="uploads/member/".$randPICID.".gif";

		$sbyou_net_list.='
		<li>
		  <div class="pic"><img src="'.$cfg_indexurl.$sbyou_typeimg.'" alt="'.$sbyou_net_row->mname.'"></div>
		  <div class="words">
			<h2>'.$sbyou_net_row->title.'</h2>
			<p>'.$sbyou_net_row->content.'</p>
		  </div>
		  <div class="info">
			<div class="name"><span>发表人：</span>'.$sbyou_net_row->mname.'</div>
			<div class="time"><span>发表于：</span>'.date('Y-m-d H:i:s',$sbyou_net_row->createdate).'</div>
			<div class="opt"> <a href="javascript:ajax_data(\'praises\',\''.$sbyou_net_row->aid.'\',\''.$sbyou_net_row->cid.'\',\'1\');" class="zc">支持[<span id="praises_'.$sbyou_net_row->aid.'_'.$sbyou_net_row->cid.'">'.$sbyou_net_row->votes1.'</span>]</a> <a href="javascript:ajax_data(\'praises\',\''.$sbyou_net_row->aid.'\',\''.$sbyou_net_row->cid.'\',\'0\');" class="fd">不支持[<span id="debases_'.$sbyou_net_row->aid.'_'.$sbyou_net_row->cid.'">'.$sbyou_net_row->votes2.'</span>]</a></div>
		  </div>
		</li>
		';
	}
	$sbyou_net_result='<ul class="ul_b_list">'.$sbyou_net_list.'</ul>'.$pageNav;
	if(!$total){
		$sbyou_net_result='
		<ul class="ul_b_list" id="ul_b_list">
		  <br />
		  <br />
		  &nbsp;::&nbsp;这本小说还没有评论哦...快来抢占头条吧！-_-
		  <br />
		  <br />
		  <br />
		</ul>
		<div class="bot_more">
		  <div class="page_info">每页显示<b>&nbsp;8&nbsp;</b>条评论，共&nbsp;<b><font id="cms_comments">0</font></b>&nbsp;条</div>
		  <div class="page_num">
			<div><img src="'.$cfg_indexurl.'templets/'.$cfg_df_style.'/images/loading_data.gif" id="cIMG"></div>
			<div><a class="info">第<b>0</b>页/共0页</a><a class="no" title="已经是第一页了">|&lt;</a><a class="no" title="没有上一页">&lt;&lt;</a></div>
			<div><a class="no" title="没有下一页">&gt;&gt;</a><a class="no" title="已经是最后一页了">&gt;|</a></div>
		  </div>
		</div>
		';
	}
	return $sbyou_net_result;
}
//自动评论
function sbyOu_NET_comment_auto($aid){
	global $dsql,$COMMENT_AUTO;
	
	if(!$COMMENT_AUTO){
		return false;
	}
	
	$time=time();
	
	//---------
	include_once dirname(__FILE__).'/../cmDATA.php';//评论数组
	shuffle($title);
	shuffle($content);
	$randTITLE=$title[0];
	$randCONTENT=$content[0];

	$score=rand(1,5);
	$score='score_'.$score;
	
	if($randTITLE && $randCONTENT){
	//添加评论
	$dsql->ExecuteNoneQuery("insert into dede_comments (aid,cuid,mid,mname,title,content,score,createdate,updatedate,ip,checked) VALUES ('$aid','5','0','游客','$randTITLE','$randCONTENT','0','$time','$time','','1')");
	//添加总点击/搜索指数/总评论数/开心等评分/周月点击月周评论数
	$dsql->ExecuteNoneQuery("update dede_arctype set bookclick=bookclick+3,bookclickm=bookclickm+1,bookclickw=bookclickw+1,tuijian=tuijian+2,lastclick='$time',$score=$score+1 where id=$aid limit 1");
	}
	//---------
}
//GBK或UTF-8字条截取
function sbyou_nEt_cut_str($string,$sublen,$start=0,$code='UTF-8')
{
    if($code == 'UTF-8')
    {
        $pa="/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa,$string,$t_string);

        if(count($t_string[0]) - $start > $sublen) return join('',array_slice($t_string[0],$start,$sublen))."...";
        return join('',array_slice($t_string[0],$start,$sublen));
    }
    else
    {
        $start=$start*2;
        $sublen=$sublen*2;
        $strlen=strlen($string);
        $tmpstr='';

        for($i=0; $i< $strlen; $i++)
        {
            if($i>=$start && $i< ($start+$sublen))
            {
                if(ord(substr($string,$i,1))>129)
                {
                    $tmpstr.= substr($string,$i,2);
                }
                else
                {
                    $tmpstr.=substr($string,$i,1);
                }
            }
            if(ord(substr($string,$i,1))>129) $i++;
        }
        if(strlen($tmpstr)<$strlen ) $tmpstr.="...";
        return $tmpstr;
    }
}
//登陆信息相关
function js_write(&$content){
	global $relog;
	if($relog==''){
		echo "document.write('".str_replace(array("\n","\r"),' ',addslashes($content))."');";
	}else{
		echo str_replace('\"','',str_replace(array("\n","\r"),' ',addslashes($content)));
	}
}
//手机版跳转功能
function SByou_NET_jump_wap($url){
	$sbyou_net='
	<script type="text/javascript"> 
		var is_mobile="";
		if(/AppleWebKit.*Mobile/i.test(navigator.userAgent) || (/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent))){
			if(window.location.href.indexOf("?mobile")<0){
				try{
					if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)){
						is_mobile="yes";
					}
					else if(/iPad/i.test(navigator.userAgent)){
						is_mobile="yes";
					}
					else{
						is_mobile="yes";
					}
				}
				catch(e){}
			}
		}
		else{
			is_mobile="no";
		}
		if(is_mobile=="yes"){
			window.location.href="'.$url.'";
		}
	</script>
	';
	return $sbyou_net;
}
//清空缓存
function SByou_NET_cache($dir){
	if(!file_exists($dir)){
		sbyou_Net_createdir($dir);
		return false;
	}
	//判断过期
	$filemtime=filemtime($dir);
	//当前时间
	$time=time();
	if(($filemtime+1)>$time){
		return false;
	}
  //先删除目录下的文件：
  $dh=opendir($dir);
  while ($file=readdir($dh)){
    if($file!="." && $file!=".."){
      $fullpath=$dir."/".$file;
      if(!is_dir($fullpath)){
          unlink($fullpath);
      }else{
          deldir($fullpath);
      }
    }
  }
 
  closedir($dh);
  //删除当前文件夹：
  if(rmdir($dir)){
		sbyou_Net_createdir($dir);
    return true;
  }else{
    return false;
  }
}
//新建文件夹
function sbyou_Net_createdir($dir){(file_exists($dir) && is_dir($dir))?'':mkdir($dir,0777);}
?>
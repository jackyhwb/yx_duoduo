<?php
require_once (dirname(__FILE__) . "/../include/dedecollection.class.php");
function clean_cachefiles( $path )
{
	$list = array();
	foreach( glob( $path . '/*') as $item )
	{
		if( is_dir( $item ) )
		{
			$list = array_merge( $list , clean_cachefiles( $item ) );
		}
		else
		{
			$list[] = $item;
		}
	}
	foreach( $list as $tmpfile )
	{
		if($tmpfile!="../data/tplcache/index.html") @unlink( $tmpfile );
	}
	return true;
}

$co = new DedeCollection();
$co->LoadNote($nid);
$orderway = "desc";
$noco=0;
$treid='a';
$treid2='b';
$pretid='c';
$replacearray=array(" ","!","?","？","（","）","(",")","！","，",".",":","。","：","【","】","[","]");
if($channelid==99) $con1=(intval($co_novelcount)==0) ? 0:1;//每次采集小说封面数量
elseif($channelid==98) $con1=(intval($co_novelcount)==0) ? 0:2;//每次采集小说封面数量(换站)
else $con1=(intval($co_artcount)==0) ? 0:3;//每次采集小说章节数量
$con=($con1==0) ? $con1+1:$con1;
//if($channelid>10 && $con1==0 && $cm==1) $co->GetSourceUrl(1,1,50);
$bscon=(intval($co_bscon)>0) ? intval($co_bscon):6;//一条规则中最多合并采集的小说数量
$co_retime=(intval($co_retime)>0) ? intval($co_retime):3;//错误重试次数
for($n=0;$n<$con;$n++)
{
	if($con1>0 || $eorid!=0)
	{
		if($noco==1) break;
		//echo "eorid:".$eorid."-aid:";
		if($eorid!=0)
		{
			$row = $dsql->GetOne("Select * From `#@__co_htmls` where aid=$eorid");
		}
		else
		{
			$row = $dsql->GetOne("Select MAX(aid) AS aid From `#@__co_htmls` where nid=$nid and isdown='0'");
			$raid=$row['aid'];
			$row = $dsql->GetOne("Select * From `#@__co_htmls` where aid='$raid'");
		}
		if(trim($co_nontitle)!="")
			$paichuarray=explode(",",$co_nontitle);//前两个字是这些字眼的章节名称都过滤掉
		else
			$paichuarray=array("a");
		if(is_array($row))
		{
			if($lasttag=="LAST")
			{
				$lasttitle=str_replace($replacearray,'',$lasttitle);
				$lastsql="select aid,title from `#@__co_htmls` where nid='$nid' order by aid";
				$dsql->Execute('c2',$lastsql);
				while ($lastidrow=$dsql->getarray('c2'))
				{
					$last_title=str_replace($replacearray,'',$lastidrow['title']);
					$last_aid=$lastidrow['aid'];
					similar_text($last_title,$lasttitle, $similarity_pst); //比较相似度
					if(stripos("ok".$lasttitle,$last_title)>0 || stripos("ok".$last_title,$lasttitle)>0 || number_format($similarity_pst, 0)>86)
					{
						$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=1,isexport=1 where nid='$nid' and aid>=$last_aid ");
						$dsql->ExecuteNoneQuery("update `#@__co_note` set remark='' where `nid`=$nid ");
						break;
					}
				}
			}
			$a_title=$row['title'];
			$c_title=reveser_c(str_replace($replacearray,'',$row['title']));
			$cc_title=substr($row['title'],0,4);
			$c_typeid=$row['typeid'];
			$aids=$row['aid'];
			//echo $aids."-".$row['title'];
			if($channelid<10)
			{
				if(in_array($cc_title,$paichuarray))
				{
					$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1,isdown=1 where aid='$aids' ");
					//echo " 【<font color='red'>$a_title-不符合要求-2</font>】<br>";
					continue;
				}
				$crow = $dsql->GetOne("Select count(a.id) as dd From `#@__archives` a left join `#@__addonarticle` b on(b.aid=a.id) where REVERSE(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(a.title,' ',''),'!',''),'！',''),'，',''),'?',''),'(',''),')',''),'？',''),'（',''),'）',''),'。',''),'：',''),':',''),'.',''),'[',''),']',''),'【',''),'】','')) like '$c_title%' and a.typeid='$c_typeid' and b.body<>''");
				if($crow['dd']>0)
				{
					$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1,isdown=1 where aid='$aids' ");
					//echo " 【<font color='red'>重复1-1-$c_typeid</font>】<br>";
					continue;
				}
			}
			else if($channelid==99)
			{
				$crow = $dsql->GetOne("Select * From `#@__arctype`  where typename='$a_title' and reid not in(0,45)");
				if(is_array($crow))
				{
					$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1,isdown=1 where aid='$aids' ");
					continue;
				}	
			}
			$co->DownUrl($row['aid'],$row['url'],$row['litpic']);
			$row = $dsql->GetOne("Select * From `#@__co_htmls` where aid='$aids'");
			if($row['isdown']=='1' && $row['result']!='')
			{
				//echo "-成功下载网址内容".$row['url'];
				//if($channelid==98) echo "<br />";
			}
			else if($channelid<10)
			{
				$suc="no";
				$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$aids' ");
				for($m=0;$m<$co_retime;$m++)
				{
					$co->DownUrl($row['aid'],$row['url'],$row['litpic']);
					$row = $dsql->GetOne("Select * From `#@__co_htmls` where aid='$aids'");
					if($row['isdown']=='1' && $row['result']!='')
					{
						$suc="ok";
						//echo "-成功下载网址内容".$row['url'];
						break;
					}
					else
					{
						$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$aids' ");
						//echo "-【<font color='red'>网址内容".$row['url']."采集失败，重新采集</font>】<br>";
					}
				}
				if($suc=="no")
				{				
					if($co_eorctr!='2' && $cotype!='2')//$co_eorctr值非法或者默认时，采集错误就自动停止采集
					{
						if($coretag=="re" && $coretimes==$co_retime)
						{
							$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$aids' ");
							$dsql->ExecuteNoneQuery("update `#@__co_note` set remark= CONCAT(remark,'访问错误，ID：','$aids'),typeid='0' where `nid`=$nid ");
							//echo "-【<font color='red'>网址内容".$row['url']."连续".$co_retime."次采集失败，取消本规则的采集</font>】<br>";	
						}
						else
						{
							
							$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$aids' ");
							$dsql->ExecuteNoneQuery("update `#@__co_note` set remark= CONCAT('re:','$coretime'),cotime=cotime+3600  where `nid`=$nid ");
							//echo "-【<font color='red'>网址内容".$row['url']."连续".$co_retime."次采集失败，等待重试</font>】<br>";	
						}
						break;
					}
					else
					{
						$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=1,isexport=1 where aid='$aids' ");
						//echo "-【<font color='red'>网址内容".$row['url']."连续".$co_retime."次采集失败，跳过该网址继续采集</font>】<br>";
					}
				}
			}
			else
			{
				$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1,isdown=1 where aid='$aids' ");
				//echo "-【<font color='red'>网址内容".$row['url']."采集失败</font>】<br>";
			}
		}
		else
		{
			if($new=="1" && $cotime!=0) $dsql->ExecuteNoneQuery("update `#@__co_note` set new=2 where `nid`=$nid ");
			//echo "没有可用网址了，先抓网址<br>";
			$co->GetSourceUrl(1,0,50);
			if($lasttag=="LAST")
			{
				$lasttitle=str_replace($replacearray,'',$lasttitle);
				$lastsql="select aid,title from `#@__co_htmls` where nid='$nid' order by aid";
				$dsql->Execute('c2',$lastsql);
				while ($lastidrow=$dsql->getarray('c2'))
				{
					$last_title=str_replace($replacearray,'',$lastidrow['title']);
					$last_aid=$lastidrow['aid'];
					similar_text($last_title,$lasttitle, $similarity_pst); //比较相似度
					if(stripos("ok".$lasttitle,$last_title)>0 || stripos("ok".$last_title,$lasttitle)>0 || number_format($similarity_pst, 0)>86)
					{
						$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=1,isexport=1 where nid='$nid' and aid>=$last_aid ");
						$dsql->ExecuteNoneQuery("update `#@__co_note` set remark='' where `nid`=$nid ");
						break;
					}
				}
			}
			$row = $dsql->GetOne("Select MAX(aid) AS aid From `#@__co_htmls` where nid=$nid and isdown='0'");
			$raid=$row['aid'];
			$row = $dsql->GetOne("Select * From `#@__co_htmls` where aid='$raid'");
			if(is_array($row))//还有没采集的内容
			{
				$a_title=$row['title'];
				$c_title=reveser_c(str_replace($replacearray,'',$row['title']));
				$cc_title=substr($row['title'],0,4);
				$c_typeid=$row['typeid'];
				$aids=$row['aid'];
				//echo $aids."-".$row['title'];
				if($channelid<10)
				{
					if(in_array($cc_title,$paichuarray))
					{
						$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1,isdown=1 where aid='$aids' ");
						//echo " 【<font color='red'>$a_title-不符合要求-2</font>】<br>";
						continue;
					}
					$crow = $dsql->GetOne("Select count(a.id) as dd From `#@__archives` a left join `#@__addonarticle` b on(b.aid=a.id) where REVERSE(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(a.title,' ',''),'!',''),'！',''),'，',''),'?',''),'(',''),')',''),'？',''),'（',''),'）',''),'。',''),'：',''),':',''),'.',''),'[',''),']',''),'【',''),'】','')) like '$c_title%' and a.typeid='$c_typeid' and b.body<>''");
					if($crow['dd']>0)
					{
						$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1,isdown=1 where aid='$aids' ");
						//echo " 【<font color='red'>重复1-1-$c_typeid</font>】<br>";
						continue;
					}
				}
				else if($channelid==99)
				{
					$crow = $dsql->GetOne("Select * From `#@__arctype`  where typename='$a_title' and reid not in(0,45)");
					if(is_array($crow))
					{
						$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1,isdown=1 where aid='$aids' ");
						continue;
					}	
				}
				$co->DownUrl($row['aid'],$row['url'],$row['litpic']);
				$row = $dsql->GetOne("Select * From `#@__co_htmls` where aid='$aids'");
				if($row['isdown']=='1' && $row['result']!='')
				{
					//echo "-成功下载网址内容".$row['url'];
					//if($channelid==98) //echo "<br />";
				}
				else if($channelid<10)
				{
					$suc="no";
					$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$aids' ");
					for($m=0;$m<$co_retime;$m++)
					{
						$co->DownUrl($row['aid'],$row['url'],$row['litpic']);
						$row = $dsql->GetOne("Select * From `#@__co_htmls` where aid='$aids'");
						if($row['isdown']=='1' && $row['result']!='')
						{
							$suc="ok";
							//echo "-成功下载网址内容".$row['url'];
							break;
						}
						else
						{
							$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$aids' ");
							//echo "-【<font color='red'>网址内容".$row['url']."采集失败，重新采集</font>】<br>";
						}
					}
					if($suc=="no")
					{				
						if($co_eorctr!='2' && $cotype!='2')//$co_eorctr值非法或者默认时，采集错误就自动停止采集
						{
							if($coretag=="re" && $coretimes==$co_retime)
							{
								$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$aids' ");
								$dsql->ExecuteNoneQuery("update `#@__co_note` set remark= CONCAT(remark,'访问错误，ID：','$aids'),typeid='0' where `nid`=$nid ");
								//echo "-【<font color='red'>网址内容".$row['url']."连续".$co_retime."次采集失败，取消本规则的采集</font>】<br>";	
							}
							else
							{
								
								$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$aids' ");
								$dsql->ExecuteNoneQuery("update `#@__co_note` set remark= CONCAT('re:','$coretime'),cotime=cotime+3600  where `nid`=$nid ");
								//echo "-【<font color='red'>网址内容".$row['url']."连续".$co_retime."次采集失败，等待重试</font>】<br>";	
							}
							break;
						}
						else
						{
							$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=1,isexport=1 where aid='$aids' ");
							//echo "-【<font color='red'>网址内容".$row['url']."连续".$co_retime."次采集失败，跳过该网址继续采集</font>】<br>";
						}
					}
				}
				else
				{
					$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1,isdown=1 where aid='$aids' ");
					//echo "-【<font color='red'>网址内容".$row['url']."下载失败</font>】<br>";
				}
			}
			else if($renid1=="1")//没有可采集的内容，已完结的结束采集
			{
				$dsql->ExecuteNoneQuery("update `#@__co_note` set remark='NULL-END',typeid='0' where `nid`=$nid ");	
				//下面判断是不是可以删除这个规则
				$typeidstart= (strpos($listconfig2,"typeid=>"));
				$typeidend= (strpos($listconfig2,"]{/dede:batchrule}"));
				$overtypeid=substr($listconfig2,$typeidstart+8,$typeidend-$typeidstart-8); 
				if(is_numeric($overtypeid))
				{
					$endid='';
					$endidsql="select id from `#@__archives` where typeid='$overtypeid' order by id desc limit 0,10";
					$dsql->Execute('c1',$endidsql);
					while ($endidrow=$dsql->getarray('c1'))
					{
						$endid .= ($endid=='' ? $endidrow['id'] : ','.$endidrow['id']);//查找最后10章的ID
					}
					$endkey=array("大结局","全书","后记","感言","完结","完本","新书","全文终","全文完","番外","终章","结局","尾声");
					for($m=0;$m<count($endkey);$m++)
					{
						$endrow = $dsql->GetOne("Select count(id) as dd From `#@__archives` where id in($endid) and title like '%$endkey[$m]%'");
						if($endrow['dd']>0)
						{
							//有完本字眼的就删除这条规则
							$dsql->ExecuteNoneQuery("Delete From `#@__co_htmls` where nid=$nid");
							$dsql->ExecuteNoneQuery("Delete From `#@__co_urls` where nid=$nid");
							$dsql->ExecuteNoneQuery("Delete From `#@__co_mediaurls` where nid=$nid");
							$dsql->ExecuteNoneQuery("Delete From `#@__co_note` where nid=$nid");
							$dsql->ExecuteNoneQuery("Delete From `dede_co_listurls` where nid in=$nid");
							//echo "【<font color='red'>符合完本条件，已删除本条规则！</font>】";
							break 2;
						}
					}
				}
				break;
			}
			else //没有可采集的内容
			{
				if($channelid==98)
				{
					$noco=1;
				}
				$rowa = $dsql->GetOne("Select * From `#@__co_htmls` where nid=$nid and isdown='1' and isexport='0'");
				if(!is_array($rowa))
				{
					if($channelid>10)
					{
						break;
					}
					elseif($booksum2<$bscon && $renid2!="0" && $nidurl=="")
					{
						if(strstr($notename2,'+')) $nidtype="+";
						else $nidtype="-";
						$bsm=$bscon-$booksum2+1;
						$rowlist=explode($nidtype, $notename2);
						$addnotename=str_replace($rowlist[0],"",$notename2);//要添加的规则名
						$addco_note=substr($listconfig2,stripos($listconfig2,'[(#)=>'),stripos($listconfig2,'{/dede:batchrule}')-stripos($listconfig2,'[(#)=>')+17);//要添加的列表源地址
						
						$row2 =$dsql->getone("SELECT * FROM `#@__co_note` where `renid`='$renid2' and booksum<$bsm and typeid<>'0' and nid<>'$nid' and cotime<>0 and con<5 order by booksum desc");
						if(is_array($row2))
						{
							$addnotename=$row2['notename'].$addnotename;
							$tonid=$row2['nid'];
							$booksum2=$row2['booksum']+$booksum2;
							$updatesql="update `#@__co_note` set `listconfig`=replace(replace(listconfig,'{/dede:batchrule}','".$addco_note."'),'".$row2['notename']."','".$addnotename."'),booksum='$booksum2',notename='$addnotename',typeid='1' where nid='$tonid'";
							if($dsql->ExecuteNoneQuery($updatesql))
							{
								$dsql->ExecuteNoneQuery("Update `#@__co_htmls` set nid='$tonid' where nid='$nid'; ");
								$dsql->ExecuteNoneQuery("Update `#@__co_urls` set nid='$tonid' where nid='$nid'; ");
								$dsql->ExecuteNoneQuery("Update `dede_co_listurls` set nid='$tonid' where nid='$nid'; ");
								$dsql->ExecuteNoneQuery("Update `#@__co_mediaurls` set nid='$tonid' where nid='$nid'; ");
								$dsql->ExecuteNoneQuery("Update `#@__arctype` set copynid='$tonid' where copynid='$nid'; ");
								$dsql->ExecuteNoneQuery("delete from `#@__co_note` where nid='$nid'; ");
							}
						}
					}
					break;
				}
			}
		}
	}
	$row = $dsql->GetOne("Select ct.id,ct.maintable,ct.addtable,ct.fieldset,cn.cotime From `#@__channeltype` ct left join `#@__co_note` cn on cn.channelid = ct.id where cn.nid='$nid' ");
	if(!is_array($row))
	{
		//echo "找不到频道内容模型信息，无法完成操作！";
		exit();
	}
	$channelid = $row['id'];
	$uptime = $row['cotime'];
	$Maitable  = $row['maintable'];
	$Addtable  = $row['addtable'];
	if(empty($Maitable))
	{
		$Maitable = '#@__archives';
	}
	if(empty($Addtable))
	{
		//echo "找不主表配置信息，无法完成操作！";
		exit();
	}
	$IndSqlTemplate = "INSERT INTO `#@__arctiny`(`arcrank`,`typeid`,`channel`,`senddate`,`sortrank`) VALUES ('$arcrank','@typeid@' ,'$channelid','@senddate@', '@sortrank@'); ";
	$MaiSqlTemplate  = "INSERT INTO `$Maitable`(id,typeid,sortrank,flag,ismake,channel,arcrank,click,money,title,shorttitle,color,writer,source,litpic,pubdate,senddate,mid,description,keywords) VALUES ('@aid@','@typeid@','@sortrank@','@flag@','-1','$channelid','$arcrank','@click@','0','@title@','','','@writer@','@source@','@litpic@','@pubdate@','@senddate@','1','@description@','@keywords@'); ";
	$inadd_f = $inadd_v = '';
	$dtp = new DedeTagParse();
	$dtp->SetNameSpace('field','<','>');
	$dtp->LoadString($row['fieldset']);
	foreach($dtp->CTags as $ctag)
	{
		$tname = $ctag->GetTagName();
		$inadd_f .= ",`$tname`";
		$notsend = $ctag->GetAtt('notsend');
		$fieldtype = $ctag->GetAtt('type');
		if($notsend==1)
		{
			if($ctag->GetAtt('default')!='')
			{
				$dfvalue = $ctag->GetAtt('default');
			}
			else if($fieldtype=='int'||$fieldtype=='float'||$fieldtype=='number')
			{
				$dfvalue = '0';
			}
			else if($fieldtype=='dtime')
			{
				$dfvalue = time();
			}
			else
			{
				$dfvalue = '';
			}
			$inadd_v .= ",'$dfvalue'";
		}
		else
		{
			$inadd_v .= ",'@$tname@'";
		}
	}
	$AddSqlTemplate = "INSERT INTO `{$Addtable}`(`aid`,`typeid`{$inadd_f}) Values('@aid@','@typeid@'{$inadd_v})";
	$dtp = new DedeTagParse();
	$dsql->SetQuery("Select * From `#@__co_htmls` where isdown='1' and isexport='0' and nid=$nid ORDER BY aid desc");
	$dsql->Execute();
	while($row = $dsql->GetObject())
	{
		$makeindex='yes';
		$nidd=$row->nid;
		$title = $row->title;
		$exid = $row->aid;
		if($channelid>10)
		{
			$dtp->LoadString($row->result);
			if(!is_array($dtp->CTags)) continue;

			//获取时间和标题
			$title = $row->title;
			$exid = $row->aid;
			$overdate="0";
			$isover="no";
			foreach ($dtp->CTags as $ctag)
			{
				$itemName = $ctag->GetAtt('name');
				if($itemName == 'title' && $usetitle==0)
				{
					$title = trim($ctag->GetInnerText());
					if($title=='')
					{
						$title = $row->title;
					}
				}
				else if($itemName == 'source')
				{
					$source = trim($ctag->GetInnerText());
				}
				else if($itemName == 'writer')
				{
					$writer = trim($ctag->GetInnerText());
				}
				else if($itemName == 'body')
				{
					$body = trim($ctag->GetInnerText());
				}
				else if($itemName == 'bookimg')
				{
					$bookimg = trim($ctag->GetInnerText());
					$bookimg = substr($bookimg,stripos($bookimg,'/uploads'),stripos($bookimg,'{/dede:img}')-stripos($bookimg,'/uploads'));
				}
				else if($itemName == 'pubdate')
				{
					$pubdate = trim($ctag->GetInnerText());
					if($pubdate=="") $pubdate=date("Y-m-d H:i:s",time());
				}
				else if($itemName == 'bookurl')
				{
					$bookurl = trim($ctag->GetInnerText());
				}
				else if($itemName == 'copynid')
				{
					$copynid = trim($ctag->GetInnerText());
				}
				else if($itemName == 'overtag')
				{
					$overtag = trim($ctag->GetInnerText());
					$days=(time()-strtotime($pubdate))/86400;
					if($co_overbooktag!="")
					{
						$co_overbooktag=explode("||",$co_overbooktag);
						if(in_array($overtag,$co_overbooktag) || $days>30)
						{
							$overdate=$pubdate;
							$isover="yes";
						}						
					}
					elseif(in_array($overtag,array("已完成","已完毕","已完本","已完结","完结","完本","完成","全本","已全本")) || $days>30)
					{
						$overdate=$pubdate;
						$isover="yes";
					}
				}
			}
			if(($title=="" || $writer=="" || $bookurl=="") && ($channelid==98 || ($channelid==99 && $source=="")))
			{
				if($eorid!=$exid)
				{
					$nn=$n;
					$n=$n-$co_retime;
					$eorid=$exid;
					$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$exid' ");
					break;
				}		
				else if($n==$nn)//连续$co_retime次下载未成功，终止本规则的采集并给出错误提示
				{
					if($co_eorctr!='2' && $cotype!='2')//$co_eorctr值非法或者默认时，采集错误就自动停止采集
					{
						if($coretag=="re" && $coretimes==$co_retime)
						{
							$dsql->ExecuteNoneQuery("Update `#@__co_htmls` set isdown=1,isexport=1 where aid='$exid' ");
							echo "-【<font color='red'>小说“".$title."”连续3次都采集不到内容，放弃采集</font>】<br>";
							continue;
						}
						else
						{
							$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$exid' ");
							$dsql->ExecuteNoneQuery("update `#@__co_note` set remark= CONCAT('re:','$coretime') where `nid`=$nid ");
							echo "-【<font color='red'>小说“".$title."”连续3次都采集不到内容，等待重试</font>】<br>";	
						}
						break 2;
					}
					else
					{
						$eorid=0;
					}
				}
				else
				{
					$dsql->ExecuteNoneQuery("Update `#@__co_htmls` set isdown=0,isexport=0,result='' where aid='$exid' ");
					echo "-【<font color='red'>小说“".$title."”内容是空的，重新采集</font>】<br>";
					break;
				}
			}
			if($nn>0)
			{
				$n=$nn;
				$nn=0;
			}
			if($channelid==98)
			{
				$renidarray=array("133-1","133-2");
				$testrow = $dsql->GetOne("SELECT a.id,b.notename,b.listconfig,b.booksum,b.nid,b.renid,b.typeid FROM `#@__arctype` a left join `#@__co_note` b ON(b.nid=a.copynid) WHERE a.typename='$title' and a.zuozhe='$writer' and ((a.lastupdate+86400)<UNIX_TIMESTAMP() or a.booksize>200000 or b.typeid=3) and b.renid not like '$copynid-%'");
				if(is_array($testrow))
				{
					$bookid = $testrow['id'];
					$oldnotename = addslashes($testrow['notename']);
					$oldlistconfig = addslashes($testrow['listconfig']);
					$oldbooksum = $testrow['booksum'];
					$oldnid = $testrow['nid'];
					$oldrenid = $testrow['renid'];
					$oldtype=$testrow['typeid'];
					if(in_array($oldrenid,$renidarray) || $oldtype=='3')//原来的采集规则是起点的则替换
					{
						$row = $dsql->GetOne("Select * From `#@__co_note` where nid='$copynid'");
						if($row)
						{
							foreach($row as $k=>$v)
							{
								$$k = addslashes($v);
							}
							$itemconfig=addslashes($row['itemconfig']);
							$usemore = (empty($usemore) ? '0' : $usemore);
							//查找本书现在的最后一章
							$lastaidrow = $dsql->GetOne("SELECT title FROM `dede_archives` WHERE id=(SELECT MAX(id) AS id FROM `dede_archives` WHERE typeid='$bookid')");
							$lasttitle="LAST:".$lastaidrow['title'];
							//已经完本的
							if($isover=="yes")
							{
								$renid=$copynid."-1";
								$notename=str_replace("章节采集模版（不要删除）","",$row['notename'])."-".$title;
								$listconfig=addslashes($row['listconfig']);
								$co_note="{dede:batchrule}[(#)=>".$bookurl."; (*)=>1-1; typeid=>".$bookid."]{/dede:batchrule}";
								$listconfig = str_replace($row['notename'],$notename,str_replace(substr($listconfig,stripos($listconfig,'{dede:batchrule}'),stripos($listconfig,'{/dede:batchrule}')-stripos($listconfig,'{dede:batchrule}')+17),$co_note,$listconfig));
								$cosql = " INSERT INTO `#@__co_note`(`channelid`,`notename`,`sourcelang`,`uptime`,`cotime`,`pnum`,`isok`,`listconfig`,`itemconfig`,`usemore`,booksum,renid,typeid,remark)
								   VALUES ('1','$notename','$sourcelang','".time()."','0','0','1','$listconfig','$itemconfig','1','1','$renid','1','$lasttitle'); ";
							}
							else
							{
								$renid=$copynid."-2";
								$notename=str_replace("章节采集模版（不要删除）","",$row['notename'])."+".$title;
								$listconfig=addslashes($row['listconfig']);
								$co_note="{dede:batchrule}[(#)=>".$bookurl."; (*)=>1-1; typeid=>".$bookid."]{/dede:batchrule}";
								$listconfig = str_replace($row['notename'],$notename,str_replace(substr($listconfig,stripos($listconfig,'{dede:batchrule}'),stripos($listconfig,'{/dede:batchrule}')-stripos($listconfig,'{dede:batchrule}')+17),$co_note,$listconfig));
								$cosql = " INSERT INTO `#@__co_note`(`channelid`,`notename`,`sourcelang`,`uptime`,`cotime`,`pnum`,`isok`,`listconfig`,`itemconfig`,`usemore`,booksum,renid,typeid,remark)
								   VALUES ('1','$notename','$sourcelang','".time()."','0','0','1','$listconfig','$itemconfig','1','1','$renid','1','$lasttitle'); ";
							}
							$dsql->ExecuteNoneQuery($cosql);
							$cnid = $dsql->GetLastID();
							$dsql->ExecuteNoneQuery("update `#@__arctype` set copynid='$cnid' where id=$bookid");
							//删除原来的规则
							if($oldbooksum>1)
							{
								$replacetname="+".$title;
								$newbooksum=$oldbooksum-1;
								$newnotename=str_replace($replacetname,"",$oldnotename);
								$newlistconfig=str_replace($replacetname,"",preg_replace('/\[\(#\)=>http:\/\/readbook.qidian.com\/bookreader\/[0-9]+\.html; \(\*\)=>1-1; typeid=>'.$bookid.'\]/i', '', $testrow['listconfig']));
								$dsql->ExecuteNoneQuery("update `#@__co_note` set notename='$newnotename',listconfig='$newlistconfig',booksum=$newbooksum where nid=$oldnid ");
								$dsql->ExecuteNoneQuery("Delete From `#@__co_htmls` where nid=$oldnid and typeid=$bookid");
							}
							else
							{
								$dsql->ExecuteNoneQuery("Delete From `#@__co_htmls` where nid=$oldnid");
								$dsql->ExecuteNoneQuery("Delete From `#@__co_urls` where nid=$oldnid");
								$dsql->ExecuteNoneQuery("Delete From `#@__co_mediaurls` where nid=$oldnid");
								$dsql->ExecuteNoneQuery("Delete From `#@__co_note` where nid=$oldnid");
								$dsql->ExecuteNoneQuery("Delete From `dede_co_listurls` where nid in=$oldnid");
							}
							//echo $title."-规则替换成功<br />";
						}
						else
						{
							$dsql->ExecuteNoneQuery("update `#@__co_note` set remark='eor:章节采集模版ID设置不正确',typeid='0' where `nid`=$nidd ");
							break 2;//章节规则不存在，跳出本次采集
						}
					}
					$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1 where aid='$exid' ");
				}
				continue;
			}

			//检测重复小说
			$testrow = $dsql->GetOne("SELECT COUNT(ID) AS dd FROM `#@__arctype` WHERE typename='$title' and zuozhe='$writer'");
			if($testrow['dd']>0)
			{
				$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1 where aid='$exid' ");
				//echo " 【<font color='red'>小说已存在</font>】<br>";
				continue;
			}
			$reidrow = $dsql->GetOne("SELECT * FROM `#@__arctype` WHERE reid=0 and content like '%$source%'");
			if(is_array($reidrow))
			{
				$retypeid=$reidrow['id'];
				$retypedir=$reidrow['typedir'];//小说分类的dir
			}
			else
			{
				$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1 where aid='$exid' ");
				continue;
			}
			
			$typepinyin = GetPinyin(stripslashes($title));
			$typepinyin = preg_replace("#\/{1,}#", "/", $typepinyin);
			//检查是否有重名的小说目录，如果有则目录拼音后自动添加数字区别
			$typedirrow = $dsql->GetOne("SELECT `typedir` FROM `#@__arctype` WHERE `typedir`='/$typepinyin'");
			if($typedirrow)
			{
				for($ti=1;$ti<100;$ti++)
				{
					$typedirsql="SELECT `typedir` FROM `#@__arctype` WHERE `typedir`='/".$typepinyin.$ti."'";
					$typedirrow = $dsql->GetOne($typedirsql);
					if(!$typedirrow)
					{
						$typepinyin = $typepinyin.$ti;
						break;
					}
				}
			}
			$typedir="/".$typepinyin;
			$addSqlType = "insert into `dede_arctype` (`reid`, `topid`, `sortrank`, `typename`, `typedir`, `isdefault`, `defaultname`, `issend`, `channeltype`, `maxpage`, `ispart`, `corank`, `tempindex`, `templist`, `temparticle`, `namerule`, `namerule2`, `modname`, `keywords`, `seotitle`, `moresite`, `sitepath`, `siteurl`, `ishidden`, `cross`, `crossid`, `content`, `smalltypes`, `bookclick`, `zuozhe`, `startdate`, `overdate`, `booksize`, `downloadurl`,`description`,`typeimg`) values('$retypeid','$retypeid','50','$title ','$typedir','-1','index.html','1','1','-1','0','0','{style}/catalog.htm','{style}/page.htm','{style}/article.htm','{typedir}/{aid}.html','/html{typedir}.html','default','','','0','$retypedir','','0','0','','','','0','$writer','0','$overdate','0','','$body','$bookimg')";
			if(!$dsql->ExecuteNoneQuery($addSqlType))
			{
				$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1 where aid='$exid' ");
				continue;
			}
			$bookid = $dsql->GetLastID();
			//变更小说URL格式
			if($bookrule != '' && $bookrule!="[拼音]" )
			{
				$pydir=GetPinyin(stripslashes($title),1);
				$typedir=str_replace('[拼音首字母]',$pydir,str_replace('[ID]',$bookid,str_replace('[拼音]',$typepinyin,$bookrule)));
				$typedir = preg_replace("#\/{1,}#", "/", $typedir);
				//检查是否有重名的小说目录，如果有则目录拼音后自动添加数字区别
				$typedirrow = $dsql->GetOne("SELECT `typedir` FROM `#@__arctype` WHERE `typedir`='/$typedir'");
				if($typedirrow)
				{
					for($ti=1;$ti<100;$ti++)
					{
						$typedirsql="SELECT `typedir` FROM `#@__arctype` WHERE `typedir`='/".$typedir.$ti."'";
						$typedirrow = $dsql->GetOne($typedirsql);
						if(!$typedirrow)
						{
							$typedir = $typedir.$ti;
							break;
						}
					}
				}
				$typedir="/".$typedir;
				$dsql->ExecuteNoneQuery("UPDATE `#@__arctype` SET `typedir`='$typedir' WHERE id='$bookid' ");
			}
						
			//插入小说章节采集规则
			$row = $dsql->GetOne("Select * From `#@__co_note` where nid='$copynid'");
			if($row)
			{
				foreach($row as $k=>$v)
				{
					if(!isset($$k))
					{
						$$k = addslashes($v);
					}
				}
				$itemconfig=addslashes($row['itemconfig']);
				$listconfig=addslashes($row['listconfig']);
				$usemore = (empty($usemore) ? '0' : $usemore);
				//已经完本的
				if($isover=="yes")
				{
					$renid=$copynid."-1";
					$notename=str_replace("章节采集模版（不要删除）","",$row['notename'])."-".$title;					
					$co_note="{dede:batchrule}[(#)=>".$bookurl."; (*)=>1-1; typeid=>".$bookid."]{/dede:batchrule}";
					$listconfig = str_replace($row['notename'],$notename,str_replace(substr($listconfig,stripos($listconfig,'{dede:batchrule}'),stripos($listconfig,'{/dede:batchrule}')-stripos($listconfig,'{dede:batchrule}')+17),$co_note,$listconfig));
					$cosql = " INSERT INTO `#@__co_note`(`channelid`,`notename`,`sourcelang`,`uptime`,`cotime`,`pnum`,`isok`,`listconfig`,`itemconfig`,`usemore`,booksum,renid,typeid)
					   VALUES ('1','$notename','$sourcelang','".time()."','0','0','1','$listconfig','$itemconfig','1','1','$renid','1'); ";
				}
				else
				{
					$renid=$copynid."-2";
					$notename=str_replace("章节采集模版（不要删除）","",$row['notename'])."+".$title;
					$co_note="{dede:batchrule}[(#)=>".$bookurl."; (*)=>1-1; typeid=>".$bookid."]{/dede:batchrule}";
					$listconfig = str_replace($row['notename'],$notename,str_replace(substr($listconfig,stripos($listconfig,'{dede:batchrule}'),stripos($listconfig,'{/dede:batchrule}')-stripos($listconfig,'{dede:batchrule}')+17),$co_note,$listconfig));
					$cosql = " INSERT INTO `#@__co_note`(`channelid`,`notename`,`sourcelang`,`uptime`,`cotime`,`pnum`,`isok`,`listconfig`,`itemconfig`,`usemore`,booksum,renid,typeid)
					   VALUES ('1','$notename','$sourcelang','".time()."','0','0','1','$listconfig','$itemconfig','1','1','$renid','1'); ";
				}
				$dsql->ExecuteNoneQuery($cosql);
				$cnid = $dsql->GetLastID();
				$dsql->ExecuteNoneQuery("update `#@__arctype` set copynid='$cnid' where id='".$bookid."' ");
			}
			else
			{
				$dsql->ExecuteNoneQuery("Delete From `#@__arctype` where id='".$bookid."'");
				$dsql->ExecuteNoneQuery("update `#@__co_note` set remark='eor:章节采集模版ID设置不正确',typeid='0' where `nid`=$nidd ");
				exit();
			}
			
			//增加新小说目录时如果没有对应的作者作品集，则自动创建一个，其中reid和topid要根据实际情况设定，目前都是45
			$row = $dsql->GetOne("SELECT `typedir` FROM `#@__arctype` WHERE reid=45 and `typename`='$writer'");
			if(!$row)
			{
				$zuozhedir = GetPinyin(stripslashes($writer));
				$zuozhedir = "/".$zuozhedir;
				$zuozhedir = preg_replace("#\/{1,}#", "/", $zuozhedir);
				$row = $dsql->GetOne("SELECT `typedir` FROM `#@__arctype` WHERE `typedir`='$zuozhedir'");
				if($row)
				{
					for($ti=1;$ti<100;$ti++)
					{
						$tsql="SELECT `typedir` FROM `#@__arctype` WHERE `typedir`='".$zuozhedir.$ti."'";
						$row = $dsql->GetOne($tsql);
						if(!$row)
						{
							$zuozhedir = $zuozhedir.$ti;
							break;
						}
					}
				}
				$zuozhe_in_query = "insert into `#@__arctype` (`reid`, `topid`, `sortrank`, `typename`, `typedir`, `isdefault`, `defaultname`, `issend`, `channeltype`, `maxpage`, `ispart`, `corank`, `tempindex`, `templist`, `temparticle`, `namerule`, `namerule2`, `modname`, `description`, `keywords`, `seotitle`, `moresite`, `sitepath`, `siteurl`, `ishidden`, `cross`, `crossid`, `content`, `smalltypes`, `bookclick`, `typeimg`, `zuozhe`, `startdate`, `overdate`, `booksize`, `downloadurl`) values('45','45','50','$writer','$zuozhedir','-1','index.html','0','1','-1','1','0','{style}/catalog45.htm','{style}/page45.htm','{style}/article.htm','{typedir}/{Y}/{M}{D}/{aid}.html','/html{typedir}.html','default','您现在浏览的是".$writer."的小说作品集，如果在阅读的过冲中发现我们的转载有问题，请及时与我们联系！特别提醒的是：小说作品一般都是根据作者写作当时的思考方式虚拟出来的，其情节虚构的成份比较多，切勿模仿！','','','0','/zuopinji','','1','0','','您现在浏览的是".$writer."的小说作品集，如果在阅读的过冲中发现我们的转载有问题，请及时与我们联系！特别提醒的是：小说作品一般都是根据作者写作当时的思考方式虚拟出来的，其情节虚构的成份比较多，切勿模仿！','','0','/images/jipin-default.jpg','','','0','0','')";
				$dsql->ExecuteNoneQuery($zuozhe_in_query);
				$zuozheid = $dsql->GetLastID();
				//变更作者URL格式
				if($zuozherule != '' && $zuozherule!="[拼音]" )
				{
					$pinyindir=substr($zuozhedir,1);
					$pydir=GetPinyin(stripslashes($writer),1);
					$zuozhedir=str_replace('[拼音首字母]',$pydir,str_replace('[ID]',$zuozheid,str_replace('[拼音]',$pinyindir,$zuozherule)));
					$zuozhedir = preg_replace("#\/{1,}#", "/", $zuozhedir);
					//检查是否有重名的目录，如果有就在最后自动添加数字区别
					$typedirrow = $dsql->GetOne("SELECT `typedir` FROM `#@__arctype` WHERE `typedir`='/$zuozhedir'");
					if($typedirrow)
					{
						for($ti=1;$ti<100;$ti++)
						{
							$typedirsql="SELECT `typedir` FROM `#@__arctype` WHERE `typedir`='/".$zuozhedir.$ti."'";
							$typedirrow = $dsql->GetOne($typedirsql);
							if(!$typedirrow)
							{
								$zuozhedir = $zuozhedir.$ti;
								break;
							}
						}
					}
					$zuozhedir="/".$zuozhedir;
					$dsql->ExecuteNoneQuery("UPDATE `#@__arctype` SET `typedir`='$zuozhedir' WHERE id='$zuozheid' ");
				}
				//将作者插入文档关键词中
				$zuozheurl = $zuozhedir.".html";
				if($co_autokeytype==1 || $co_autokeytype=3)
				{
					$row = $dsql->GetOne("SELECT `keyword` FROM `#@__keywords` WHERE `keyword`='$writer'");
					if(!$row && strlen($writer)>2)
					{
						$dsql->ExecuteNoneQuery("insert into `#@__keywords` (`keyword`, `rank`, `sta`, `rpurl`) values('$writer','30','1','$zuozheurl')");
					}
				}
			}
			//将小说插入文档关键词中
			if($co_autokeytype==2 || $co_autokeytype=3)
			{
				$row = $dsql->GetOne("SELECT `keyword` FROM `#@__keywords` WHERE `keyword`='$title'");
				if(!$row && strlen($title)>2)
				{
					$typeurl=$typedir."/";
					$dsql->ExecuteNoneQuery("insert into `#@__keywords` (`keyword`, `rank`, `sta`, `rpurl`) values('$title','30','1','$typeurl')");
				}
			}
			$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1 where aid='$exid' ");
			$dsql->ExecuteNoneQuery("update `#@__co_note` set remark='' where nid='$nid' ");
		}
		else
		{
			$clickstart=mt_rand(1,intval($co_addread));//点击随机增加
			$booktuijian=mt_rand(0,intval($co_addpraise));//推荐随机增加
			$gett=0;
			$body="";
			$typeid = $row->typeid != 0 ? $row->typeid : $typeid;
			$IndSql = str_replace('@typeid@',$typeid,$IndSqlTemplate);
			$MaiSql = str_replace('@typeid@',$typeid,$MaiSqlTemplate);
			$MaiSql = str_replace('@click@',$clickstart,$MaiSql);
			if($channelid!=1)
				$AddSql = str_replace('@typeid@',$typeid,$AddSql);
			else
				$AddSql = "INSERT INTO `#@__addonarticle`(`aid`,`typeid`,`body`) Values('@aid@',$typeid,'@body@')";
			$dtp->LoadString($row->result);
			$exid = $row->aid;
			if(!is_array($dtp->CTags)) continue;
			$pubdate = $sortrank = time();
			$title = $row->title;
			$litpic = '';
			foreach ($dtp->CTags as $ctag)
			{
				$itemName = $ctag->GetAtt('name');
				if($itemName == 'title')
				{
					$title = trim($ctag->GetInnerText());
					if($title=='') $title = $row->title;
				}
				else if($itemName == 'pubdate')
				{
					$pubdate = trim($ctag->GetInnerText());
					if(ereg("[^0-9]",$pubdate))
					{
						$pubdate = $sortrank = GetMkTime($pubdate);
						$gett=1;
					}
					else
					{
						$pubdate = $sortrank = time();
					}
				}
				else if($itemName == 'bookimg')
				{
					$bookimg = trim($ctag->GetInnerText());
					$bookimg = substr($bookimg,stripos($bookimg,'/uploads'),stripos($bookimg,'{/dede:img}')-stripos($bookimg,'/uploads'));
				}
				else if($itemName == 'litpic')
				{
					$litpic = trim($ctag->GetInnerText());
				}
				else if($itemName == 'body')
				{
					$body = trim($ctag->GetInnerText());
					$body = mb_decode_numericentity($body,array(0,0xffffff,0,0xffffff),'GBK');
					$body = str_replace('&amp;nbsp;','',$body);
				}
			}
			
			if($body=="" || strlen($body)-mb_strlen($body,'UTF8')<1)
			{
				if($eorid!=$exid)
				{
					$n=0;
					$eorid=$exid;
					$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$exid' ");
					break;
				}		
				else if($n==$co_retime)//连续$co_retime次下载未成功，终止本规则的采集并给出错误提示
				{
					if($co_eorctr!='2' && $cotype!='2')//$co_eorctr值非法或者默认时，采集错误就自动停止采集
					{
						if($coretag=="re" && $coretimes==$co_retime)
						{
							$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$exid' ");
							$dsql->ExecuteNoneQuery("update `#@__co_note` set remark= CONCAT(remark,'内容为空ID：','$exid'),typeid='0' where `nid`=$nid ");
							//echo "-【<font color='red'>章节“".$title."”连续3次都采集不到内容，取消本规则的采集</font>】<br>";
						}
						else
						{
							$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isdown=0,result='' where aid='$exid' ");
							$dsql->ExecuteNoneQuery("update `#@__co_note` set remark= CONCAT('re:','$coretime'),cotime=cotime+3600 where `nid`=$nid ");
							//echo "-【<font color='red'>章节“".$title."”连续3次都采集不到内容，等待重试</font>】<br>";	
						}
						$treid2='b';
						break 2;
					}
					else
					{
						$eorid=0;
					}
				}
				else
				{
					$dsql->ExecuteNoneQuery("Update `#@__co_htmls` set isdown=0,isexport=0,result='' where aid='$exid' ");
					//echo "-【<font color='red'>章节“".$title."”内容是空的，重新采集</font>】<br>";
					break;
				}
			}
			
			$title = addslashes($title);
			$title1=str_replace($replacearray,'',reveser_c($title));
			if($only)
			{
				$testrow = $dsql->GetOne("Select a.id,b.body From `#@__archives` a left join `#@__addonarticle` b on(b.aid=a.id) where REVERSE(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(a.title,' ',''),'!',''),'！',''),'，',''),'?',''),'(',''),')',''),'？',''),'（',''),'）',''),'。',''),'：',''),':',''),'.',''),'[',''),']',''),'【',''),'】','')) like '$title1%' and a.typeid='$typeid'");
				if(is_array($testrow))
				{
					$taid=$testrow['id'];
					$tbody=$testrow['body'];
					if($tbody!="")
					{
						$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1 where aid='$exid' ");
						//echo " 【<font color='red'>重复2</font>】<br>";
						continue;
					}
					else
					{
						$dsql->ExecuteNoneQuery("update `#@__addonarticle` set body='$body' where aid='$taid' ");
						$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1 where aid='$exid' ");
						//echo " 【<font color='green'>已更新</font>】<br>";
						continue;
					}
				}
			}
			$senddate = time();
			$flag = '';
			$IndSql = str_replace('@senddate@',$senddate,$IndSql);
			$IndSql = str_replace('@sortrank@',$sortrank,$IndSql);
			$MaiSql = str_replace('@flag@',$flag,$MaiSql);
			$MaiSql = str_replace('@sortrank@',$sortrank,$MaiSql);
			$MaiSql = str_replace('@pubdate@',$pubdate,$MaiSql);
			$MaiSql = str_replace('@senddate@',$senddate,$MaiSql);
			$MaiSql = str_replace('@title@',cn_substr($title, 100),$MaiSql);
			$AddSql = str_replace('@body@',$body,$AddSql);
			foreach($dtp->CTags as $ctag)
			{
				
				if($ctag->GetName()!='field') continue;
				$itemname = $ctag->GetAtt('name');
				$itemvalue = addslashes(trim($ctag->GetInnerText()));
				$MaiSql = str_replace("@$itemname@",$itemvalue,$MaiSql);
				if($co_nontext!="")
				{
					$co_nontext=explode("\r\n",$co_nontext);//后台设置的替换词为一行一个，这里要用双引号，单引号不起作用
					$AddSql = str_replace($co_nontext,"",str_replace("@$itemname@",$itemvalue,$AddSql));
				}
				else
					$AddSql = str_replace("@$itemname@",$itemvalue,$AddSql);
			}
			if($dsql->ExecuteNoneQuery($IndSql))
			{
				$aid = $dsql->GetLastID();
				$MaiSql= str_replace('@aid@',$aid,$MaiSql);
				$AddSql = str_replace('@aid@',$aid,$AddSql);
				$MaiSql= ereg_replace('@([a-z0-9]{1,})@','',$MaiSql);
				$AddSql = ereg_replace('@([a-z0-9]{1,})@','',$AddSql);
				if(!$dsql->ExecuteNoneQuery($MaiSql))
				{
					$dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$aid' ");
				}
				else
				{
					if(!$dsql->ExecuteNoneQuery($AddSql))
					{
						$dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$aid' ");
						$dsql->ExecuteNoneQuery("Delete From `$Maitable` where id='$aid' ");
					}
					else
					{
						/* $row =$dsql->getone( "select id as aid,arcrank,typeid,description,keywords from `#@__archives` where id=$aid");
						if($row)//自动生成文章的摘要和关键词
						{
							$aid = $row['aid'];
							$typeid = $row['typeid'];
							$arcrank = $row['arcrank'];
							$row['keywords'] = trim($row['keywords']);
							$row['description'] = trim($row['description']);
							if(strlen($row['description'])<3)
							{
								$arcrow= $dsql->getone("Select  body From {$Addtable}  where aid={$aid}");
								if($arcrow)
								{
									$body = $arcrow['body'];
									$bodytext = preg_replace("/#p#|#e#|副标题|分页标题/isU","",Html2Text($body));
									if(strlen($bodytext) > 1)
									{
										$des = trim(addslashes(cn_substr($bodytext,240)));
										if(strlen($des)<3)
										{
											$des = "-";
										}
										$dsql->ExecuteNoneQuery("Update #@__archives set description='{$des}' where id='{$aid}';");
									}
								}
							}
							if($row['keywords']!='' && !ereg(',',$row['keywords']))
							{
								$keyarr = explode(' ', $row['keywords']);
							}
							else
							{
								$keyarr = explode(',', $row['keywords']);
							}
							foreach($keyarr as $keyword)
							{
								$keyword = trim($keyword);
								if($keyword != '' && strlen($keyword)<13 )
								{
									$keyword = addslashes($keyword);
									$row = $dsql->getone("select id from `#@__tagindex` where tag='$keyword'");
									if(is_array($row))
									{
										$tid = $row['id'];
										$query = "update `#@__tagindex` set `total`=`total`+1 where id='$tid' ";
										$dsql->ExecuteNoneQuery($query);
									}
									else
									{
										$query = " Insert Into `#@__tagindex`(`tag`,`count`,`total`,`weekcc`,`monthcc`,`weekup`,`monthup`,`addtime`) values('$keyword','0','1','0','0','$timestamp','$timestamp','$timestamp');";
										$dsql->ExecuteNoneQuery($query);
										$tid = $dsql->GetLastID();
									}
									$query = "replace into `#@__taglist`(`tid`,`aid`,`typeid`,`arcrank`,`tag`) values ('$tid', '$aid', '$typeid','$arcrank','$keyword'); ";
									$dsql->ExecuteNoneQuery($query);
								}
							}
						} */
						if($make)
						{
							require_once(DEDEINC."/arc.archives.class.php");
							$ac=new Archives($aid);
							$rurl=$ac->MakeHtml();
							$ac->Close();
							$ac=new Archives($aid-1);
							$rurl=$ac->MakeHtml();
							$ac->Close();
							$dsql->ExecuteNoneQuery("Update `#@__co_note` set cotime='".time()."' where nid='$nid'; ");
						}
						
						//更新电子书章节
						$row = $dsql->GetOne("SELECT a.id,a.title,a.click,a.typeid,b.body,c.typename,c.zuozhe,c.bookclick,c.bookclickm,c.bookclickw,c.tuijian,c.tuijianm,c.tuijianw,c.booksize,c.lastclick,c.lasttuijian,c.startdate,c.reid,d.typename as retypename FROM `$Maitable` a left join `{$Addtable}` b on(a.id=b.aid) left join `#@__arctype` c on(a.typeid=c.id) left join `#@__arctype` d on(c.reid=d.id) WHERE a.id='$aid'");
						if($row)
						{
							$txt_filename=$row['zuozhe']."-".$row['typename'];
							$typename=$row['typename'];
							$txt_comtens="";
							$startdatesql="";
							$txt_title=$row['title'];
							$treid=$row['reid'];
							$tretypename=$row['retypename'];
							$updatetime=time();
							$lastclick=$row['lastclick'];
							$lasttuijian=$row['lasttuijian'];
							if(intval($cfg_weekstart)!='2') $beginweek=mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'));//本周开始时间戳,星期日~星期六为一周。
							else $beginweek=mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));//本周开始时间戳,星期一~星期日为一周。
							$beginmonth=mktime(0,0,0,date('m'),1,date('Y'));//本月开始时间戳
							$txt_body=$txt_title."\r\n".$row['body'];
							$txt_click=$row['click']+$row['bookclick'];
							$tuijian=$booktuijian+$row['tuijian'];
							$clickrow=$dsql->GetOne("SELECT MAX(lastclick) as lastclick FROM `#@__arctype` WHERE reid NOT IN (0,45)");
							$lastclickall=$clickrow['lastclick'];
							if($beginmonth<=$lastclick)//判断是否同一个月
								$txt_clickm=$row['click']+$row['bookclickm'];//月点击
							else
							{
								$txt_clickm=$row['click'];
								if($row['bookclickm']!=0 && $beginmonth>$lastclickall)
									$dsql->ExecuteNoneQuery("UPDATE `#@__arctype` SET bookclickm='0',tuijianm='0',lastclick=$beginmonth ");
							}
							if($beginweek<=$lastclick)//判断是否同一周
								$txt_clickw=$row['click']+$row['bookclickw'];//周点击
							else
							{
								$txt_clickw=$row['click'];
								if($row['bookclickw']!=0 && $beginmonth>$lastclickall)
									$dsql->ExecuteNoneQuery("UPDATE `#@__arctype` SET bookclickw='0',tuijianw='0',lastclick=$beginweek ");
							}
								
							if($beginmonth<=$lasttuijian)//判断是否同一个月
								$tuijianm=$booktuijian+$row['tuijianm'];//月推荐
							else
								$tuijianm=$booktuijian;
								
							if($beginweek<=$lasttuijian)//判断是否同一周
								$tuijianw=$booktuijian+$row['tuijianw'];//周推荐
							else
								$tuijianw=$booktuijian;
								
							if($gett==1&&$row['booksize']=='0'&&$row['startdate']=='0')
							{
								$startdate=date("Y-m-d",$pubdate);
								$startdatesql=",startdate='".$startdate."'";
							}
							$a=array('‘','’','“','”','–','—',' ','￠','￡','¤','￥','|','§','◎','《','》','°','±','2','3','1','�','×','÷','"','&','<','>','…','‰','<','>','←','↑','→','↓','-','√','∞','≠','≤','≥');
							$b=array('&lsquo;','&rsquo;','&ldquo;','&rdquo;','&ndash;','&mdash;','&nbsp;','&cent;','&pound;','&curren;','&yen;','&brvbar;','&sect;','&copy;','&raquo;','&laquo;','&deg;','&plusmn;','&sup2;','&sup3;','&sup1;','&euro;','&times;','&divide;','&quot;','&amp;','&lt;','&gt;','&hellip;','&permil;','&lsaquo;','&rsaquo;','&larr;','&uarr;','&rarr;','&darr;','&minus;','&radic;','&infin;','&ne;','&le;','&ge;');
							$txt_body=str_replace($b,$a,str_replace("<br />","\r\n",str_replace("<br>","\r\n",trim($txt_body))));
							$txt_body=str_replace("<p>","\r\n",str_replace("</p>","\r\n",trim($txt_body)));
							$txt_body=str_replace("\r\n\r\n","\r\n",str_replace("\r\n\r\n\r\n","\r\n",str_replace("\r\n\r\n\r\n\r\n","\r\n",$txt_body)));
							$acrlen=round(strlen($txt_body)/2.05);
							$booksize=$acrlen+$row['booksize'];							
							if(floor($row['booksize']/$co_addtxtpsize)<floor($booksize/$co_addtxtpsize) || $row['booksize']=='0')
							{
								$txt_comtens=str_replace("http://","",str_replace(array("[网站名称]","[站点根网址]"),array($cfg_webname,$cfg_basehost),$co_addtxttext))."\r\n\r\n";
							}
							$dsql->ExecuteNoneQuery("UPDATE `#@__arctype` SET bookclick='$txt_click',bookclickm='$txt_clickm',bookclickw='$txt_clickw',tuijian='$tuijian',tuijianm='$tuijianm',tuijianw='$tuijianw',booksize='$booksize',lastupdate='$updatetime',lastclick='$updatetime',lasttuijian='$updatetime'$startdatesql WHERE id='$typeid' ");
							$txt_comtens.=$txt_body."\r\n"."\r\n";
							if($co_autotxt!='2' && $channelid==1)
							{
								$file = fopen("$txt_filename.txt","ab");
								fwrite($file,$txt_comtens);
								fclose($file);
							}
							
							//更新小说首章id
							$startrow = $dsql->GetOne("SELECT COUNT(id) as dd FROM `#@__archives` WHERE typeid='$typeid' ");
							if($startrow['dd']=='1')
							{
								$dsql->ExecuteNoneQuery("UPDATE `#@__arctype` SET startaid='$aid',bookclick='".$row['click']."',bookclickm='".$row['click']."',bookclickw='".$row['click']."',tuijian='$booktuijian',tuijianm='$booktuijian',tuijianw='$booktuijian',booksize='$acrlen',lastupdate='$updatetime',lastclick='$updatetime',lasttuijian='$updatetime',downloadurl='/download/".$txt_filename.".txt' WHERE id='$typeid' ");
							}
							if($pretid!=$typeid && $pretid!='c')//只有当采集到的连续两个章节不是同一本书的时候，才更新上一本书，采集结束时更新最后一本书
							{
								$tid=$pretid;
								include_once(DEDEINC."/arc.listview.class.php");
								//echo '<br/>开始更新《'.$ptypename.'》-ID：'.$pretid;
								$lv = new ListView($tid);
								$reurl = $lv->MakeHtml();
								$lv->Close();
								if($treid2!=$treid && $treid2!='b')//只有当采集到的连续两个章节不是同一个分类的时候，才更新上一个分类，采集结束时最后一个分类
								{
									$tid=$treid2;
									$lv = new ListView($tid);
									$reurl = $lv->MakeHtml();
									$lv->Close();
									//echo '，更新分类：'.$tretypename2;
								}
								//echo '-成功';
							}
							$treid2=$row['reid'];//作为下一条记录的上一个分类
							$tretypename2=$row['retypename'];
							$pretid=$typeid;//作为下一条记录的上一本书ID
							$ptypename=$row['typename'];//作为下一条记录的上一本书名称
						}
					}
				}
				$dsql->ExecuteNoneQuery("update `#@__co_htmls` set isexport=1 where aid='$exid' ");
				$dsql->ExecuteNoneQuery("update `#@__co_note` set remark='' where nid='$nid' ");
			}
		}
		//echo " 【完成】<br>";
		$eorid=0;
	}
}
//更新当前采集规则还剩多少条没有采集，以便在规则自动合并时判断，剩余多余5条的就不要合并进去，免得影响采集效果 by QISIP 2014-04-04
$conrow = $dsql->GetOne("Select count(aid) as dd From `#@__co_htmls` where nid='$nid' and isdown=0");
$nidcon=$conrow['dd'];
$dsql->ExecuteNoneQuery("update `#@__co_note` set con='$nidcon' where nid='$nid' ");

//如果需要生成相关html
if($makeindex=='yes' && $treid2!='b')
{
	$tid=$typeid;
	include_once(DEDEINC."/arc.listview.class.php");
	//echo '开始更新《'.$typename.'》-ID：'.$typeid;
	$lv = new ListView($typeid);
	$reurl = $lv->MakeHtml();
	$lv->Close();
	$tid=$treid;
	$lv = new ListView($treid);
	$reurl = $lv->MakeHtml();
	$lv->Close();
	//echo '，更新分类：'.$tretypename.'-成功<br/>';
	clean_cachefiles( "../data/tplcache" );//删除模版缓存，以免文件爆满
	//生成首页 by QISIP 2014-04-01
	$homeFile =dirname(__FILE__)."/../html/index.html";
	$cfg_autoindex_time=(intval($cfg_autoindex_time)>=0) ? intval($cfg_autoindex_time):600;
	if(abs(time()-@filemtime($homeFile))>$cfg_autoindex_time)//首页、网站地图、排行榜10分钟更新一次，这些页面主要是针对搜索引擎爬行，书友不会关心这些太多
	{
		//生成首页 by QISIP 2014-04-01
		$tid='';
		$dsql->ExecuteNoneQuery("Delete From `#@__arccache`");
		include_once(DEDEINC."/arc.partview.class.php");
		$row = $dsql->GetOne("Select * From `#@__homepageset`");
		$row['templet'] = MfTemplet($row['templet']);
		$pv = new PartView();
		$pv->SetTemplet($cfg_basedir . $cfg_templets_dir . "/" . $row['templet']);
		$pv->SaveToHtml('../html/index.html');
		$dsql->Close();
		//生成sitemap by QISIP 2014-04-01
		$sitemaptemplet=$cfg_df_style."/sitemap.htm";
		$pv2 = new PartView();
		$pv2->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$sitemaptemplet);
		$sitemapFile ="../sitemap.html";
		$pv2->SaveToHtml($sitemapFile);
		//生成排行榜 by QISIP 2014-04-03
		$tid='375';
		$paihangbangtemplet=$cfg_df_style."/paihang.htm";
		$paihangbangtemplet = $cfg_basedir.$cfg_templets_dir."/".$paihangbangtemplet;
		if(file_exists($paihangbangtemplet))
		{
			$lv = new ListView($tid);
			$reurl = $lv->MakeHtml();
			$lv->Close();
		}
	}
}
setcookie("upflag",'1',(time()+$co_oldpertime));
?>
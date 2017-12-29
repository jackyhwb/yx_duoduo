<?php 
$shangjiaurl=DD_U_URL.'/index.php?g=alliance&m=Baoming&site_url='.urlencode(SITEURL).'&cur_url='.urlencode(u(MOD,ACT,array('lanmu'=>$lanmu)));
$url=$shangjiaurl.'&a=add&lanmu='.$lanmu;
$height='700';
if(ACT=='list'){
	$url=$shangjiaurl.'&a=lists';
	//$height='420px';
}
?>

<div id="ddiframediv" style="width:1000px; margin:auto; margin-top:10px;overflow:hidden">
<iframe  src="<?=$url?>" style="width:100%; height:<?=$height?>px;" frameborder="0"></iframe>
</div>
<script>
setInterval("listenHash()", 500);
var pageH = 0;
function listenHash() {
    var url = location.href;
    url = url.split('#');
    if (url[1] != '') {
		var p=parseInt(url[1]);
		if(p>0 && p!=pageH){
			var _p=parseInt(p+100);
			$('#ddiframediv iframe')[0].style.height=_p+'px';
			window.scrollTo(0,$('#ddiframediv')[0].offsetTop);
			pageH=p;
		}
    } 
}
listenHash();
</script>
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

$top_nav_name=get_lm($need_zhlm);
include(ADMINTPL.'/header.tpl.php');
?>
<script>
$(function(){
	<?=radio_guanlian('duomai[status]')?>;
	$('#duomai').show();
})
</script>
<form action="index.php?mod=<?=MOD?>&act=<?=ACT?>" method="post" name="form1">
<table id="addeditable" align="center" border=1 cellpadding=0 cellspacing=0 bordercolor="#dddddd">
  <tbody class="from_table" id="duomai">
  <tr>
    <td width="115px" align="right">状态：</td>
    <td>&nbsp;<?=html_radio($zhuangtai_arr,$webset['duomai']['status'],'duomai[status]')?> <span class="zhushi"><a href="<?=$lianmeng[4]['regurl']?>" target="_blank">注册</a> <a href="<?=$lianmeng[4]['helpurl']?>" target="_blank">教程</a></span></td>
  </tr>

  <tr class="duomaistatus_guanlian">
    <td align="right">多麦会员id号：</td>
    <td>&nbsp;<input id="" name="duomai[uid]" value="<?=$webset['duomai']['uid']?>" /> </td>
  </tr>
  <tr class="duomaistatus_guanlian">
    <td align="right">多麦网站id号：</td>
    <td>&nbsp;<input id="" name="duomai[wzid]" value="<?=$webset['duomai']['wzid']?>" /></td>
  </tr>
  <tr class="duomaistatus_guanlian">
    <td align="right">多麦网站编号：</td>
    <td>&nbsp;<input id="" name="duomai[wzbh]" value="<?=$webset['duomai']['wzbh']?>" /></td>
  </tr>
  <tr class="duomaistatus_guanlian">
    <td align="right">多麦密钥：</td>
    <td>&nbsp;<?=limit_input("duomai[key]")?><span class="zhushi">点击激活修改</span></td>
  </tr>
  <tr class="duomaistatus_guanlian">
    <td align="right">数据接收：</td>
    <td>&nbsp;<?=$status['duomai']?>&nbsp;</a><span class="zhushi">提示：如果异常。设置好后添加 1号店 商城并下单测试，下单后等待60分左右查看状态是否变化。</span></td>
  </tr>
  </tbody> 
  <tr>
     <td align="right">&nbsp;</td>
     <td>&nbsp;<input type="submit" name="sub" value=" 保 存 设 置 " /></td>
  </tr>
</table>
</form>
<form id="form2" target="_blank" method="post" action="http://www.chanet.com.cn/api/pt/duoduo_bind.cgi">
<input type="hidden" id="username" name="username" value=""/>
<input type="hidden" id="password" name="password" value=""/>
<input type="hidden" id="id" name="id" value=""/>
<input type="hidden" id="type" name="type" value="3"/>
<input type="hidden" id="url" name="url" value="http://<?=URL?>"/>
<input type="hidden" id="method" name="method" value="get"/>
<input type="hidden" id="encode" name="encode" value="utf8"/>
</form>
<?php include(ADMINTPL.'/footer.tpl.php');?>
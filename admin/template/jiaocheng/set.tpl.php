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

$jiaocheng_arr = array (
		array (
			'title' => '淘宝S8搜索教程地址',
			'code' => 'taos8',
			'url' => SITEURL.'/index.php?mod=mall&act=index',
		),
		array (
			'title' => '商城返利教程地址',
			'code' => 'mall',
			'url' => SITEURL.'/index.php?mod=mall&act=index',
		),
		array (
			'title' => '商城订单状态地址',
			'code' => 'mall_order',
			'url' => SITEURL.'/index.php?mod=user&act=tradelist&do=mall',
		),
		array (
			'title' => '站内购物引导地址',
			'code' => 'zhannei',
			'url' => SITEURL.'/index.php?mod=mall&act=index',
		),
		array (
			'title' => '清除cookie教程地址',
			'code' => 'cookie',
			'url' => SITEURL.'/index.php?mod=mall&act=view&id=1',
		),
		array (
			'title' => '会员等级说明地址',
			'code' => 'dengji',
			'url' => SITEURL.'/index.php?mod=user&act=index',
		),
		array (
			'title' => '添加爆料教程地址',
			'code' => 'baoliao',
			'url' => SITEURL.'/index.php?mod=zhidemai&act=index',
		),
		array (
			'title' => '添加淘宝帐号/订单号教程',
			'code' => 'tbnick',
			'url' => SITEURL.'/index.php?mod=user&act=info&do=tbnick',
		)
	);

include(ADMINTPL.'/header.tpl.php');
?>
<form action="index.php?mod=<?=MOD?>&act=<?=ACT?>" method="post" name="form1">
	<table id="addeditable" border=1 cellpadding=0 cellspacing=0 bordercolor="#dddddd">
    <? foreach($jiaocheng_arr as $r){ ?>
        <tr>
        <td align="right" width="180"><?=$r['title']?>：</span>
        </td>
        <td >&nbsp;<input style="width:400px"  name="jiaocheng[<?=$r['code']?>]" value="<?=$jiaocheng[$r['code']]?>" />&nbsp;<a href="<?=$r['url']?>" target="_blank">查看显示位置</a></td>
      </tr>
    <?php }?>
  <tr>
	<td align="right"></td>
	<td>&nbsp;<input type="hidden" name="from" value="jiaocheng_form"><input type="submit" name="sub" class="sub" value=" 保 存 设 置 " /></td>
  </tr>
</table>
</form>
<?php include(ADMINTPL.'/footer.tpl.php');?>
<?php

class plugin
{
    public $duoduo;
    public $plugin;
    public $plugin_code;
    public $_plugin;
    public function __construct($duoduo, $plugin_code)
    {
        $this->duoduo = $duoduo;
        $plugin = $this->duoduo->select('plugin', '*', 'code="' . $plugin_code . '"');
        $this->plugin = $plugin;
        $this->_plugin = (include DDROOT . '/plugin/' . $plugin_code . '/set.php');
        if ($_GET['do'] != 'uninstall') {
            $re = check_plugin_auth($plugin['code'], $plugin['key']);
            if ($re == 1) {
            //    dd_exit('授权码无法解密，请进入后台百宝箱重新获取订单');
            } elseif ($re == 2) {
              //  dd_exit('授权码无法解密，请进入后台百宝箱重新获取订单');
            } elseif ($re == 3) {
             //   dd_exit('网站没有购买此应用（<a href="' . DD_U_URL . '/index.php?m=bbx&a=view&code=' . $plugin_code . '">点击购买</a>）');
            } elseif ($re == 4) {
             //   dd_exit('授权码错误，可能引起的原因：1、域名更换（<a href="' . u('plugin', 'list') . '">重新获取百宝箱订单即可</a>）2、还未购买（<a href="' . DD_U_URL . '/index.php?m=bbx&a=view&code=' . $plugin_code . '">点击购买</a>）');
            } elseif ($re == 5) {
             //   dd_exit('应用已到期，请进入后台百宝箱续费（<a href="' . DD_YUN_URL . '/index.php?m=bbx&a=view&code=' . $plugin_code . '">点击购买</a>）');
            }
        }
    }
    public function check_status()
    {
        if ($this->plugin['status'] == 0) {
            error_html('应用已关闭');
        }
    }
    public function install_mod_act($row)
    {
        $duoduo = $this->duoduo;
        $mod = $row['mod'];
        $act = $row['act'];
        $title = $row['title'];
        $tag = $row['tag'];
        $alias_mod_act_arr = dd_get_cache('alias');
        if (!isset($alias_mod_act_arr[$mod . '/' . $act])) {
            $alias_mod_act_arr[$mod . '/' . $act] = array($mod, $act);
            dd_set_cache('alias', $alias_mod_act_arr);
        }
        if ($row['nav'] == 1) {
            $nav_id = $duoduo->select('nav', 'id', '`mod`="' . $mod . '" and `act`="' . $act . '"');
            if (!$nav_id) {
                $data = array('title' => $title, 'sort' => 99, 'mod' => $mod, 'act' => $act, 'tag' => $tag, 'plugin' => 1, 'addtime' => TIME);
                $duoduo->insert('nav', $data);
            }
        }
        $seo_id = $duoduo->select('seo', 'id', '`mod`="' . $mod . '" and `act`="' . $act . '"');
        if (!$seo_id) {
            $title = mysql_real_escape_string($title);
            $data = array('title' => $title . ' - {WEBNICK}', 'mod' => $mod, 'act' => $act, 'keyword' => $title . ' - {WEBNICK}', 'desc' => $title . ' - {WEBNICK}', 'label' => '{WEBNICK}-网站名称', 'sys' => 0, 'addtime' => TIME);
            $duoduo->insert('seo', $data);
            $title = template_parse(str_replace('\\\'', '\'', $title . ' - {WEBNICK}'));
            $keyword = template_parse(str_replace('\\\'', '\'', $title . ' - {WEBNICK}'));
            $desc = template_parse(str_replace('\\\'', '\'', $title . ' - {WEBNICK}'));
            $page_title = '<title>' . $title . '</title> <!--网站标题-->
';
            $page_title .= '<meta name="keywords" content="' . $keyword . '" />' . '
';
            $page_title .= '<meta name="description" content="' . $desc . '" />' . '
';
            $pagetag = $mod . '_' . $act;
            create_file(DDROOT . '/data/title/' . $pagetag . '.title.php', $page_title);
        }
        if ($mod != $tag) {
            $page_tag = dd_get_cache('page_tag', 'array');
            $add_page_tag = 1;
            foreach ($page_tag as $row) {
                if ($row['mod'] == $mod && $row['act'] == $act && $row['tag'] == $tag) {
                    $add_page_tag = 0;
                    break;
                }
            }
            if ($add_page_tag == 1 && $tag != '') {
                $page_tag[] = array('mod' => $mod, 'act' => $act, 'tag' => $tag);
                dd_set_cache('page_tag', $page_tag, 'array');
            }
        }
    }
    public function uninstall_mod_act($row)
    {
        $mod = $row['mod'];
        $act = $row['act'];
        $tag = $row['tag'];
        $alias_mod_act_arr = dd_get_cache('alias');
        if (isset($alias_mod_act_arr[$mod . '/' . $act])) {
            unset($alias_mod_act_arr[$mod . '/' . $act]);
            dd_set_cache('alias', $alias_mod_act_arr);
        }
        if ($row['nav'] == 1) {
            $nav_id = $this->duoduo->select('nav', 'id', '`mod`="' . $mod . '" and `act`="' . $act . '"');
            if ($nav_id > 0) {
                $this->duoduo->delete('nav', '`mod`="' . $mod . '" and `act`="' . $act . '"');
            }
        }
        $seo_id = $this->duoduo->select('seo', 'id', '`mod`="' . $mod . '" and `act`="' . $act . '"');
        if ($seo_id > 0) {
            $this->duoduo->delete('seo', '`mod`="' . $mod . '" and `act`="' . $act . '"');
            $pagetag = $mod . '_' . $act;
            unlink(DDROOT . '/data/title/' . $pagetag . '.title.php');
        }
        $page_tag = dd_get_cache('page_tag', 'array');
        $del_page_tag = 0;
        foreach ($page_tag as $k => $row) {
            if ($row['mod'] == $mod && $row['act'] == $act && $row['tag'] == $tag) {
                $del_page_tag = 1;
                break;
            }
        }
        if ($del_page_tag > 0 && $tag != '') {
            unset($page_tag[$del_page_tag]);
            dd_set_cache('page_tag', $page_tag, 'array');
        }
    }
    public function install_search()
    {
        $code = $this->plugin['code'];
        $row = $this->_plugin['search'];
        $a = dd_get_cache('plugin_nav_search');
        if (empty($a)) {
            $a = array();
        }
        $a[$code] = array('mod' => $code, 'act' => $row['act'], 'name' => $row['search_name'], 'value' => $row['search_tip'], 'width' => $row['search_width'], 'ver' => 2);
        dd_set_cache('plugin_nav_search', $a);
    }
    public function uninstall_search()
    {
        $code = $this->plugin['code'];
        $a = dd_get_cache('plugin_nav_search');
        if (isset($a[$code])) {
            unset($a[$code]);
            dd_set_cache('plugin_nav_search', $a);
        }
    }
    public function update_info($type = 'install')
    {
        $url = DD_YUN_URL . '/index.php?m=Api&a=one&code=' . $this->plugin['code'] . '&domain=' . DOMAIN;
        $re = dd_get_json($url);
        if ($re['s'] == 1) {
            $data['title'] = $re['r']['title'];
            $data['addtime'] = $re['r']['addtime'];
            $data['authcode'] = $re['r']['authcode'];
            $data['endtime'] = $re['r']['endtime'];
            $data['admin_set'] = (int) $this->_plugin['admin_set'];
            $data['search_open'] = $re['r']['search_open'];
            $data['search_name'] = $re['r']['search_name'];
            $data['search_width'] = $re['r']['search_width'];
            $data['search_tip'] = $re['r']['search_tip'];
            $data['toper_name'] = $re['r']['username'];
            $data['toper_qq'] = $re['r']['qq'];
            $data['banben'] = $re['r']['banben'];
            $data['jiaocheng'] = $re['r']['jiaocheng'];
            $data['version'] = $re['r']['version'];
        }
        if ($type == 'install') {
            $data['install'] = 1;
        }
        $this->duoduo->update('plugin', $data, 'id="' . $this->plugin['id'] . '"');
    }
    public function install()
    {
        $plugin = $this->plugin;
        $_plugin = $this->_plugin;
        if ($_plugin['need_include'] == 1) {
            $plugin_include = dd_get_cache('plugin_include');
            if (!in_array($plugin['code'], $plugin_include)) {
                $plugin_include[] = $plugin['code'];
            }
            dd_set_cache('plugin_include', $plugin_include);
        }
        foreach ($this->_plugin['act_arr'] as $row) {
            $row['mod'] = $this->plugin['code'];
            $this->install_mod_act($row);
        }
        if (!empty($this->_plugin['search'])) {
            $this->install_search();
        }
        foreach ($this->_plugin['table'] as $table => $field) {
            if ($table != '' && !empty($protected_table_arr) && !in_array($table, $protected_table_arr)) {
                $this->duoduo->delete_table($table);
            }
            $table = 'plugin_' . $table;
            $this->duoduo->creat_table($table, $field);
        }
        $sql = str_replace('{%BIAOTOU%}', BIAOTOU, $this->_plugin['install_sql']);
        if ($sql != '') {
            $sql_arr = explode(';', $sql);
            foreach ($sql_arr as $sql) {
                $this->duoduo->query($sql);
            }
        }
        $this->update_info();
        $plugin_set = dd_get_cache('plugin');
        $plugin_set[$plugin['code']] = 1;
        dd_set_cache('plugin', $plugin_set);
    }
    public function uninstall()
    {
        $plugin = $this->plugin;
        $_plugin = $this->_plugin;
        if ($_plugin['need_include'] == 1) {
            $plugin_include = dd_get_cache('plugin_include');
            foreach ($plugin_include as $k => $v) {
                if ($v == $plugin['code']) {
                    unset($plugin_include[$k]);
                }
            }
            dd_set_cache('plugin_include', $plugin_include);
        }
        $plugin_set = dd_get_cache('plugin');
        unset($plugin_set[$plugin['code']]);
        dd_set_cache('plugin', $plugin_set);
        foreach ($this->_plugin['act_arr'] as $row) {
            $row['mod'] = $this->plugin['code'];
            $this->uninstall_mod_act($row);
        }
        if (!empty($this->_plugin['search'])) {
            $this->uninstall_mod_act($row);
        }
        $protected_table = $this->_plugin['protected_table'];
        $protected_table_arr = explode(',', $protected_table);
        foreach ($this->_plugin['table'] as $table => $field) {
            if (!in_array($table, $protected_table_arr)) {
                $table = 'plugin_' . $table;
                $this->duoduo->delete_table($table);
            }
        }
        $sql = str_replace('{%BIAOTOU%}', BIAOTOU, $this->_plugin['uninstall_sql']);
        if ($sql != '') {
            $sql_arr = explode(';', $sql);
            foreach ($sql_arr as $sql) {
                $this->duoduo->query($sql);
            }
        }
        $data['install'] = 0;
        $data['status'] = 0;
        $this->duoduo->update('plugin', $data, 'id="' . $this->plugin['id'] . '"');
        $this->duoduo->delete('plugin_file', 'code="' . $this->plugin['code'] . '"');
        if ($this->_plugin['protected_file'] != 1) {
            deldir(DDROOT . '/plugin/' . $this->plugin['code']);
        }
        unlink(DDROOT . '/data/json/plugin/' . $this->plugin['code'] . '.php');
    }
}
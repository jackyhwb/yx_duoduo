<?php

function get_css_js_dir($tbver_119, $tbver_120)
{
    $tbver_117 = '';
    foreach ($tbver_119 as $tbver_116) {
        $tbver_117 .= $tbver_116;
    }
    $tbver_117 = dd_crc32($tbver_117);
    if (defined('ACT')) {
        $tbver_117 = ACT . '_' . $tbver_117;
    }
    if (defined('MOD')) {
        $tbver_117 = MOD . '_' . $tbver_117;
    }
    $tbver_117 = 'data/' . $tbver_120 . '/' . $tbver_117 . '.' . $tbver_120;
    return $tbver_117;
}
function css($tbver_112, $tbver_113 = '')
{
    if (defined('DEBUG') && DEBUG == 1) {
        foreach ($tbver_112 as $tbver_114 => $tbver_116) {
            echo '<link rel=stylesheet type=text/css href="' . $tbver_116 . '" />' . '
';
        }
        return '';
    }
    $tbver_115 = '';
    $tbver_121 = '';
    $tbver_117 = '';
    $tbver_122 = '';
    if (is_array($tbver_112)) {
        $tbver_117 = get_css_js_dir($tbver_112, 'css');
        $tbver_129 = DDROOT . '/' . $tbver_117;
        $tbver_130 = $tbver_117;
        foreach ($tbver_112 as $tbver_114 => $tbver_116) {
            if (preg_match('/[a-z]/', $tbver_114)) {
                $tbver_122 .= '<link rel=stylesheet type=text/css href="' . $tbver_116 . '" />' . '
';
                continue;
            }
        }
        if (is_file($tbver_129) && file_get_contents($tbver_129) != '') {
            return $tbver_122 . '<link rel=stylesheet type=text/css href="' . $tbver_113 . $tbver_130 . '" />' . '
';
        }
        foreach ($tbver_112 as $tbver_114 => $tbver_116) {
            if (preg_match('/[a-z]/', $tbver_114)) {
                $tbver_122 .= '<link rel=stylesheet type=text/css href="' . $tbver_116 . '" />' . '
';
                continue;
            }
            $tbver_115 = file_get_contents(DDROOT . '/' . $tbver_116);
            $tbver_131 = dirname($tbver_116) . '/';
            preg_match_all('`url\s*\(\s*(["\']?(\.\.\/)*[^\)]+)`', $tbver_115, $tbver_128);
            foreach ($tbver_128[1] as $tbver_127 => $tbver_123) {
                if (strpos($tbver_123, 'http://') !== false || strpos($tbver_123, 'https://') !== false) {
                    continue;
                }
                $tbver_124 = preg_replace('/"|\'/', '', $tbver_123);
                preg_match_all('/\.\.\//', $tbver_124, $tbver_125);
                $tbver_126 = $tbver_131;
                if ($tbver_125[0]) {
                    foreach ($tbver_125[0] as $tbver_111) {
                        $tbver_126 = dirname($tbver_126);
                    }
                    if ($tbver_126 == '.') {
                        $tbver_126 = '../../';
                    } elseif ($tbver_126) {
                        $tbver_126 = '../../' . $tbver_126 . '/';
                    }
                } else {
                    if (substr($tbver_124, 0, 1) == '/' || substr($tbver_124, 0, 1) == DIRECTORY_SEPARATOR) {
                        $tbver_126 = "";
                    } else {
                        $tbver_126 = '../../' . $tbver_126;
                    }
                }
                $tbver_110 = str_replace('../', '', $tbver_124);
                $tbver_96  = $tbver_128[0][$tbver_127];
                $tbver_97  = str_replace($tbver_124, $tbver_126 . $tbver_110, $tbver_96);
                $tbver_115 = str_replace($tbver_96, $tbver_97, $tbver_115);
            }
            $tbver_121 .= $tbver_115 . '/*' . $tbver_116 . '*/';
        }
        $tbver_121 = preg_replace('/[
]/', '', $tbver_121);
        $tbver_121 = preg_replace('/[\s]+/', ' ', $tbver_121);
        dd_file_put($tbver_129, $tbver_121);
        return $tbver_122 . '<link rel=stylesheet type=text/css href="' . $tbver_113 . $tbver_130 . '" />' . '
';
    }
}
function js($tbver_98, $tbver_113 = '')
{
    if (isset($tbver_98['a'])) {
        $tbver_95[] = $tbver_98['a'];
        unset($tbver_98['a']);
        $tbver_98 = array_merge($tbver_95, $tbver_98);
    }
    if (defined('DEBUG') && DEBUG == 1) {
        foreach ($tbver_98 as $tbver_114 => $tbver_116) {
            echo '<script type="text/javascript" src="' . $tbver_116 . '"></script>' . '
';
        }
        return '';
    }
    $tbver_115 = '';
    $tbver_94  = '';
    $tbver_117 = '';
    if (is_array($tbver_98)) {
        $tbver_117 = get_css_js_dir($tbver_98, 'js');
        $tbver_90  = DDROOT . '/' . $tbver_117;
        $tbver_91  = $tbver_117;
        if (is_file($tbver_90) && file_get_contents($tbver_90) != '') {
            return '<script type="text/javascript" src="' . $tbver_113 . $tbver_91 . '"></script>' . '
';
        }
        foreach ($tbver_98 as $tbver_116) {
            $tbver_94 .= file_get_contents(DDROOT . '/' . $tbver_116) . '
/*' . $tbver_116 . '*/
';
        }
        dd_file_put($tbver_90, $tbver_94);
        return '<script type="text/javascript" src="' . $tbver_113 . $tbver_91 . '"></script>' . '
';
    }
}
function curl_email($tbver_92, $tbver_93, $tbver_99, $tbver_100, $tbver_107, $tbver_108, $tbver_109, $tbver_115)
{
    list($tbver_106, $tbver_105) = explode(' ', microtime());
    $tbver_101   = curl_init();
    $tbver_102[] = 'HELO localhost';
    $tbver_102[] = 'AUTH LOGIN ' . base64_encode($tbver_93);
    $tbver_102[] = base64_encode($tbver_99);
    $tbver_102[] = 'MAIL FROM:<' . $tbver_100 . '>';
    $tbver_102[] = 'RCPT TO:<' . $tbver_107 . '>';
    $tbver_102[] = 'DATA';
    $tbver_102[] = 'MIME-Version:1.0
Content-Type:text/html
To: ' . $tbver_107 . '
From: ' . $tbver_108 . '<' . $tbver_100 . '>
Subject: ' . $tbver_109 . '
Date: ' . date('r') . '
X-Mailer:By Redhat (PHP/5.3.8)
Message-ID: <' . date('YmdHis', $tbver_105) . '.' . ($tbver_106 * 1000000) . '.' . $tbver_100 . '>

' . $tbver_115;
    $tbver_102[] = '
.
';
    $tbver_102[] = 'QUIT';
    $tbver_115   = '';
    foreach ($tbver_102 as $tbver_116) {
        $tbver_115 .= $tbver_116 . '
';
    }
    curl_setopt($tbver_101, CURLOPT_URL, $tbver_92);
    curl_setopt($tbver_101, CURLOPT_PORT, 25);
    curl_setopt($tbver_101, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($tbver_101, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($tbver_101, CURLOPT_CUSTOMREQUEST, $tbver_115);
    $tbver_103 = curl_exec($tbver_101);
    curl_close($tbver_101);
    return $tbver_103;
}
function mail_send($tbver_104, $tbver_109, $tbver_132, $tbver_133 = '', $tbver_162 = '', $tbver_92 = '', $tbver_120 = 0)
{
    global $webset;
    if ($tbver_120 == 0 || $tbver_120 == '') {
        $tbver_120 = $webset['smtp']['type'];
    }
    if ($tbver_120 == 2) {
        $tbver_163 = 'MIME-Version: 1.0' . '
';
        $tbver_163 .= 'Content-type:text/html;charset=utf-8' . '
';
        if ($tbver_133 != '') {
            $tbver_163 .= 'From: <' . $tbver_133 . '>' . '
';
        }
        $tbver_164 = mail($tbver_104, $tbver_109, $tbver_132, $tbver_163);
        if ($tbver_164 == 1) {
            $tbver_161 = 1;
        } else {
            $tbver_161 = 0;
        }
        return $tbver_161;
    }
    $tbver_133 = $tbver_133 ? $tbver_133 : $webset['smtp']['name'];
    $tbver_162 = $tbver_162 ? $tbver_162 : $webset['smtp']['pwd'];
    $tbver_92  = $tbver_92 ? $tbver_92 : $webset['smtp']['host'];
    unset($webset);
    if ($tbver_133 == '' || $tbver_162 == '' || $tbver_92 == '') {
        return 0;
    }
    $tbver_160 = WEBNICK;
    $tbver_156 = $tbver_133;
    if (!function_exists('fsockopen') && !function_exists('pfsockopen')) {
        if (!function_exists('curl_exec')) {
            return 'curl模块，fsockopen或者pfsockopen函数不可用，联系空间商为您开启此函数';
        }
        curl_email($tbver_92, $tbver_156, $tbver_162, $tbver_133, $tbver_104, $tbver_160, $tbver_109, $tbver_132);
        return 1;
    }
    if (!class_exists('PHPMailer')) {
        include(DDROOT . '/comm/class.phpmailer.php');
    }
    $tbver_157           = new PHPMailer();
    $tbver_157->CharSet  = 'UTF-8';
    $tbver_157->Encoding = 'base64';
    $tbver_157->IsSMTP();
    $tbver_157->SMTPAuth = true;
    $tbver_157->Host     = $tbver_92;
    $tbver_157->Username = $tbver_156;
    $tbver_157->Password = $tbver_162;
    $tbver_157->From     = $tbver_133;
    $tbver_157->FromName = $tbver_160;
    $tbver_157->WordWrap = 50;
    $tbver_157->IsHTML(true);
    $tbver_157->Subject = (isset($tbver_109)) ? $tbver_109 : '';
    $tbver_157->MsgHTML($tbver_132);
    if ($tbver_104) {
        $tbver_158 = explode('|', $tbver_104);
        foreach ($tbver_158 AS $tbver_127 => $tbver_159) {
            $tbver_157->AddAddress($tbver_159, "");
        }
    }
    if (!$tbver_157->Send()) {
        $tbver_161 = 0;
    } else {
        $tbver_161 = 1;
    }
    return $tbver_161;
}
function str_utf8_chinese_word_count($str = "")
{
    $tbver_170 = '/[\x{4e00}-\x{9fff}\x{f900}-\x{faff}]/u';
    $tbver_172 = '/[\x{ff00}-\x{ffef}\x{2000}-\x{206F}]/u';
    $str       = preg_replace($tbver_172, "", $str);
    return preg_match_all($tbver_170, $str, $tbver_119);
}
function str_utf8_english_count($str = "")
{
    $tbver_170 = '/[\x{4e00}-\x{9fff}\x{f900}-\x{faff}]/u';
    $tbver_172 = '/[\x{ff00}-\x{ffef}\x{2000}-\x{206F}]/u';
    $str       = preg_replace($tbver_172, "", $str);
    return strlen(preg_replace($tbver_170, "", $str));
}
function str_utf8_mix_word_count($str = "")
{
    $tbver_170 = '/[\x{4e00}-\x{9fff}\x{f900}-\x{faff}]/u';
    $tbver_172 = '/[\x{ff00}-\x{ffef}\x{2000}-\x{206F}]/u';
    $str       = preg_replace($tbver_172, "", $str);
    return str_utf8_chinese_word_count($str) + strlen(preg_replace($tbver_170, "", $str));
}
function authcode($tbver_173, $tbver_174 = 'DECODE', $tbver_127 = DDKEY, $tbver_175 = 0)
{
    $tbver_171 = 4;
    if (empty($tbver_127)) {
        $tbver_127 = DDKEY;
    }
    $tbver_127 = md5($tbver_127);
    $tbver_167 = md5(substr($tbver_127, 0, 16));
    $tbver_168 = md5(substr($tbver_127, 16, 16));
    $tbver_169 = $tbver_171 ? ($tbver_174 == 'DECODE' ? substr($tbver_173, 0, $tbver_171) : substr(md5(microtime()), -$tbver_171)) : '';
    $tbver_165 = $tbver_167 . md5($tbver_167 . $tbver_169);
    $tbver_155 = strlen($tbver_165);
    $tbver_173 = $tbver_174 == 'DECODE' ? base64_decode(substr($tbver_173, $tbver_171)) : sprintf('%010d', $tbver_175 ? $tbver_175 + TIME : 0) . substr(md5($tbver_173 . $tbver_168), 0, 16) . $tbver_173;
    $tbver_140 = strlen($tbver_173);
    $tbver_141 = '';
    $tbver_142 = range(0, 255);
    $tbver_139 = array();
    for ($tbver_138 = 0; $tbver_138 <= 255; $tbver_138++) {
        $tbver_139[$tbver_138] = ord($tbver_165[$tbver_138 % $tbver_155]);
    }
    for ($tbver_134 = $tbver_138 = 0; $tbver_138 < 256; $tbver_138++) {
        $tbver_134             = ($tbver_134 + $tbver_142[$tbver_138] + $tbver_139[$tbver_138]) % 256;
        $tbver_135             = $tbver_142[$tbver_138];
        $tbver_142[$tbver_138] = $tbver_142[$tbver_134];
        $tbver_142[$tbver_134] = $tbver_135;
    }
    for ($a = $tbver_134 = $tbver_138 = 0; $tbver_138 < $tbver_140; $tbver_138++) {
        $a                     = ($a + 1) % 256;
        $tbver_134             = ($tbver_134 + $tbver_142[$a]) % 256;
        $tbver_135             = $tbver_142[$a];
        $tbver_142[$a]         = $tbver_142[$tbver_134];
        $tbver_142[$tbver_134] = $tbver_135;
        $tbver_141 .= chr(ord($tbver_173[$tbver_138]) ^ ($tbver_142[($tbver_142[$a] + $tbver_142[$tbver_134]) % 256]));
    }
    if ($tbver_174 == 'DECODE') {
        if ((substr($tbver_141, 0, 10) == 0 || substr($tbver_141, 0, 10) - TIME > 0) && substr($tbver_141, 10, 16) == substr(md5(substr($tbver_141, 26) . $tbver_168), 0, 16)) {
            return substr($tbver_141, 26);
        } else {
            return '';
        }
    } else {
        return $tbver_169 . str_replace('=', '', base64_encode($tbver_141));
    }
}
function PutInfo($tbver_137, $tbver_143 = '', $tbver_144 = 0)
{
    $tbver_109 = '系统提示';
    $tbver_151 = '';
    $tbver_152 = '';
    if (is_numeric($tbver_143) && $tbver_143 < 0) {
        $tbver_151 = 'history.go(' . $tbver_143 . ');';
    } elseif ($tbver_143 != '') {
        $tbver_152 = '<meta http-equiv=\'refresh\' content=\'1;url=' . $tbver_143 . '\' />';
    }
    if ($tbver_144 == 1) {
        $tbver_137 .= '<br/><a href=\'' . $tbver_143 . '\'>如果浏览器没有跳转，请点击这里</a>';
    }
    $tbver_153 = '<html>
<head>
		<meta http-equiv=\'Content-Type\' content=\'text/html; charset=utf-8\' />
' . $tbver_152 . '
		<title>' . $tbver_109 . '</title>
		<base target=\'_self\'/>

		<script>setTimeout(function(){' . $tbver_151 . '},1000);</script>
		</head>
<body style=\'text-align:center\'>

		<br/>
		<div style=\'margin:auto;width:400px;\'>
		<div style=\'width:400px; padding-top:4px;height:24;font-size:10pt;border-left:1px solid #cccccc;border-top:1px solid #cccccc;border-right:1px solid #cccccc;background-color:#DBEEBD; text-align:center\'>' . $tbver_109 . '</div>
		<div style=\'width:400px;height:100px;font-size:10pt;border:1px solid #cccccc;background-color:#F4FAEB; text-align:center\'>
		<span style=\'line-height:160%\'><br/>' . $tbver_137 . '</span></div></div>';
    echo $tbver_153 . '
</body>
</html>';
    dd_exit();
}
function upload($tbver_150, $tbver_149 = '', $tbver_145 = 1)
{
    if (is_uploaded_file($_FILES[$tbver_150]['tmp_name'])) {
        $tbver_146                    = fs('pic_upload');
        $tbver_146->out_file_dir      = DDROOT . '/' . 'upload';
        $tbver_146->upload_form_field = $tbver_150;
        $tbver_146->saved_upload_name = $tbver_149;
        $tbver_146->upload_process();
        $tbver_147 = $tbver_146->saved_upload_name;
        $tbver_147 = str_replace(DDROOT . '/', '', $tbver_147);
        if ($tbver_146->error_no) {
            switch ($tbver_146->error_no) {
                case 1:
                    $tbver_148 = 201;
                    break;
                case 2:
                case 5:
                    $tbver_148 = 202;
                    break;
                case 3:
                    $tbver_148 = 203;
                    break;
                case 4:
                    $tbver_148 = 204;
                    break;
            }
            return $tbver_148;
        } else {
            if (FUJIAN_FTP == 1 && $tbver_145 <= 1) {
                $tbver_154 = array(
                    'hostname' => FTP_IP,
                    'username' => FTP_USER,
                    'password' => FTP_PWD,
                    'port' => FTP_PORT,
                    'pasv' => FTP_PASV,
                    'timeout' => FTP_TIMEOUT,
                    'mulu' => FTP_MULU,
                    'url' => FTP_URL
                );
                $tbver_89  = fs('ftp', $tbver_154);
                return $tbver_89->make_dir_file(DDROOT . '/' . $tbver_147, $tbver_145);
            } else {
                return $tbver_147;
            }
        }
    }
    return 1;
}
function template_parse($str)
{
    $str      = strip_tags($str);
    $str      = trim($str);
    $tbver_30 = str_replace('if ', '', $str);
    preg_match_all('/\{(.*?)\}/', $tbver_30, $a);
    foreach ($a[1] as $tbver_116) {
        if (preg_match('/[\n\r\t\s\{\}]+/i', $tbver_116)) {
            jump(-1, '模板解析失败！');
        }
    }
    $tbver_31 = '\(|\)|"|;|_post|_get|_request';
    if (preg_match('#' . $tbver_31 . '#i', $str)) {
        jump(-1, '模板解析失败！');
    }
    $tbver_32 = get_defined_functions();
    $tbver_32 = $tbver_32['internal'];
    $tbver_29 = array(
        'date',
        'time',
        'sleep',
        'file',
        'mail',
        'sort',
        'end',
        'next',
        'key',
        'min',
        'max',
        'ord',
        '_'
    );
    foreach ($tbver_32 as $tbver_116) {
        if (!in_array($tbver_116, $tbver_29)) {
            if (strpos($str, $tbver_116)) {
                jump(-1, '模板解析失败！' . $tbver_116);
            }
        }
    }
    $a   = include(DDROOT . '/data/template_replace.php');
    $str = strtr($str, $a);
    $str = preg_replace("/\{if\s+(.+?)\}/", "<?php if(\\1) {?>", $str);
    $str = preg_replace("/\{else\}/", "<?php } else { ?>", $str);
    $str = preg_replace("/\{elseif\s+(.+?)\}/", "<?php } elseif (\\1) {?>", $str);
    $str = preg_replace("/\{\/if\}/", "<?php } ?>", $str);
    $str = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str);
    $str = preg_replace("/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str);
    $str = preg_replace("/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $str);
    $str = preg_replace("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es", "addquote('<?php echo \\1;?>')", $str);
    $str = preg_replace("/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>", $str);
    return $str;
}

function getCategorySelect($tbver_28 = 0, $tbver_24 = 0, $tbver_25 = 0)
{
    global $duoduo;
    global $mod_tag;
    if (!isset($mod_tag)) {
        $mod_tag = MOD;
    }
    $tbver_26 = $duoduo->select_all('type', '*', 'pid="' . $tbver_24 . '" and tag="' . $mod_tag . '" order by sort desc');
    for ($tbver_27 = 0; $tbver_27 < $tbver_25 * 2 - 1; $tbver_27++) {
        $tbver_33 .= '&nbsp;';
    }
    if ($tbver_25++)
        $tbver_33 .= '┝';
    foreach ($tbver_26 as $tbver_34) {
        $tbver_24 = $tbver_34['id'];
        $tbver_41 = $tbver_34['title'];
        $tbver_42 = $tbver_28 == $tbver_24 ? 'selected' : '';
        echo '<option value="' . $tbver_24 . '" ' . $tbver_42 . '>' . $tbver_33 . ' ' . $tbver_41 . '</option>
';
        if ($tbver_24 > 0) {
            getCategorySelect($tbver_28, $tbver_24, $tbver_25);
        }
    }
}
function dd_strtotime($tbver_43)
{
    if ($tbver_43 > '2038-01-19 11:14:07') {
        $a        = explode(' ', $tbver_43);
        $tbver_40 = $a[0];
        if (isset($a[1])) {
            $tbver_39 = $a[1];
        } else {
            $tbver_39 = '00:00:00';
        }
        $tbver_35 = explode('-', $tbver_40);
        $tbver_36 = ceil(($tbver_35[0] - 2038) / 68);
        $tbver_37 = 0;
        for ($tbver_138 = 1; $tbver_138 < $tbver_36; $tbver_138++) {
            $tbver_37 += 2147483647;
        }
        $tbver_38 = $tbver_35[0] - 2038 - ($tbver_36 - 1) * 68 + 1970;
        $tbver_23 = $tbver_35[1];
        $tbver_22 = $tbver_35[2];
        $tbver_35 = explode(':', $tbver_39);
        $tbver_7  = $tbver_35[0];
        $tbver_8  = $tbver_35[1];
        $tbver_9  = $tbver_35[2];
        return 2147483647 + strtotime($tbver_10 = $tbver_38 . '-' . $tbver_23 . '-' . $tbver_22 . ' ' . $tbver_7 . ':' . $tbver_8 . ':' . $tbver_9) + 2793600 + $tbver_37;
    } else {
        $tbver_6 = strtotime($tbver_43);
        return $tbver_6 < 0 ? 0 : $tbver_6;
    }
}
function dd_date($tbver_5, $tbver_120 = 1)
{
    if ($tbver_5 > 2147483647) {
        $tbver_36 = ceil(($tbver_5 - 2147483647) / 2147483647);
        $tbver_37 = 0;
        for ($tbver_138 = 1; $tbver_138 < $tbver_36; $tbver_138++) {
            $tbver_37 += 1;
        }
        $tbver_43 = date('Y-m-d H:i:s', $tbver_5 - 2147483647 - 2793600 - $tbver_37 * 2147483647);
        $a        = explode(' ', $tbver_43);
        $tbver_40 = $a[0];
        $tbver_39 = $a[1];
        $tbver_35 = explode('-', $tbver_40);
        $tbver_38 = $tbver_35[0] - 1970 + 2038 + $tbver_37 * 68;
        $tbver_23 = $tbver_35[1];
        $tbver_22 = $tbver_35[2];
        $tbver_35 = explode(':', $tbver_39);
        $tbver_7  = $tbver_35[0];
        $tbver_8  = $tbver_35[1];
        $tbver_9  = $tbver_35[2];
        if ($tbver_120 == 1) {
            return $tbver_38 . '-' . $tbver_23 . '-' . $tbver_22 . ' ' . $tbver_7 . ':' . $tbver_8 . ':' . $tbver_9;
        } elseif ($tbver_120 == 2) {
            return $tbver_38 . '-' . $tbver_23 . '-' . $tbver_22;
        }
    } else {
        if ($tbver_120 == 1) {
            $tbver_6 = date('Y-m-d H:i:s', $tbver_5);
            if ($tbver_6 == '1970-01-01 08:00:00') {
                $tbver_6 = '';
            }
        } elseif ($tbver_120 == 2) {
            $tbver_6 = date('Y-m-d', $tbver_5);
            if ($tbver_6 == '1970-01-01') {
                $tbver_6 = '';
            }
        }
        return $tbver_6;
    }
}
function beijing_time()
{
    $tbver_1 = 'http://www.time.ac.cn/timeflash.asp?user=flash';
    $tbver_2 = dd_get_xml($tbver_1);
    if (is_array($tbver_2)) {
        $tbver_3 = $tbver_2['time']['year'] . '-' . $tbver_2['time']['month'] . '-' . $tbver_2['time']['day'] . ' ' . $tbver_2['time']['hour'] . ':' . $tbver_2['time']['minite'] . ':' . $tbver_2['time']['second'];
        $tbver_4 = strtotime($tbver_3);
    } else {
        $tbver_4 = 0;
    }
    return $tbver_4;
}
function get_domain($tbver_143 = '')
{
    if ($tbver_143 == '') {
        $tbver_143 = $_SERVER['HTTP_HOST'];
    }
    if (preg_match('#^http[s]?://#', $tbver_143)) {
        $tbver_143 = preg_replace('#http[s]?://#', '', $tbver_143);
    }
    if (preg_match('#^localhost#', $tbver_143) || preg_match('/127\.\d+\.\d+\.\d+/', $tbver_143) || preg_match('/192\.168\.\d+\.\d+/', $tbver_143) || preg_match('/172\.16|31\.\d+\.\d+/', $tbver_143)) {
        return 'localhost';
    }
    $a         = explode('/', $tbver_143);
    $tbver_143 = $a[0];
    preg_match('/^(\d+\.\d+\.\d+\.\d+)/', $tbver_143, $a);
    if (isset($a[1])) {
        return $a[1];
    }
    $tbver_11 = DOMAIN_PREG;
    preg_match($tbver_11, $tbver_143, $tbver_12);
    if (count($tbver_12) > 0) {
        $tbver_19 = $tbver_12[0];
    } else {
        $tbver_20 = parse_url($tbver_143);
        $tbver_21 = $tbver_20['host'];
        if (!strcmp(long2ip(sprintf('%u', ip2long($tbver_21))), $tbver_21)) {
            return $tbver_21;
        } else {
            $tbver_119 = explode('.', $tbver_21);
            $tbver_18  = count($tbver_119);
            $tbver_17  = array(
                'com',
                'net',
                'org',
                '3322'
            );
            if (in_array($tbver_119[$tbver_18 - 2], $tbver_17)) {
                $tbver_19 = $tbver_119[$tbver_18 - 3] . '.' . $tbver_119[$tbver_18 - 2] . '.' . $tbver_119[$tbver_18 - 1];
            } else {
                $tbver_19 = $tbver_119[$tbver_18 - 2] . '.' . $tbver_119[$tbver_18 - 1];
            }
        }
    }
    if ($tbver_19 == '.') {
        $tbver_19 = '';
    }
    return $tbver_19;
}
function get_host($tbver_143 = "")
{
    if (empty($tbver_143)) {
        return false;
    }
    if ($tbver_143 == 'localhost' || preg_match('/127\.\d+\.\d+\.\d+/', $tbver_143) || preg_match('/192\.168\.\d+\.\d+/', $tbver_143) || preg_match('/172\.16|31\.\d+\.\d+/', $tbver_143)) {
        return 'localhost';
    }
    if (!preg_match('/^http(s?):/is', $tbver_143))
        $tbver_143 = 'http://' . $tbver_143;
    $tbver_143 = parse_url(strtolower($tbver_143));
    return strtolower($tbver_143['host']);
}
function iptocity($tbver_13 = '')
{
    if ($tbver_13 == '') {
        $tbver_13 = get_client_ip();
    }
    if (preg_match('/127\.0\.\d+\.\d+/', $tbver_13) == 1) {
        return '本地';
    }
    $tbver_143 = 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $tbver_13;
    $a         = dd_get_json($tbver_143);
    return str_replace('市', '', $a['data']['city']);
}
function mobiletocity($tbver_14)
{
    if (reg_mobile($tbver_14) == 0) {
        return 0;
    }
    $tbver_143 = 'http://webservice.webxml.com.cn/WebServices/MobileCodeWS.asmx/getMobileCodeInfo?mobileCode=' . $tbver_14 . '&userID=';
    $a         = dd_get($tbver_143);
    preg_match('#<string xmlns="http://WebXml.com.cn/">\d+：(.*)</string>#', $a, $tbver_35);
    return $tbver_35[1];
}
function dd_crc32($str)
{
    return (float) sprintf('%u
', crc32($str));
}
function StopAttack(&$tbver_15 = array())
{
    if (empty($tbver_15)) {
        check_attack($_GET);
        check_attack($_POST);
        check_attack($_REQUEST);
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '') {
            $a = array(
                $_SERVER['HTTP_REFERER']
            );
            check_attack($a);
            $_SERVER['HTTP_REFERER'] = $a[0];
        }
    } else {
        check_attack($tbver_15);
    }
}
function check_attack(&$tbver_16)
{
    $tbver_44 = '\b(alert\(|confirm\(|prompt\()\b|<[^>]*?\b(onerror|onmousemove|onload|onclick|onmouseover)\b[^>]*?>|^\+\/v(8|9)|\b(and|or)\b\s*?([\(\)\'"\d]+?=[\(\)\'"\d]+?|[\(\)\'"a-zA-Z]+?=[\(\)\'"a-zA-Z]+?|>|<|\s+?[\w]+?\s+?\bin\b\s*?\(|\blike\b\s+?["\'])|\/\*.+?\*\/|<\s*script\b|\bEXEC\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\s+(TABLE|DATABASE)|\.\./\.\./';
    foreach ($tbver_16 as $tbver_114 => $tbver_116) {
        if (is_array($tbver_116)) {
            check_attack($tbver_116);
        } else {
            if (get_magic_quotes_gpc() == 0) {
                $tbver_116 = addslashes($tbver_116);
            }
            if (preg_match('#' . $tbver_44 . '#is', $tbver_116) == 1) {
                $tbver_137 = '操作IP: ' . $_SERVER['REMOTE_ADDR'] . '操作时间: ' . strftime('%Y-%m-%d %H:%M:%S') . '操作页面:' . MOD . '_' . ACT . '提交方式: ' . $_SERVER['REQUEST_METHOD'];
                create_file(DDROOT . '/data/temp/attack/' . date('Y-m-d') . '.txt', $tbver_137 . '
' . $tbver_116 . '
', 1, 1);
                dd_exit('what are you doing man!');
            }
            $tbver_116            = dd_htmlspecialchars($tbver_116);
            $tbver_16[$tbver_114] = $tbver_116;
        }
    }
}
function dd_htmlspecialchars($str)
{
    $tbver_119 = array(
        '"' => '&quot;',
        '\'' => '&#039',
        '<' => '&lt;',
        '>' => '&gt;',
        'u003c' => '&lt;',
        'u003e' => '&gt;'
    );
    foreach ($tbver_119 as $tbver_114 => $tbver_116) {
        if (strpos($str, $tbver_114) !== false) {
            $str = str_replace($tbver_114, $tbver_116, $str);
        }
    }
    return $str;
}

function php2js_array($tbver_16, $tbver_41)
{
    if (empty($tbver_16)) {
        return false;
    }
    $tbver_45 = '';
    echo $tbver_41 . '=new Array();' . $tbver_45;
    foreach ($tbver_16 as $tbver_114 => $tbver_116) {
        if (strpos($tbver_116, '\'') !== false) {
            $tbver_116 = str_replace('\"', '', $tbver_116);
        }
        echo $tbver_41 . '[\'' . $tbver_114 . '\']=\'' . $tbver_116 . '\';' . $tbver_45;
    }
}

function php2js_object($tbver_16, $tbver_41)
{
    if (empty($tbver_16)) {
        return false;
    }
    $tbver_45 = '
';
    echo $tbver_41 . '=new Object();' . $tbver_45;
    foreach ($tbver_16 as $tbver_114 => $tbver_116) {
        if (strpos($tbver_116, '\'') !== false) {
            $tbver_116 = str_replace('\"', '', $tbver_116);
        }
        echo $tbver_41 . '[\'' . $tbver_114 . '\']=\'' . $tbver_116 . '\';' . $tbver_45;
    }
}
function str2arr($str, $tbver_74)
{
    $tbver_164 = array();
    $a         = '';
    if (is_string($str)) {
        $tbver_119 = explode(',', $str);
    } else {
        $tbver_119 = $str;
    }
    $tbver_75 = count($tbver_119);
    if ($tbver_75 > $tbver_74) {
        $tbver_138 = 0;
        foreach ($tbver_119 as $tbver_116) {
            $a .= $tbver_116 . ',';
            $tbver_138++;
            if ($tbver_138 == $tbver_74) {
                $a           = preg_replace('/,$/', '', $a);
                $tbver_164[] = $a;
                $a           = '';
                $tbver_138   = 0;
            }
        }
        $a = preg_replace('/,$/', '', $a);
        if ($a != '') {
            $tbver_164[] = $a;
        }
    } else {
        $tbver_164[] = $str;
    }
    return $tbver_164;
}
function randsort($tbver_76, $tbver_73 = 4)
{
    $tbver_72 = count($tbver_76) - 1;
    $tbver_68 = ceil($tbver_72 / $tbver_73);
    for ($tbver_69 = $tbver_68, $tbver_70 = $tbver_72; $tbver_69 < $tbver_72 - $tbver_68; $tbver_69++, $tbver_70--) {
        $tbver_71            = $tbver_76[$tbver_69];
        $tbver_76[$tbver_69] = $tbver_76[$tbver_70];
        $tbver_76[$tbver_70] = $tbver_71;
    }
    for ($tbver_69 = 0, $tbver_70 = $tbver_72; $tbver_69 < $tbver_72 - 2; $tbver_69 += 2, $tbver_70--) {
        $tbver_71            = $tbver_76[$tbver_69];
        $tbver_76[$tbver_69] = $tbver_76[$tbver_70];
        $tbver_76[$tbver_70] = $tbver_71;
    }
    return $tbver_76;
}
function split_word($tbver_109)
{
    if (!class_exists('SplitWord')) {
        include(DDROOT . '/comm/splitword.class.php');
    }
    $tbver_77 = fs('SplitWord');
    $tbver_77 = new SplitWord();
    $tbver_77->SetSource($tbver_109);
    $tbver_77->StartAnalysis();
    $a = $tbver_77->GetFinallyIndex();
    return $a;
}

function rand_title($tbver_109, $tbver_74 = 1)
{
    $a = split_word($tbver_109);
    for ($tbver_138 = 0; $tbver_138 < $tbver_74; $tbver_138++) {
        $a = randsort($a);
    }
    return implode('', $a);
}
function check_plugin_auth($tbver_78, $tbver_85)
{
    if ($tbver_78 == '' || $tbver_85 == '') {
        return '参数不能为空';
    }
    global $duoduo;
    $tbver_86 = $duoduo->select('plugin', 'authcode', 'code="' . $tbver_78 . '"');
    if ($tbver_86 == '') {
        return 1;
    }
    $tbver_86 = json_decode(authcode($tbver_86, 'DECODE', $tbver_85), 1);
    if ($tbver_86 == '') {
        return 2;
    }
    $tbver_87 = (int) $tbver_86['endtime'];
    $tbver_84 = (int) $tbver_86['checktime'];
    $tbver_19 = $tbver_86['domain'];
    if ($tbver_19 != get_domain()) {
        return 4;
    }
    if (date('Ymd') - date('Ymd', $tbver_84) > 6) {
        $tbver_143 = DD_YUN_URL . '/index.php?m=Api&a=one&code=' . $tbver_78 . '&url=' . $tbver_19;
        $tbver_164 = dd_get_json($tbver_143);
        if ($tbver_164['s'] == '0') {
            return 3;
        } elseif ($tbver_164['s'] == '-1') {
            return 5;
        } elseif ($tbver_164['s'] == '-2') {
            return 100;
        } else {
            if ($tbver_164['r']['authcode'] != '') {
                $tbver_83 = array(
                    'authcode' => $tbver_164['r']['authcode']
                );
                $duoduo->update('plugin', $tbver_83, 'code="' . $tbver_78 . '"');
            }
        }
    }
    return 100;
}
function add_plugin_test($tbver_78)
{
    global $duoduo;
    $tbver_86 = array(
        'endtime' => time() + 3600 * 24 * 365,
        'checktime' => time(),
        'domain' => DOMAIN
    );
    $tbver_86 = authcode(json_encode($tbver_86), 'ENCODE', $tbver_78);
    $tbver_83 = array(
        'code' => $tbver_78,
        'title' => $tbver_78,
        'status' => 0,
        'addtime' => TIME,
        'key' => $tbver_78,
        authcode => $tbver_86,
        'mod' => '',
        'act' => '',
        'tag' => '',
        'admin_set' => 0,
        'endtime' => date('Y-m-d H:i:s', TIME + 3600 * 24 * 365),
        'toper_name' => 'duoduo',
        'toper_qq' => '111111',
        'banben' => BANBEN,
        'install' => 0,
        'jiaocheng' => 'http://bbs.duoduo123.com',
        'need_include' => 1,
        'version' => 1.0
    );
    $tbver_24 = (int) $duoduo->select('plugin', 'id', 'code="' . $tbver_78 . '"');
    if ($tbver_24 > 0) {
        $duoduo->update('plugin', $tbver_83, 'id="' . $tbver_24 . '"');
        file_put_contents(DDROOT . '/data/plugin_update.txt', 'add_plugin_test|' . $duoduo->lastsql . '
', FILE_APPEND);
    } else {
        $duoduo->insert('plugin', $tbver_83);
    }
    $tbver_79 = mysql_error();
    if ($tbver_79 == '') {
        return 1;
    } else {
        return $tbver_79;
    }
}
function get_nick_url($tbver_143)
{
    if (!function_exists('curl_exec')) {
        $tbver_132 = file_get_contents($tbver_143);
    } else {
        $tbver_80 = curl_init();
        curl_setopt($tbver_80, CURLOPT_URL, $tbver_143);
        curl_setopt($tbver_80, CURLOPT_RETURNTRANSFER, 1);
        $tbver_81  = curl_exec($tbver_80);
        $tbver_132 = curl_redir_exec($tbver_80);
    }
    return $tbver_132;
}
function curl_redir_exec($tbver_80, $tbver_82 = "")
{
    static $tbver_67 = 0;
    static $tbver_66 = 20;
    if ($tbver_67++ >= $tbver_66) {
        $tbver_67 = 0;
        return FALSE;
    }
    curl_setopt($tbver_80, CURLOPT_HEADER, true);
    curl_setopt($tbver_80, CURLOPT_RETURNTRANSFER, true);
    $tbver_83 = curl_exec($tbver_80);
    $tbver_52 = $tbver_83;
    list($tbver_102, $tbver_83) = explode('

', $tbver_83, 2);
    $tbver_53 = curl_getinfo($tbver_80, CURLINFO_HTTP_CODE);
    if ($tbver_53 == 301 || $tbver_53 == 302) {
        $tbver_12 = array();
        preg_match('/Location:(.*?)\n/', $tbver_102, $tbver_12);
        $tbver_143 = @parse_url(trim(array_pop($tbver_12)));
        if (!$tbver_143) {
            $tbver_67 = 0;
            return $tbver_83;
        }
        $tbver_54 = parse_url(curl_getinfo($tbver_80, CURLINFO_EFFECTIVE_URL));
        $tbver_51 = $tbver_143['scheme'] . '://' . $tbver_143['host'] . $tbver_143['path'] . ($tbver_143['query'] ? '?' . $tbver_143['query'] : '');
        curl_setopt($tbver_80, CURLOPT_URL, $tbver_51);
        return curl_redir_exec($tbver_80);
    } else {
        $tbver_67 = 0;
        return $tbver_52;
    }
}
function get_4_tradeid($tbver_50)
{
    $tbver_46 = dd_get('http://yun2.duoduo123.com/mod/get_trade_uid.php?tbnick=' . $tbver_50 . '&ddurl=' . urlencode(URL));
    $tbver_47 = dd_json_decode($tbver_46, 1);
    return $tbver_47;
}
function check_sold_out($tbver_48)
{
    $tbver_143 = DD_YUN_URL . '/api/index.php?m=taoapi&method=check_sold_out&iid=' . $tbver_48 . '&ddurl=' . urlencode(URL) . '&ddyunkey=' . urlencode(DDYUNKEY);
    $tbver_49  = DDROOT . '/data/temp/session/' . date('Ymd') . '/' . dd_crc32($tbver_48) . '.txt';
    if (file_exists($tbver_49)) {
        $a = file_get_contents($tbver_49);
    } else {
        $a = dd_get($tbver_143);
        create_file($tbver_49, $a);
    }
    $a = dd_json_decode($a, 1);
    return $a;
}
function bijia($tbver_127, $tbver_55 = 1, $tbver_56 = 0, $tbver_63 = 0, $tbver_64 = '')
{
    if (DDMALL == 1)
        return false;
    $tbver_143 = 'http://sf.manmanbuy.com/s.aspx?id=71&key=' . urlencode($tbver_127) . '&PageID=' . $tbver_55;
    if ($tbver_63 < $tbver_56)
        return false;
    if ($tbver_56 > 0) {
        $tbver_143 .= '&price1=' . $tbver_56;
    }
    if ($tbver_63 > 0) {
        $tbver_143 .= '&price2=' . $tbver_63;
    }
    if ($tbver_64 != '') {
        $tbver_143 .= '&orderby=' . $tbver_64;
    }
    $tbver_65 = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
    $tbver_62 = md5($tbver_143);
    $tbver_61 = DDROOT . '/data/temp/bijia/' . substr($tbver_62, 0, 2) . '/' . substr($tbver_62, 2, 2) . '/' . $tbver_62 . '.json';
    if (file_exists($tbver_61)) {
        $tbver_57 = file_get_contents($tbver_61);
        $tbver_57 = dd_json_decode($tbver_57, 1);
    } else {
        if (function_exists('curl_exec')) {
            $tbver_80 = curl_init();
            curl_setopt($tbver_80, CURLOPT_URL, $tbver_143);
            curl_setopt($tbver_80, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($tbver_80, CURLOPT_USERAGENT, $tbver_65);
            $a = curl_exec($tbver_80);
        } else {
            $tbver_58 = array(
                'http' => array(
                    'method' => 'GET',
                    'header' => 'User-Agent: ' . $tbver_65
                )
            );
            $tbver_59 = stream_context_create($tbver_58);
            $a        = file_get_contents($tbver_143, false, $tbver_59);
        }
        $a = compact_html($a);
        preg_match('/共有(\d+)条记录/', $a, $tbver_35);
        $tbver_60 = $tbver_35[1];
        preg_match('/<divid="listpro"(.*)<divid="dispage"style="margin:20px8px;width:600px;/', $a, $tbver_132);
        preg_match_all('/<divclass="bjline">(.*?)<divclass=\'clear\'><\/div>/', $tbver_132[1], $a);
        $tbver_57 = array();
        foreach ($a[1] as $tbver_114 => $tbver_116) {
            preg_match('#<ahref=".*&url=(.*)"target="_blank">.*\'"src="(.*)"class=\'sppicclass\'>.*class="f14sc">(.*)</a><spanstyle.*align=\'absmiddle\'>(.*?)</div></div><divclass=.*<fontclass=\'priceword\'>(.*)</font></div><divclass#', $tbver_116, $a);
            if ($a[1] != '') {
                $tbver_57[$tbver_114]['url']   = urldecode($a[1]);
                $tbver_57[$tbver_114]['img']   = $a[2];
                $tbver_57[$tbver_114]['title'] = str_replace('<fontcolor="red">', '<font color="red">', $a[3]);
                $tbver_57[$tbver_114]['mall']  = preg_replace('/\(第三方\)|\(自营\)/', '', $a[4]);
                $tbver_57[$tbver_114]['price'] = $a[5];
            }
        }
        if (!empty($tbver_57)) {
            $tbver_57['total'] = $tbver_60;
            create_file($tbver_61, dd_json_encode($tbver_57));
        } else {
            $tbver_57['total'] = 0;
        }
    }
    return $tbver_57;
}
function get_tao_mobile_price($tbver_48)
{
    $tbver_143 = 'http://hws.m.taobao.com/cache/wdetail/5.0/?id=' . $tbver_48 . '&ttid=2013@taobao_h5_1.0.0&exParams={}';
    $a         = dd_get($tbver_143, 'get', 86400);
    $a         = json_decode($a, 1);
    $a         = json_decode($a['data']['apiStack'][0]['value'], 1);
    return (float) $a['data']['itemInfoModel']['priceUnits'][0]['price'];
}

if (defined('ADMIN') && ADMIN == 1) 
{
/*
    if ($_GET['mod'] == 'login' && $_GET['act'] == 'login' && get_domain() != 'localhost' && empty($_POST)) {
        $a = dd_get_cache('authorize');
        if ($a == '' || (float) $a['time'] < strtotime(date('Ymd'))) {
            $url               = 'http://auth.duoduo123.com/new_install.php?url=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) . '&key=' . urlencode(DDKEY) . '&miyue=1';
            $authorize         = file_get_contents($url);
            $authorize_url_get = 1;
        } else {
            $authorize         = $a['code'];
            $authorize_url_get = 0;
        }
        if ($authorize == '') {
           // exit('授权获取失败，请刷新浏览器从新获取！');
        }
        $auth_arr = unserialize(authcode($authorize, 'DECODE', md5('521meimei' . get_domain() . '521meimei')));
        if ((float) $auth_arr['etime'] < strtotime('2014-07-01')) {
            $url               = 'http://auth.duoduo123.com/new_install.php?url=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) . '&key=' . urlencode(DDKEY) . '&miyue=1';
            $authorize         = file_get_contents($url);
            $authorize_url_get = 1;
            if ($authorize == '') {
             //   exit('授权获取失败，请刷新浏览器从新获取！');
            }
            $auth_arr = unserialize(authcode($authorize, 'DECODE', md5('521meimei' . get_domain() . '521meimei')));
            if ((float) $auth_arr['etime'] < time()) {
             //   exit('您的域名授权已到期，请续费多多返利系统。<a href="http://auth.duoduo123.com">多多官方授权</a>');
            } else {
                if ($authorize_url_get == 1) {
                    $a = array(
                        'time' => time(),
                        'code' => $authorize
                    );
                    dd_set_cache('authorize', $a);
                }
            }
        } else {
            if ($authorize_url_get == 1) {
                $a = array(
                    'time' => time(),
                    'code' => $authorize
                );
                dd_set_cache('authorize', $a);
            }
        }
    }
	*/
}
?>
<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_view_helper{

    function block_area($params, $content, $template)
    {
        if(null!==$content){
            return '<!-----'.$params['inject'].'-----'.$content.'-----'.$params['inject'].'----->';
        }
    }

    function block_t($params, $content, $template)
    {
        return $content;
        if(!is_null($content)&&trim($content)!=''){
            if(empty($arguments['app'])) throw new \InvalidArgumentException('missing app argument in setting tag in '.__FILE__.' on line '.__LINE__);
            return app::get($params['app'])->_($content);
        }
    }//End Function

    function _tpl_escape_chars($string)
    {
        if(!is_array($string))
        {
            $string = preg_replace('!&(#?\w+);!', '%%%TEMPLATE_START%%%\\1%%%TEMPLATE_END%%%', $string);
            $string = htmlspecialchars($string);
            $string = str_replace(array('%%%TEMPLATE_START%%%','%%%TEMPLATE_END%%%'), array('&',';'), $string);
        }
        return $string;
    }

    function function_html_options($params, $template)
    {

        $name = null;
        $options = null;
        $selected = array();
        $extra = '';

        foreach($params as $_key => $_val)
        {
            switch($_key)
            {
            case 'name':
                $$_key = (string)$_val;
                break;
            case 'options':
                $$_key = (array)$_val;
                break;
            case 'values':
                case 'output':
                    $$_key = array_values((array)$_val);
                    break;
                case 'selected':
                    $$_key = array_values((array)$_val);
                    break;
                default:
                    if(!is_array($_key))
                    {
                        $extra .= ' ' . $_key . '="' . $this->_tpl_escape_chars($_val) . '"';
                    }
                    else
                    {
                        throw new \InvalidArgumentException("html_select: attribute '$_key' cannot be an array");
                    }
                    break;
            }
        }

        $_html_result = '';
        if (is_array($options))
        {
            foreach ($options as $_key=>$_val)
            {
                $_html_result .= $this->_tpl_function_html_options_optoutput($_key, $_val, $selected);
            }
        }
        else
        {
            foreach ((array)$values as $_i=>$_key)
            {
                $_val = isset($output[$_i]) ? $output[$_i] : '';
                $_html_result .= $this->_tpl_function_html_options_optoutput($_key, $_val, $selected);
            }
        }

        if(!empty($name))
        {
            $_html_result = '<select name="' . $this->_tpl_escape_chars($name) . '"' . $extra . '>' . "\n" . $_html_result . '</select>' . "\n";
        }

        return $_html_result;
    }

    function _tpl_function_html_options_optoutput($key, $value, $selected){
        if(!is_array($value))
        {
            $_html_result = '<option label="' . $this->_tpl_escape_chars($value) . '" value="' . $this->_tpl_escape_chars($key) . '"';
            if (in_array($key, $selected))
            {
                $_html_result .= ' selected="selected"';
            }
            $_html_result .= '>' . $this->_tpl_escape_chars($value) . '</option>' . "\n";
        }
        else
        {
            $_html_result = $this->_function_html_options_optgroup($key, $value, $selected);
        }
        return $_html_result;
    }

    function _function_html_options_optgroup($key, $values, $selected){
        $optgroup_html = '<optgroup label="' . $this->_tpl_escape_chars($key) . '">' . "\n";
        foreach ($values as $key => $value)
        {
            $optgroup_html .= $this->_tpl_function_html_options_optoutput($key, $value, $selected);
        }
        $optgroup_html .= "</optgroup>\n";
        return $optgroup_html;
    }

    function function_html_table($params, $template){
        $table_attr = 'border="1"';
        $tr_attr = '';
        $td_attr = '';
        $cols = 3;
        $trailpad = '&nbsp;';

        extract($params);

        if (!isset($loop))
        {
            throw new \InvalidArgumentException('html_table tag missing loop argument.');
        }

        $output = "<table $table_attr>\n";
        $output .= "<tr " . $this->_function_html_table_cycle('tr', $tr_attr) . ">\n";

        for($x = 0, $y = count($loop); $x < $y; $x++)
        {
            $output .= "<td " . $this->_function_html_table_cycle('td', $td_attr) . ">" . $loop[$x] . "</td>\n";
            if((!(($x+1) % $cols)) && $x < $y-1)
            {
                // go to next row
                $output .= "</tr>\n<tr " . $this->_function_html_table_cycle('tr', $tr_attr) . ">\n";
            }
            if($x == $y-1)
            {
                // last row, pad remaining cells
                $cells = $cols - $y % $cols;
                if($cells != $cols) {
                    for($padloop = 0; $padloop < $cells; $padloop++) {
                        $output .= "<td " . $this->_function_html_table_cycle('td', $td_attr) . ">$trailpad</td>\n";
                    }
                }
                $output .= "</tr>\n";
            }
        }
        $output .= "</table>\n";
        return $output;
    }

    function _function_html_table_cycle($name, $var){
        static $names = array();

        if(!is_array($var))
        {
            return $var;
        }

        if(!isset($names[$name]) || $names[$name] == count($var)-1)
        {
            $names[$name] = 0;
            return $var[0];
        }

        $names[$name]++;
        return $var[$names[$name]];
    }

    function function_json($params, $template){
        return json_encode($params['from']);
    }

    function function_mailto($params, $template){
        extract($params);

        if (empty($address))
        {
            throw new \InvalidArgumentException('mail_to tag missing address argument.');
        }

        if (empty($text))
        {
            $text = $address;
        }

        if (empty($extra))
        {
            $extra = "";
        }

        // netscape and mozilla do not decode %40 (@) in BCC field (bug?)
        // so, don't encode it.

        $mail_parms = array();
        if (!empty($cc))
        {
            $mail_parms[] = 'cc='.str_replace('%40','@',rawurlencode($cc));
        }

        if (!empty($bcc))
        {
            $mail_parms[] = 'bcc='.str_replace('%40','@',rawurlencode($bcc));
        }

        if (!empty($subject))
        {
            $mail_parms[] = 'subject='.rawurlencode($subject);
        }

        if (!empty($newsgroups))
        {
            $mail_parms[] = 'newsgroups='.rawurlencode($newsgroups);
        }

        if (!empty($followupto))
        {
            $mail_parms[] = 'followupto='.str_replace('%40','@',rawurlencode($followupto));
        }

        $mail_parm_vals = "";
        for ($i=0; $i<count($mail_parms); $i++)
        {
            $mail_parm_vals .= (0==$i) ? '?' : '&';
            $mail_parm_vals .= $mail_parms[$i];
        }
        $address .= $mail_parm_vals;

        if (empty($encode))
        {
            $encode = 'none';
        }
        elseif (!in_array($encode,array('javascript','hex','none')) )
        {
            throw new \InvalidArgumentException('mailto tag `encode` argument must be none, javascript or hex.');
        }

        if ($encode == 'javascript' )
        {
            $string = 'document.write(\'<a href="mailto:'.$address.'" '.$extra.'>'.$text.'</a>\');';
            for ($x=0; $x < strlen($string); $x++)
            {
                $js_encode .= '%' . bin2hex($string[$x]);
            }
            return '<script>eval(unescape(\''.$js_encode.'\'))</script>';
        }
        elseif ($encode == 'hex')
        {
            preg_match('!^(.*)(\?.*)$!',$address,$match);
            if(!empty($match[2]))
            {
                throw new \InvalidArgumentException('mailto tag `encode` argument does not work with extra attributes. Try javascript..');
            }
            $address_encode = "";
            for ($x=0; $x < strlen($address); $x++)
            {
                if(preg_match('!\w!',$address[$x]))
                {
                    $address_encode .= '%' . bin2hex($address[$x]);
                }
                else
                {
                    $address_encode .= $address[$x];
                }
            }
            $text_encode = "";
            for ($x=0; $x < strlen($text); $x++)
            {
                $text_encode .= '&#x' . bin2hex($text[$x]).';';
            }
            return '<a href="mailto:'.$address_encode.'" '.$extra.'>'.$text_encode.'</a>';
        }
        else
        {
            // no encoding
            return '<a href="mailto:'.$address.'" '.$extra.'>'.$text.'</a>';
        }
    }

    function function_pagers($params, $template){
        if(!$params['data']['current'])$params['data']['current'] = 1;
        if(!$params['data']['total'])$params['data']['total'] = 1;
        if($params['data']['total']<2){
            return '';
        }

        $prev = $params['data']['current']>1?
            '<a href="'.str_replace($params['data']['token'],$params['data']['current']-1,$params['data']['link']).'" class="prev" onmouseover="this.className = \'onprev\'" onmouseout="this.className = \'prev\'" title="'.app::get('base')->_('上一页').'">'.app::get('base')->_('上一页').'</a>':
            '<span class="unprev" title="'.app::get('base')->_('已经是第一页').'">'.app::get('base')->_('已经是第一页').'</span>';

        $next = $params['data']['current']<$params['data']['total']?
            '<a href="'.str_replace($params['data']['token'],$params['data']['current']+1,$params['data']['link']).'" class="next" onmouseover="this.className = \'onnext\'" onmouseout="this.className = \'next\'" title="'.app::get('base')->_('下一页').'">'.app::get('base')->_('下一页').'</a>':
            '<span class="unnext" title="'.app::get('base')->_('已经是最后一页').'">'.app::get('base')->_('已经是最后一页').'</span>';

        if($params['rand'] && $params['data']['total']>1){
            $r=    rand(1,$params['data']['total']);
            $rand = '<td><input type="button" onclick="window.location=\''.str_replace($params['data']['token'],$r,$params['data']['link']).'\'" value="'.app::get('base')->_('随便一页').'" class="rand"></td>';
        }

        if($params['type']=='mini'){
            return <<<EOF
    <table class="pager">
        <tr>
            <td>
            <span class="pagecurrent">{$params['data']['current']}</span>
            /
            <span class="pageall">{$params['data']['total']}</span>
            </td>
            <td>{$prev}</td>
            <td>{$next}</td>
            {$rand}
        </tr>
    </table>
EOF;
        }else{

            $c = $params['data']['current']; $t=$params['data']['total']; $v = array();  $l=$params['data']['link']; $p=$params['data']['token'];

            if($t<11){
                $v[] = $this->_pager_link(1,$t,$l,$p,$c);
                //123456789
            }else{
                if($t-$c<8){
                    $v[] = $this->_pager_link(1,3,$l,$p);
                    $v[] = $this->_pager_link($t-8,$t,$l,$p,$c);
                    //12..50 51 52 53 54 55 56 57
                }elseif($c<10){
                    $v[] = $this->_pager_link(1,max($c+3,10),$l,$p,$c);
                    $v[] = $this->_pager_link($t-1,$t,$l,$p);
                    //1234567..55
                }else{
                    $v[] = $this->_pager_link(1,3,$l,$p);
                    $v[] = $this->_pager_link($c-2,$c+3,$l,$p,$c);
                    $v[] = $this->_pager_link($t-1,$t,$l,$p);
                    //123 456 789
                }
            }
            $links = implode('...',$v);
            $____p=app::get('base')->_('到第');
            $____p1=app::get('base')->_('页');

            //    str_replace($params['data']['token'],4,$params['data']['link']);
            //    if($params['data']['total']
            return <<<EOF
      <div class="clearfix">
    <table class="pager floatRight"><tr>
    <td>{$prev}</td>
    <td class="pagernum">{$links}</td>
    <td style="padding-right:20px">{$next}</td>
    <!-- <td>$____p <input type="text" class="pagenum"> $____p1</td>
    <td><input type="button" value="" class="go"></td> -->
    {$rand}
    </tr></table></div>
EOF;
        }
    }

    function _pager_link($from,$to,$l,$p,$c=null){
        for($i=$from;$i<$to+1;$i++){
            if($c==$i){
                $r[]=' <strong class="pagecurrent">'.$i.'</strong> ';
            }else{
                $r[]=' <a href="'.str_replace($p,$i,$l).'">'.$i.'</a> ';
            }
        }
        return implode(' ',$r);
    }

    function function_toinput($params, $template){
        $html = null;
        $this->_tpl_function_toinput($params['from'],$ret,$params['name']);
        foreach($ret as $k=>$v){
            $html.='<input type="hidden" name="'.$k.'" value="'.$v."\" />\n";
        }
        return $html;
    }

    function _tpl_function_toinput($data,&$ret,$path=null){
        foreach($data as $k=>$v){
            $d = $path?$path.'['.$k.']':$k;
            if(is_array($v)){
                $this->_tpl_function_toinput($v,$ret,$d);
            }else{
                $ret[$d]=$v;
            }
        }
    }

    function modifier_storager($image_id,$size=''){
        return base_storager::modifier($image_id,$size);
    }

    function modifier_cdate($string,$type='FDATE_FTIME'){
        $time = $string?intval($string):time();
        switch($type){
        case 'FDATE':
            $dateFormat = 'Y-m-d';
            break;
        case 'SDATE':
            $dateFormat = 'y-m-d';
            break;
        case 'DATE':
            $dateFormat = 'm-d';
            break;
        case 'FDATE_FTIME':
            $dateFormat = 'Y-m-d H:i:s';
            break;
        case 'FDATE_STIME':
            $dateFormat = 'Y-m-d H:i';
            break;
        case 'SDATE_FTIME':
            $dateFormat = 'y-m-d H:i:s';
            break;
        case 'SDATE_STIME':
            $dateFormat = 'y-m-d H:i';
            break;
        case 'DATE_FTIME':
            $dateFormat = 'm-d H:i:s';
            break;
        case 'DATE_STIME':
            $dateFormat = 'm-d H:i';
            break;
        default:
            $dateFormat = 'Y-m-d';
        }

        return date($dateFormat,$time);
    }

    function modifier_cut($string, $length = 80, $etc = '...', $break_words = false, $middle = false){
        if ($length == 0)
            return '';

        if (isset($string{$length+1})) {

            $length -= min($length, strlen($etc));

            if (!$break_words && !$middle) {
                $string =  $this->utftrim(substr($string, 0, $length+1));
                //$string = preg_replace('/\s+?(\S+)?$/', '', $this->utftrim(substr($string, 0, $length+1)));
            }
            if(!$middle) {
                return $this->utftrim(substr($string, 0, $length)) . $etc;
            } else {
                return $this->utftrim(substr($string, 0, $length/2)) . $etc . $this->utftrim(substr($string, -$length/2));
            }
        } else {
            return $string;
        }
    }

    function modifier_hidden_show($string, $etc='*')
    {
        $first = $string[0];
        $last = substr($string,-1,1);
        for( $i=0; $i<4; $i++ )
        {
            $hiddenStr .= $etc;
        }
        return $first.$hiddenStr.$last;
    }

    function utftrim($str){
        $found = false;
        for($i=0;$i<4&&$i<strlen($str);$i++)
        {
            $ord = ord(substr($str,strlen($str)-$i-1,1));
            if($ord> 192)
            {
                $found = true;
                break;
            }
        }
        if($found)
        {
            if($ord>240)
            {
                if($i==3) return $str;
                else return substr($str,0,strlen($str)-$i-1);
            }
            elseif($ord>224)
            {
                if($i==2) return $str;
                else return substr($str,0,strlen($str)-$i-1);
            }
            else
            {
                if($i==1) return $str;
                else return substr($str,0,strlen($str)-$i-1);
            }
        }
        else return $str;
    }

    function modifier_date($string, $format="r", $default_date=null){
        if($string != '')
        {
            return date($format, $this->make_timestamp($string));
        }
        elseif (isset($default_date) && $default_date != '')
        {
            return date($format, $this->make_timestamp($default_date));
        }
        else
        {
            return;
        }
    }

    function make_timestamp($string)
    {
        if(empty($string))
        {
            $string = "now";
        }
        if (is_numeric($time) && $time != -1 && strlen($string) == 10)
        {
            return $time;
        }

        $time = strtotime($string);
        // is mysql timestamp format of YYYYMMDDHHMMSS?
        if (is_numeric($string) && strlen($string) == 14)
        {
            $time = mktime(substr($string,8,2),substr($string,10,2),substr($string,12,2),substr($string,4,2),substr($string,6,2),substr($string,0,4));
            return $time;
        }

        // couldn't recognize it, try to return a time
        $time = (int) $string;
        if ($time > 0)
        {
            return $time;
        }
        else
        {
            return time();
        }
    }

    function modifier_date_format($string, $format="%b %e, %Y", $default_date=null){
        if($string != '')
        {
            return strftime($format, $this->make_timestamp($string));
        }
        elseif (isset($default_date) && $default_date != '')
        {
            return strftime($format, $this->make_timestamp($default_date));
        }
        else
        {
            return;
        }
    }

    function modifier_escape($string, $esc_type = 'html', $char_set = 'ISO-8859-1'){
        switch ($esc_type)
        {
        case 'html':
            return htmlspecialchars($string, ENT_QUOTES, $char_set);

        case 'htmlall':
            return htmlentities($string, ENT_QUOTES, $char_set);

        case 'url':
            return rawurlencode($string);

        case 'urlpathinfo':
            return str_replace('%2F','/',rawurlencode($string));

        case 'quotes':
            // escape unescaped single quotes
            return preg_replace("%(?<!\\\\)'%", "\\'", $string);

        case 'hex':
            // escape every character into hex
            $return = '';
            for ($x=0; $x < strlen($string); $x++) {
                $return .= '%' . bin2hex($string[$x]);
            }
            return $return;

        case 'hexentity':
            $return = '';
            for ($x=0; $x < strlen($string); $x++) {
                $return .= '&#x' . bin2hex($string[$x]) . ';';
            }
            return $return;

        case 'decentity':
            $return = '';
            for ($x=0; $x < strlen($string); $x++) {
                $return .= '&#' . ord($string[$x]) . ';';
            }
            return $return;

        case 'javascript':
            // escape quotes and backslashes, newlines, etc.
            return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));

        case 'mail':
            // safe way to display e-mail address on a web page
            return str_replace(array('@', '.'),array(' [AT] ', ' [DOT] '), $string);

        case 'nonstd':
            // escape non-standard chars, such as ms document quotes
            $_res = '';
            for($_i = 0, $_len = strlen($string); $_i < $_len; $_i++)
            {
                $_ord = ord(substr($string, $_i, 1));
                // non-standard char, escape it
                if($_ord >= 126)
                {
                    $_res .= '&#' . $_ord . ';';
                }
                else
                {
                    $_res .= substr($string, $_i, 1);
                }
            }
            return $_res;

        default:
            return $string;
        }
    }

    function modifier_gender($result){
        switch($result){
        case 'male':
            return app::get('base')->_('男');
            break;
        case 'female':
            return app::get('base')->_('女');
            break;

        }
    }

    function modifier_t($content, $app_id, $arg1=null)
    {
        return $content;
        $args = func_get_args();
        array_shift($args);
        array_shift($args);   //todo: remove 2 params;
        if(count($args)){
            array_unshift($args, $content);
            return call_user_func_array(array(app::get($app_id), "_"), $args);
        }else{
            return app::get($app_id)->_($content);
        }
    }//End Function

    function modifier_number($num,$type=0){
        switch($type){
        case 0:
            $number = intval($num);
            break;
        case 1:
            if($num <1){
                $number=app::get('base')->_('低于1');
            }else{
                $number= number_format($num,1,'','');
                if($number%10==0){
                    $number=$number/10;
                }
            }
            break;
        case 2:
            if($num<1){
                $number = app::get('base')->_('超过99');
            }else{
                $number = 100-intval($num);

            }
            break;
        case 3:
            if($num <1){
                $number=app::get('base')->_('低于1');
            }else{
                $number= ceil($num*10)/10;
            }
            break;
        }
        return $number;
    }

    function modifier_paddingleft($vol,$empty,$fill){
        return str_repeat($fill,$empty).$vol;

    }

    function modifier_regex_replace($string, $search, $replace){
        if (preg_match('!([a-zA-Z\s]+)$!s', $search, $match) && (strpos($match[1], 'e') !== false))
        {
            /* remove eval-modifier from $search */
            $search = substr($search, 0, -strlen($match[1])) . preg_replace('![e\s]+!', '', $match[1]);
        }
        return preg_replace($search, $replace, $string);
    }

    function modifier_region($r){
        list($pkg,$regions,$region_id) = explode(':',$r);
        if(is_numeric($region_id)){
            return str_replace('/','-',$regions);
        }else{
            return $r;
        }
    }

    function modifier_replace($string, $search, $replace){
        return str_replace($search, $replace, $string);
    }

    function modifier_strip($string, $replace = ' '){
        return preg_replace('!\s+!', $replace, $string);
    }

    function modifier_styleset($style)
    {
        switch($style){
        case 1:
            return 'font-weight: bold;';
            break;
        case 2:
            return 'font-style: italic;';
            break;
        case 3:
            return 'text-decoration: line-through;';
            break;
        }

    }

    function block_strip($params, $content, $template)
    {
        if(null!==$content){
            $replace = (isset($params['replace'])) ? $params['replace'] : ' ';
            return preg_replace('!\s+!', $replace, $content);
        }
    }

    function modifier_print_r($string){
        return "<pre>";print_r($string);
    }

    function modifier_count($array){
        return count($array);
    }

    function modifier_htmlspecialchars($string){
        return htmlspecialchars($string);
    }

    function modifier_json2value($key, $arr_json)
    {
        $arr = json_decode($arr_json,1);
        if(is_array($arr))
        {
            return $arr[$key];
        }
        else
        {
            return $arr;
        }
    }

}

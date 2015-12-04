<?php

//todo:检查是否支持php5及pathinfo
$output = <<<EOF
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
 <html xmlns="http://www.w3.org/1999/xhtml">
 <head> 
 <meta charset="utf-8">
 <title>提示</title>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <style type="text/css">
 .installer-notice{
 background:#FFF9CF;
 border:1px solid #FFD324;
 color:#514721;
 padding:0.3em 0.8em 0.3em 0.8em;
}
</style>
</head>
<body>
<div class="installer-notice">%message%</div>
 </body>
 </html>
EOF;


if(version_compare(phpversion(), '5', '<')){
    echo str_replace('%message%', '不支持php5', $output);
    exit;
}

if(isset($_GET['check_path_info'])){
    $path_info = get_path_info();
    if($path_info){
        echo 'SUPPORT_PATHINFO';
    }else{
        echo 'NO_SUPPORT_PATHINFO';
    }
    exit;
}

function get_path_info() {
    $path_info = '';
    if (isset($_SERVER['PATH_INFO'])) {
        $path_info = $_SERVER['PATH_INFO'];
    }elseif(isset($_SERVER['ORIG_PATH_INFO'])){
        $path_info = $_SERVER['ORIG_PATH_INFO'];
        $script_name = get_script_name();
        if(substr($script_name, -1, 1) == '/'){
            $path_info = $path_info . '/';
        }
    }else{
        $script_name = get_script_name();
        $script_dir = preg_replace('/[^\/]+$/', '', $script_name);
        $request_uri = get_request_uri();
        $urlinfo = parse_url($request_uri);
        if ( strpos($urlinfo['path'], $script_name) === 0) {
            $path_info = substr($urlinfo['path'], strlen($script_name));
        } elseif ( strpos($urlinfo['path'], $script_dir) === 0 ) {
            $path_info = substr($urlinfo['path'], strlen($script_dir));
        }
    }
    if($path_info){
        $path_info = "/".ltrim($path_info,"/");
    }
    return $path_info;
}

function get_script_name() {
    return isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : '');
}

function get_request_uri() {
    if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
        return $_SERVER['HTTP_X_REWRITE_URL'];
    } elseif (isset($_SERVER['REQUEST_URI'])) {
        return $_SERVER['REQUEST_URI'];
    } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
        return $_SERVER['ORIG_PATH_INFO'] . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
    }
}

function get_host() {
    $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
    //$host = $_SERVER['HTTP_HOST'];
    if (!empty($host)) {
        return $host;
    }

    $scheme = get_schema();
    $name   = get_name();
    $port   = get_port();

    if (($scheme == "HTTP" && $port == 80) || ($scheme == "HTTPS" && $port == 443)) {
        return $name;
    } else {
        return $name . ':' . $port;
    }
}

function get_name()
{
    return isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME'];
}//End Function

function get_schema()
{
    return ($_SERVER['HTTPS'] == 'on') ? "HTTPS" : "HTTP";
}//End Function

function get_port()
{
    return $_SERVER['SERVER_PORT'];
}//End Function

function check_pathinfo(){
    $host = get_host();
    $port = get_port();
    $uri = strtolower(get_schema()) . '://' . get_host() . get_request_uri() . '/pathinfotest?check_path_info=1';
    $content = '';
    $host_addr_arr = array(
            $host,
            '127.0.0.1',
            'localhost',
        );
    foreach($host_addr_arr AS $host_addr){
        $fp = @fsockopen($host_addr, $port, $errno, $errstr, 2);
        if ($fp) {
            $out = "GET ".$uri." HTTP/1.1\r\n";
            $out .= "Host: {$host}\r\n";
            $out .= "Connection: close\r\n\r\n";
            fwrite($fp, $out);
            while (!feof($fp) && strlen($content)<512) {
                $content .= fgets($fp, 128);
            }
            fclose($fp);
        }
        $content = preg_split("\r?\n\r?\n",$content);
        if(strpos($content[1],'SUPPORT_PATHINFO')!==false){
            return true;
        }
    }
    if(function_exists('curl_init')){
        ob_start();
        $fp = curl_init($uri);
        curl_exec($fp);
        curl_close($fp);
        $str = ob_get_contents();
        ob_end_clean();
        return ($str == 'SUPPORT_PATHINFO') ? true : false;
    }else{
        return false;
    }
}

if(check_pathinfo()){
    $url = $_COOKIE['LOCAL_SETUP_URL'];
    setCookie('LOCAL_SETUP_URL', '', 0, '/');
    Header('Location: ' . $url);    //todo:进入安装流程
    exit;
}else{
    echo str_replace('%message%', '不支持pathinfo', $output);
    exit;
}

?>

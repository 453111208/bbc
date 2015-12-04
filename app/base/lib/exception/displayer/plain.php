<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class base_exception_displayer_plain implements base_exception_displayer_interface{

    public function display(Exception $exception) {
        $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
		$headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : array();
        $body = $exception->getMessage();
        return new Response(self::output('服务异常稍后再试', $body, $status), $status, $headers);
    }

    
    protected function output($title, $body='', $status_code=500){
        //header('Connection:close',1,500);
        
        $date = date(DATE_RFC822);
        
        $html =<<<HTML
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
        	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        	<title>Error: $title</title>
        	<style>
                #main{width:500;margin:auto;}
                #header{position: relative;background:#c52f24;margin:20px 0 5px 0;
                padding:5px;color:#fff;height:30px;
                font-family: "Helvetica Neue", Arial, Helvetica, Geneva, sans-serif;}
                .code{font-size:14px;line-height:16px;font-weight:bold;font-family: "Courier New", Courier, mono;}
                .lnk{text-decoration: underline;color:#009;	cursor: pointer;}
        	</style>
        </head>

        <body>
            <div id="main">
                <div id="header">
                    <span style="float:left;">$title</span>
                    <span style="float:right;font-size:10px">$date</span>
                </div>
                <br class="clear" />
                <div>
                $body
                </div>
            </div>
        </body>
        </html>
HTML;

 
        return str_pad($html,1024);
    }
    
}//End Class

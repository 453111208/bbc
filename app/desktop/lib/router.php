<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_router
{
    static public function dispatch()
    {
        $controller = request::get('ctl')?:'default';
        $action = request::get('act')?:'index';
        // 这里需要get的优先级高于post
        $app = request::get('app')?:'desktop';
        $query_args = request::get('p');

        // 丑陋的兼容
        $_GET['ctl'] = $controller;
        $_GET['act'] = $action;
        $_GET['app'] = $app;

        $controller = app::get($app)->controller($controller);

        return call_user_func_array(array($controller,$action),(array)$query_args);
    }
}

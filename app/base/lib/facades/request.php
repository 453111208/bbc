<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class base_facades_request extends base_facades_facade
{

	/**
	 * Return the Request instance
	 *
	 * @var \Symfony\Component\HttpFoundation\Request;
	 */

    private static $__request;

    protected static function getFacadeAccessor() {
        if (!static::$__request)
        {
            // todo: 因为没有容器的临时策略
            if (defined('WEB_MODE') && constant('WEB_MODE') === true)
            {
                static::$__request = base_http_request::createFromGlobals();
            }
            else
            {
                $i = 'index.php';
                $url = trim(config::get('app.url', 'http://localhost'), '/');
                $url = strpos($url, $i) === false ? $url.'/' : $url;
                $parsed_url = parse_url($url);

                $path = $parsed_url['path'];

                $_SERVER['SCRIPT_FILENAME'] = PUBLIC_DIR.'/'.$i;

                $_SERVER['SCRIPT_NAME'] = (str_contains($path, $i) ? str_replace('/'.$i, '', $path) : $path).'/'.$i;

                static::$__request = base_http_request::create($url, 'GET', [], [], [], $_SERVER);
            }
        }
        return static::$__request;
    }
}

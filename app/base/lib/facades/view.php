<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class base_facades_view extends base_facades_facade
{

	/**
	 * Return the View instance
	 * 
	 * @var \Symfony\Component\HttpFoundation\Request;
	 */
    
    private static $__view;

    protected static function getFacadeAccessor() {
        if (!static::$__view)
        {
            $finder = kernel::single('base_view_finder');
            $appPaths = defined('CUSTOM_CORE_DIR') ? [CUSTOM_CORE_DIR, APP_DIR] : [APP_DIR];
            $finder->addNamespace('app', $appPaths, 'base_view_object_app');
            $finder->addNamespace('theme', array(),'base_view_object_theme');
            $finder->addNamespace('messenger', array(),'base_view_object_messenger');
            //$finder->addNamespace('widget', array(),'base_view_object_file');
            $finder->setDefaultNamespace('app');

            $compiler = new base_view_compilers_tramsy();
            $engine = new base_view_engine($compiler);
            static::$__view = new base_view_factory($engine , $finder);
        }
        return static::$__view;
    }

	/**
	 * Return the Request instance, 临时性的用法. 需要抽象ioc
	 * 
	 * @var \Symfony\Component\HttpFoundation\Request;
	 */
    public static function getView()
    {
        static::getFacadeAccessor();
        return static::$__view;
    }
    
}

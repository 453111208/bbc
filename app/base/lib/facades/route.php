<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_facades_route extends base_facades_facade
{
	/**
	 * Return the View instance
	 * 
	 * @var \Symfony\Component\HttpFoundation\Request;
	 */
    
    private static $__router;
    
    protected static function getFacadeAccessor()
    {
        if (!static::$__router)
        {
            static::$__router =  kernel::single('base_routing_router', request::instance());
            route::boot();
        }
        return static::$__router;
    }

    protected static function boot()
    {
        if (kernel::routesAreCached())
        {
            self::loadCachedRoutes();
        }
        else
        {
            self::loadRoutes();
        }
    }

	/**
	 * Load the cached routes for the application.
	 *
	 * @return void
	 */
	protected static function loadCachedRoutes()
	{
        require kernel::getCachedRoutesPath();
	}

	/**
	 * Load the application routes.
	 *
	 * @return void
	 */
	protected function loadRoutes()
	{
        if (defined('CUSTOM_CORE_DIR')) $paths[] = BOOT_DIR.'/custom_routes.php';
        $paths[] = BOOT_DIR.'/routes.php';

        $file = kernel::single('base_filesystem');
        foreach($paths as $path)
        {
            if ($file->exists($path)) return require($path);
        }

        throw new \ErrorException('Cannot load routes.');
	}
}

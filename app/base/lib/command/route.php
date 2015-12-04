<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use base_routing_collection as RouteCollection;
 
class base_command_route extends base_shell_prototype
{
    public function __construct()
    {
        $this->files = kernel::single('base_filesystem');
        parent::__construct();
    }

    public $command_cache = 'Create a route cache file for faster route registration';
    public function command_cache()
    {
        if ($this->files->exists(kernel::getCachedRoutesPath()))
        {
            $this->command_clear();
        }
        
        if (!$this->files->isWritable(dirname(kernel::getCachedRoutesPath())))
        {
            logger::info(sprintf('Route cache path:%s cannot write', kernel::getCachedRoutesPath()));
            exit;
        }

        
        $routes = route::getRoutes();

        if (count($routes)==0)
        {
            logger::info("Your application doesn't have any routes.");
            exit;
        }

		foreach ($routes as $route)
		{
			$route->prepareForSerialization();
		}

        $this->files->put(
            kernel::getCachedRoutesPath(), $this->buildRouteCacheFile($routes)
        );
        

        logger::info("Routes cached successfully!");
        
    }

	/**
	 * Build the route cache file.
	 *
	 * @param  \Illuminate\Routing\RouteCollection  $routes
	 * @return string
	 */
	protected function buildRouteCacheFile(RouteCollection $routes)
	{
		$stub = $this->files->get(__DIR__.'/stubs/routes.stub');

		return str_replace('{{routes}}', base64_encode(serialize($routes)), $stub);
	}
    
    public $command_clear = 'Remove the route cache file';
    function command_clear()
    {
        if (!$this->files->isWritable(kernel::getCachedRoutesPath()))
        {
            logger::info(sprintf('Sorry, route cahce path:%s cannot delete!', kernel::getCachedRoutesPath()));
        }
        $this->files->delete(kernel::getCachedRoutesPath());
        logger::info('Route cache Cleared!');
    }
}

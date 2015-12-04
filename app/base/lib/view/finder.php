<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_view_finder implements base_view_finder_interface
{
	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The array of active view paths.
	 *
	 * @var array
	 */
	protected $paths;

	/**
	 * The array of views that have been located.
	 *
	 * @var array base_view_object_interface
	 */
	protected $views = array();

	/**
	 * The default namespace.
	 *
	 * @var string
	 */
    protected $defaultNamespace = 'app';
    
	/**
	 * The namespace to file path hints.
	 *
	 * @var array
	 */
    protected $hints = array(
        'app' => ['handler' => 'base_view_object_file', 'paths' => [APP_DIR]],
    );

	/**
	 * Hint path delimiter value.
	 *
	 * @var string
	 */
	const HINT_PATH_DELIMITER = ':';
    
	/**
	 * Create a new file view loader instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @param  array  $paths
	 * @param  array  $extensions
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Get the fully qualified location of the view.
	 *
	 * @param  string  $name
	 * @return base_view_object_interface
	 */
	public function find($name)
	{
		if (isset($this->views[$name])) return $this->views[$name];

		if (!$this->hasHintInformation($name = trim($name)))
		{
            if (!isset($this->defaultNamespace)) throw new \ErrorException('Not set default namespace.');
            $name = $this->defaultNamespace.static::HINT_PATH_DELIMITER.$name;
		}
        return $this->views[$name] = $this->findNamedView($name);

	}

	/**
	 * Get the path to a template with a named path.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function findNamedView($name)
	{
		list($namespace, $view) = $this->getNamespaceSegments($name);

		return $this->findObject($namespace, $view);
	}
    

	/**
	 * Get the segments of a template with a named path.
	 *
	 * @param  string  $name
	 * @return array
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function getNamespaceSegments($name)
	{
		$segments = explode(static::HINT_PATH_DELIMITER, $name);

		if (count($segments) != 2)
		{
			throw new \InvalidArgumentException("View [$name] has an invalid name.");
		}

		if ( ! isset($this->hints[$segments[0]]))
		{
			throw new \InvalidArgumentException("No hint path defined for [{$segments[0]}].");
		}

		return $segments;
	}

	/**
	 * Find the given view in the list of paths.
	 *
	 * @param  string  $name
	 * @param  array   $paths
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function findObject($namespace, $view)
	{
        $handler = $this->hints[$namespace]['handler'];
        $paths = (array)$this->hints[$namespace]['paths'];

        
        if (!isset($handler))
        {
            throw new \InvalidArgumentException("No hint handler defined for [$namespace].");
        }

        if (array_search('base_view_object_interface', with(new ReflectionClass($handler))->getInterfaceNames())===false)
        {
            throw new \InvalidArgumentException("View [$namespace]:[$name] not found.");
        }

        if ($viewPath = forward_static_call(array($handler, 'verifyPath'), $view, $paths))
        {
            return with(new $handler($view, $viewPath));
        }
        
		throw new \InvalidArgumentException("View [$namespace]:[$name] not found.");
	}

	/**
	 * Init a namespace hint to the finder.
	 *
	 * @param  string  $namespace
	 * @param  string|array  $hints
	 * @return void
	 */
    public function addNamespace($namespace, $paths, $handler = null)
    {
		$paths = (array) $paths;

        if (isset($this->hints[$namespace]))
        {
            $hint['paths'] = array_merge((array)$this->hints[$namespace]['pahts'], $paths);
        }

        $hint['handler'] = ($handler !== null) ? $handler : $this->hints[$namespace]['handler'] ;

        $this->hints[$namespace] = $hint;

        return $this;
    }

	/**
	 * Init a namespace hint to the finder.
	 *
	 * @param  string  $namespace
	 * @param  string|array  $hints
	 * @return void
	 */
    public function prependNamespace($namespace, $paths, $handler = null) {
		$paths = (array) $paths;

        if (isset($this->hints[$namespace]))
        {
            $hint['paths'] = array_merge($paths, $this->hints[$namespace]['pahts']);
        }

        if ($handler !== null) $hint['handler'] = $handler;
        
        $this->hints[$namespace] = $hint;

        return $this;
    }

	/**
	 * set Default Namespace.
	 *
	 * @param  string  $name
	 * @return void
	 */
    public function setDefaultNamespace($namespace)
    {
        if (!empty($namespace) && isset($this->hints[$namespace]))
        {
            $this->defaultNamespace = $namespace;
            return $this;
        }

        throw new \InvalidArgumentException("Not exists default namespace [$namespace]");
    }
    
	/**
	 * Returns whether or not the view specify a hint information.
	 *
	 * @param  string  $name
	 * @return boolean
	 */
	public function hasHintInformation($name)
	{
		return strpos($name, static::HINT_PATH_DELIMITER) > 0;
	}

	/**
	 * Get the namespace to file path hints.
	 *
	 * @return array
	 */
	public function getHints()
	{
		return $this->hints;
	}
}

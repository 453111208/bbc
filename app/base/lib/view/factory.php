<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use base_support_contracts_interface_arrayable as Arrayable;

class base_view_factory
{
	/**
	 * The engine implementation.
	 *
	 * @var \Illuminate\View\Engines\EngineResolver
	 */
	protected $engine;

	/**
	 * The view finder implementation.
	 *
	 * @var base_view_finder_interface
	 */
	protected $finder;

	/**
	 * Data that should be available to all templates.
	 *
	 * @var array
	 */
	protected $shared = array();

	/**
	 * All of the finished, captured sections.
	 *
	 * @var array
	 */
	protected $sections = array();

	/**
	 * The stack of in-progress sections.
	 *
	 * @var array
	 */
	protected $sectionStack = array();

	/**
	 * The number of active rendering operations.
	 *
	 * @var int
	 */
	protected $renderCount = 0;

	/**
	 * The base_component_comiler instance .
	 *
	 * @var string
	 */
	protected $ui;


	/**
	 * Create a new view factory instance.
	 *
	 * @param  \Illuminate\View\Engines\EngineResolver  $engines
	 * @param  \Illuminate\View\ViewFinderInterface  $finder
	 * @param  \Illuminate\Events\Dispatcher  $events
	 * @return void
	 */
	public function __construct(base_view_engines_interface $engine, base_view_finder_interface $finder)
	{
		$this->finder = $finder;
		$this->engine = $engine;

		$this->share('__env', $this);
        $this->ui = kernel::single('base_component_ui');
	}

	/**
	 * Get the evaluated view contents for the given view.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @param  array   $mergeData
	 * @return \Illuminate\View\View
	 */
	public function make($view, $data = array(), $mergeData = array())
	{
		$object = $this->finder->find($view);

		$data = array_merge($mergeData, $this->parseData($data));

        $view = new base_view_view($this, $this->engine, $view, $object, $data);

		return $view;
	}

	/**
	 * Parse the given data into a raw array.
	 *
	 * @param  mixed  $data
	 * @return array
	 */
	protected function parseData($data)
	{
		return $data instanceof Arrayable ? $data->toArray() : $data;
	}

	/**
	 * Determine if a given view exists.
	 *
	 * @param  string  $view
	 * @return bool
	 */
	public function exists($view)
	{
		try
		{
			$this->finder->find($view);
		}
		catch (\InvalidArgumentException $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Add a piece of shared data to the environment.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function share($key, $value = null)
	{
		if ( ! is_array($key)) return $this->shared[$key] = $value;

		foreach ($key as $innerKey => $innerValue)
		{
			$this->share($innerKey, $innerValue);
		}
	}

	/**
	 * Get the appropriate view engine for the given path.
	 *
	 * @param  string  $path
	 * @return \Illuminate\View\Engines\EngineInterface
	 */
    public function getEngine()
    {
        return $this->engine;
    }

	/**
	 * Flush all of the section contents.
	 *
	 * @return void
	 */
	public function flushSections()
	{
		$this->sections = array();

		$this->sectionStack = array();
	}

	/**
	 * Flush all of the section contents if done rendering.
	 *
	 * @return void
	 */
	public function flushSectionsIfDoneRendering()
	{
		if ($this->doneRendering()) $this->flushSections();
	}

	/**
	 * Increment the rendering counter.
	 *
	 * @return void
	 */
	public function incrementRender()
	{
		$this->renderCount++;
	}

	/**
	 * Decrement the rendering counter.
	 *
	 * @return void
	 */
	public function decrementRender()
	{
		$this->renderCount--;
	}

	/**
	 * Check if there are no active render operations.
	 *
	 * @return bool
	 */
	public function doneRendering()
	{
		return $this->renderCount == 0;
	}

	/**
	 * Add a new namespace to the loader.
	 *
	 * @param  string  $namespace
	 * @param  string|array  $hints
	 * @return void
	 */
	public function addNamespace($namespace, $paths, $handler = null)
	{
		$this->finder->addNamespace($namespace, $paths, $handler);
	}

	/**
	 * Prepend a new namespace to the loader.
	 *
	 * @param  string  $namespace
	 * @param  string|array  $hints
	 * @return void
	 */
	public function prependNamespace($namespace, $paths, $handler = null)
	{
		$this->finder->prependNamespace($namespace, $paths, $handler);
	}

	/**
	 * Get the view finder instance.
	 *
	 * @return \Illuminate\View\ViewFinderInterface
	 */
	public function getFinder()
	{
		return $this->finder;
	}

	/**
	 * Get an item from the shared data.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function shared($key, $default = null)
	{
		return array_get($this->shared, $key, $default);
	}

	/**
	 * Get all of the shared data for the environment.
	 *
	 * @return array
	 */
	public function getShared()
	{
		return $this->shared;
	}

	/**
	 * Set the path to the view.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function ui($path)
	{
		return $this->ui;
	}
}



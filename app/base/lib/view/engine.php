<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_view_engine
{
	/**
	 * The Blade compiler instance.
	 *
	 * @var \Illuminate\View\Compilers\CompilerInterface
	 */
	protected $compiler;

	/**
	 * Create a new Blade view engine instance.
	 *
	 * @param  \Illuminate\View\Compilers\CompilerInterface  $compiler
	 * @return void
	 */
	public function __construct(base_view_compilers_interface $compiler)
	{
		$this->compiler = $compiler;
	}

	/**
	 * Get the evaluated contents of the view.
	 *
	 * @param  string  $path
	 * @param  array   $data
	 * @return string
	 */
	public function get(base_view_object_interface $object, array $data = array())
	{
		$this->lastCompiled[] = $object;

		// If this given view has expired, which means it has simply been edited since
		// it was last compiled, we will re-compile the views so we can evaluate a
		// fresh copy of the view. We'll pass the compiler the path of the view.
		if ($this->compiler->isExpired($object))
		{
			$compiled = $this->compiler->compile($object);
		}
        else
        {
            $compiled = $this->compiler->getCompiledContent($object);
        }
		// Once we have the path to the compiled file, we will evaluate the paths with
		// typical PHP just like any other templates. We also keep a stack of views
		// which have been rendered for right exception messages to be generated.

		$results = $this->evaluate($compiled, $data);

		array_pop($this->lastCompiled);

		return $results;
	}

	/**
	 * Handle a view exception.
	 *
	 * @param  \Exception  $e
	 * @return void
	 *
	 * @throws $e
	 */
	protected function handleViewException($e)
	{
		$e = new \ErrorException($this->getMessage($e), 0, 1, $e->getFile(), $e->getLine(), $e);

		ob_get_clean(); throw $e;
	}

	/**
	 * Get the exception message for an exception.
	 *
	 * @param  \Exception  $e
	 * @return string
	 */
	protected function getMessage($e)
	{
		return $e->getMessage().' (View: '.realpath(last($this->lastCompiled)).')';
	}

	/**
	 * Get the compiler implementation.
	 *
	 * @return \Illuminate\View\Compilers\CompilerInterface
	 */
	public function getCompiler()
	{
		return $this->compiler;
	}

	/**
	 * Get the evaluated contents.
	 *p
	 * @param  string  $__content
	 * @param  array   $__data
	 * @return string
	 */
    public function evaluate($__content, $__data)
    {
        //logger::error($__content);
        ob_start();

        extract($__data);
		// We'll evaluate the contents of the view inside a try/catch block so we can
		// flush out any stray output that might get out before an error occurs or
		// an exception is thrown. This prevents any partial views from leaking.

		try
		{
			eval('?>'.$__content);

		}
		catch (\Exception $e)
		{
			$this->handleViewException($e);
		}
		return ltrim(ob_get_clean());
    }
}

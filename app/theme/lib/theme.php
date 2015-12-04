<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class theme_theme
{
	/**
	 * Theme namespace.
	 */
    public static $namespace = 'theme';

	/**
	 * Theme configuration.
	 *
	 * @var mixed
	 */
	protected $themeConfig;

	/**
	 * View.
	 *
	 * @var base_view_factory
	 */
	protected $view;

	/**
	 * The name of theme.
	 *
	 * @var string
	 */
	protected $theme;

	/**
	 * The name of layout.
	 *
	 * @var string
	 */
	protected $layout;

	/**
	 * The name of layout.
	 *
	 * @var string
	 */
	protected $currentLayoutOrPartial;
    
	/**
	 * Content dot path.
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * Regions in the theme.
	 *
	 * @var array
	 */
	protected $regions = array();

	/**
	 * Content arguments.
	 *
	 * @var array
	 */
	protected $arguments = array();

	/**
	 * Data bindings.
	 *
	 * @var array
	 */
	protected $bindings = array();
    
    
	/**
	 * Engine compiler.
	 *
	 * @var array
	 */
	protected $compilers = array();

	/**
	 * Engine compiler.
	 *
	 * @var array
	 */
	protected $isPreview = false;
    

    /**
     * Create a new theme instance.
     *
     * @param  \Illuminate\View\Factory $view |
     *
     * @return \Teepluss\Theme\Theme
     */
    public function __construct($view)
    {
        $this->view = $view;

        // $this->theme = kernel::single('site_theme_base')->get_default();

        $this->layout = config::get('theme.layoutDefault');

        $this->compilers['tramsy'] = kernel::single('base_view_compilers_tramsy');

        $this->addPathLocation();
    }

	/**
	 * Get current theme name.
	 *
	 * @return string
	 */
    public function getThemeName()
    {
        return $this->theme;
    }

	/**
	 * Get current layout name.
	 *
	 * @return string
	 */
	public function getLayoutName()
	{
		return $this->layout;
	}

    /**
     * Get theme namespace.
     * 临时修改
     *
     * @param string $path
     *
     * @return string
     */
	public function getThemeNamespace($path = '')
	{
		// Namespace relate with the theme name.
		$namespace = static::$namespace.':'.$this->getThemeName();

		if ($path != false)
		{
			return $namespace.'/'.$path;
		}

		return $namespace;
	}

	/**
	 * Check theme exists.
	 * 预留接口, 暂时全部情况返回true
	 *
	 * @param  string  $theme
	 * @return boolean
	 */
	public function exists($theme)
	{
        $path = PUBLIC_DIR.'/'.$this->path($theme).'/';

        return is_dir($path);
	}

	/**
	 * Get theme config.
	 *
	 * @param  string $key
	 * @return mixed
	 */
	public function getConfig($key = null)
	{
		// Main package config.
		if ( ! $this->themeConfig)
		{
			$this->themeConfig = config::get('theme');
		}

		return is_null($key) ? $this->themeConfig : array_get($this->themeConfig, $key);
	}

	/**
	 * Add location path to look up.
	 *
	 * @param string $location
	 */
	protected function addPathLocation()
	{
		// First path is in the selected theme.
		$hints[] = THEME_DIR;

        $this->view->addNamespace(static::$namespace, $hints);
	}

    

    /**
     * Set up a theme name.
     *
     * @param  string $theme
     * @throws UnknownThemeException
     * @return Theme
     */
    public function theme($theme = null)
    {
        // If theme name is not set, so use default from config.
        if ($theme != false)
        {
            $this->theme = $theme;
        }

        if ( ! $this->exists($theme))
        {
            throw new UnknownThemeException("Theme [$theme] not found.");
        }

        return $this;
    }

	/**
	 * Alias of theme method.
	 *
	 * @param  string $theme
	 * @return Theme
	 */
	public function uses($theme = null)
	{
		return $this->theme($theme);
	}

	/**
	 * Set up a layout name.
	 *
	 * @param  string $layout
	 * @return Theme
	 */
	public function layout($layout)
	{
		// If layout name is not set, so use default from config.
		if ($layout != false)
		{
			$this->layout = $layout;
		}

		return $this;
	}

	/**
	 * Get theme path.
	 *
	 * @param  string $forceThemeName
	 * @return string
	 */
	public function path($forceThemeName = null)
	{
		$themeDir = $this->getConfig('themeDir');

		$theme = $this->theme;

		if ($forceThemeName != false)
		{
			$theme = $forceThemeName;
		}

		return $themeDir.'/'.$theme;
	}
    

	/**
	 * Set a place to regions.
	 *
	 * @param  string $region
	 * @param  string $value
	 * @return Theme
	 */
	public function set($region, $value)
	{
		// Content is reserve region for render sub-view.
		if ($region == 'content') return;

		$this->regions[$region] = $value;

		return $this;
	}

	/**
	 * Append a place to existing region.
	 *
	 * @param  string $region
	 * @param  string $value
	 * @return Theme
	 */
	public function append($region, $value)
	{
		return $this->appendOrPrepend($region, $value, 'append');
	}

	/**
	 * Prepend a place to existing region.
	 *
	 * @param  string $region
	 * @param  string $value
	 * @return Theme
	 */
	public function prepend($region, $value)
	{
		return $this->appendOrPrepend($region, $value, 'prepend');
	}

	/**
	 * Append or prepend existing region.
	 *
	 * @param  string $region
	 * @param  string $value
	 * @param  string $type
	 * @return Theme
	 */
	protected function appendOrPrepend($region, $value, $type = 'append')
	{
		// If region not found, create a new region.
		if (isset($this->regions[$region]))
		{
			if ($type == 'prepend')
			{
				$this->regions[$region] = $value.$this->regions[$region];
			}
			else
			{
				$this->regions[$region] .= $value;
			}
		}
		else
		{
			$this->set($region, $value);
		}

		return $this;
	}

	/**
	 * Binding data to view.
	 *
	 * @param  string $variable
	 * @param  mixed  $callback
	 * @return mixed
	 */
	public function bind($variable, $callback = null)
	{
		$name = 'bind.'.$variable;

		// If callback pass, so put in a queue.
		if ( ! empty($callback))
		{
            array_set($this->bindings, $name, $callback);
		}

        return array_get($this->bindings, $name, function(){});
	}

	/**
	 * Check having binded data.
	 *
	 * @param  string $variable
	 * @return boolean
	 */
	public function binded($variable)
	{
		$name = 'bind.'.$variable;

		return isset($this->bindings[$name]);
	}

	/**
	 * Assign data across all views.
	 *
	 * @param  mixed $key
	 * @param  mixed $value
	 * @return mixed
	 */
	public function share($key, $value)
	{
		return $this->view->share($key, $value);
	}
    
    /**
     * Set up a partial.
     *
     * @param  string $view
     * @param  array $args
     * @throws UnknownPartialFileException
     * @return mixed
     */
	public function partial($view, $args = array())
	{
		$partialDir = $this->getThemeNamespace($this->getConfig('containerDir.partial'));
		return $this->loadPartial($view, $partialDir, $args);
	}

	/**
	 * The same as "partial", but having prefix layout.
	 *
	 * @param  string $view
     * @param  array $args
     * @throws UnknownPartialFileException
     * @return mixed
	 */
	public function partialWithLayout($view, $args = array())
	{
		$view = $this->getLayoutName().'/'.$view;

		return $this->partial($view, $args);
	}

	/**
	 * Load a partial
	 *
	 * @param  string $view
	 * @param  string $partialDir
	 * @param  array  $args
	 * @throws UnknownPartialFileException
	 * @return mixed
	 */
	public function loadPartial($view, $partialDir, $args)
	{
		$path = $partialDir.'/'.$view;
 
        // todo: 
        $this->currentLayoutOrPartial = $path;

		if ( ! $this->view->exists($path))
		{
			throw new UnknownPartialFileException("Partial view [$view] not found.");
		}

		$partial = $this->fixThemeMedia($this->view->make($path, $args)->render());
        /*
        if (view::getEngine()->getCompiler()->isExpired(view::getFinder()->find($view)))
        {
            $partial = $this->fixThemeMedia($partial);
        }
        */

		$this->regions[$view] = $partial;
        
		return $this->regions[$view];
	}

	/**
	 * Get compiler.
	 *
	 * @param  string $compiler
	 * @return object
	 */
	public function getCompiler($compiler)
	{
		if (isset($this->compilers[$compiler]))
		{
			return $this->compilers[$compiler];
		}
	}

    /**
     * Parses and compiles strings by using tramsy template system.
     *
     * @param  string $str
     * @param  array $data
     * @param  boolean $phpCompile
     * @throws \Exception
     * @return string
     */
	public function tramsier($str, $data = array(), $phpCompile = true)
	{
		if ($phpCompile == false)
		{
			$patterns = array('|<\?|', '|<\?php|', '|<\%|', '|\?>|', '|\%>|');
			$replacements = array('&lt;?', '&lt;php', '&lt;%', '?&gt;', '%&gt;');

			$str = preg_replace($patterns, $replacements, $str);
		}

		// Get blade compiler.
		$parsed = $this->getCompiler('tramsy')->compileString($str);

		ob_start() and extract($data, EXTR_SKIP);

		try
		{
			eval('?>'.$parsed);
		}
		catch (\Exception $e)
		{
			ob_end_clean(); throw $e;
		}

		$str = ob_get_contents();
		ob_end_clean();

		return $str;
	}
    

	/**
	 * Check region exists.
	 *
	 * @param  string  $region
	 * @return boolean
	 */
	public function has($region)
	{
		return (boolean) isset($this->regions[$region]);
	}

	/**
	 * Render a region.
	 *
	 * @param  string $region
	 * @param  mixed  $default
	 * @return string
	 */
	public function get($region, $default = null)
	{
		if ($this->has($region))
		{
			return $this->regions[$region];
		}

		return $default ? $default : '';
	}

	/**
	 * Render a region.
	 *
	 * @param  string $region
	 * @param  mixed  $default
	 * @return string
	 */
	public function place($region, $default = null)
	{
		return $this->get($region, $default);
	}

	/**
	 * Place content in sub-view.
	 *
	 * @return string
	 */
	public function content()
	{
		return $this->regions['content'];
	}

	/**
	 * Set up a content to template.
	 *
	 * @param  string $view
	 * @param  array  $args
	 * @param  string $type
	 * @return Theme
	 */
	public function of($view, $args = array(), $type = null)
	{
		// Layout.
		$layout = ucfirst($this->layout);

		// Keeping arguments.
		$this->arguments = $args;

		// Compile string blade, string twig, or from file path.
		switch ($type)
		{
			case 'tramsy' :
				$content = $this->tramsier($view, $args);
				break;
			default :
				$content = $this->fixThemeMedia($this->view->make($view, $args)->render());
				break;
		}

		// View path of content.
		$this->content = $view;

		// Set up a content regional.
		$this->regions['content'] = $content;

		return $this;
	}

	/**
	 * Get all arguments assigned to content.
	 *
	 * @return mixed
	 */
	public function getContentArguments()
	{
		return $this->arguments;
	}

    /**
     * Get a argument assigned to content.
     *
     * @param  string $key
     * @param null $default
     * @return mixed
     */
	public function getContentArgument($key, $default = null)
	{
		return array_get($this->arguments, $key, $default);
	}

	/**
	 * Checking content argument existing.
	 *
	 * @param  string  $key
	 * @return boolean
	 */
	public function hasContentArgument($key)
	{
		return (bool) isset($this->arguments[$key]);
	}

	/**
	 * Find view location.
	 *
	 * @param  boolean $realpath
	 * @return base_view_object_interface | string
	 */
	public function location($realpath = false)
	{
		if ($this->view->exists($this->content))
		{
			return ($realpath) ? $this->view->getFinder()->find($this->content) : $this->content;
		}
	}

	/**
	 * Compile from string.
	 *
	 * @param  string $str
	 * @param  array  $args
	 * @param  string $type
	 * @return Theme
	 */
	public function string($str, $args = array(), $type = 'blade')
	{
		$shared = $this->view->getShared();

		$args = array_merge($data, $args);

		return $this->of($str, $args, $type);
	}

    /**
     * Return a template with content.
     *
     * @param  integer $statusCode
     * @throws UnknownLayoutFileException
     * @return Response
     */
	public function render($statusCode = 200)
	{

		// Layout directory.
		$layoutDir = $this->getConfig('containerDir.layout');
        if (empty($layoutDir))
        {
            $path = $this->getThemeNamespace($this->layout);
        }
        else
        {
            $path = $this->getThemeNamespace($layoutDir.'/'.$this->layout);
        }

        // todo: 
        $this->currentLayoutOrPartial = $path;

		if ( ! $this->view->exists($path))
		{
			throw new UnknownLayoutFileException("Layout [$this->layout] not found.");
		}

		$content = $this->fixThemeMedia($this->view->make($path)->render());

		// Append status code to view.
		$content = new base_http_response($content, $statusCode);

		return $content;
	}

	/**
	 * Magic method for set, prepend, append, has, get.
	 *
	 * @param  string $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters = array())
	{
		$callable = preg_split('|[A-Z]|', $method);

		if (in_array($callable[0], array('set', 'prepend', 'append', 'has', 'get')))
		{
			$value = lcfirst(preg_replace('|^'.$callable[0].'|', '', $method));

			array_unshift($parameters, $value);

			return call_user_func_array(array($this, $callable[0]), $parameters);
		}

		trigger_error('Call to undefined method '.__CLASS__.'::'.$method.'()', E_USER_ERROR);
	}

    public function getCurrentLayoutOrPartial()
    {
        return $this->currentLayoutOrPartial;
    }

	/**
	 * 是否演示
	 *
	 * @return bool
	 */
	public function isPreview()
	{
        return $this->isPreview;
	}

	/**
	 * Magic method for set, prepend, append, has, get.
	 *
	 * @param  string $method
	 * @return bool
	 */
	public function preview()
	{
        $this->isPreview = true;
        return $this;
	}

    /*
     * 修补模板媒体文件 todo: 临时做法
     * @var string $code
     * @access public
     * @return string
     */
    private function fixThemeMedia($code)
    {
        //return $code;
        $from = array(
            '/((?:background|src|href)\s*=\s*["|\'])(?:\.\/|\.\.\/)?(images\/.*?["|\'])/is',
            '/((?:background|background-image):\s*?url\()(?:\.\/|\.\.\/)?(images\/)/is',
        );


        $themeUrl = kernel::get_themes_host_url();

        $to = array(
            sprintf('\1%s\2', $themeUrl.'/'.theme::getThemeName().'/'),
            sprintf('\1%s\2', $themeUrl.'/'.theme::getThemeName().'/'),
        );
        //        var_dump($to);exit;
        
        return preg_replace($from, $to, $code);
    }//End Function
    
    
}



class UnknownThemeException extends \UnexpectedValueException {}
class UnknownViewFileException extends \UnexpectedValueException {}
class UnknownLayoutFileException extends \UnexpectedValueException {}
class UnknownWidgetFileException extends \UnexpectedValueException {}
class UnknownWidgetClassException extends \UnexpectedValueException {}
class UnknownPartialFileException extends \UnexpectedValueException {}

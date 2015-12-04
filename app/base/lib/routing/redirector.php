<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

//use Illuminate\Http\RedirectResponse;

class base_routing_redirector {

	/**
	 * The URL generator instance.
	 *
	 * @var \Illuminate\Routing\UrlGenerator
	 */
	protected $generator;

	/**
	 * The session store instance.
	 *
	 * @var \Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Create a new Redirector instance.
	 *
	 * @param  \Illuminate\Routing\UrlGenerator  $generator
	 * @return void
	 */
	public function __construct(base_routing_urlgenerator $generator)
	{
		$this->generator = $generator;
	}

	/**
	 * Create a new redirect response to the previous location.
	 *
	 * @param  int    $status
	 * @param  array  $headers
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function back($status = 302, $headers = array())
	{
		$back = $this->generator->getRequest()->headers->get('referer');

		return $this->createRedirect($back, $status, $headers);
	}

	/**
	 * Create a new redirect response to the current URI.
	 *
	 * @param  int    $status
	 * @param  array  $headers
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function refresh($status = 302, $headers = array())
	{
		return $this->to($this->generator->getRequest()->path(), $status, $headers);
	}

	/**
	 * Create a new redirect response to the given path.
	 *
	 * @param  string  $path
	 * @param  int     $status
	 * @param  array   $headers
	 * @param  bool    $secure
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function to($path, $status = 302, $headers = array(), $secure = null)
	{
		$path = $this->generator->to($path, array(), $secure);

		return $this->createRedirect($path, $status, $headers);
	}

	/**
	 * Create a new redirect response to an external URL (no validation).
	 *
	 * @param  string  $path
	 * @param  int     $status
	 * @param  array   $headers
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function away($path, $status = 302, $headers = array())
	{
		return $this->createRedirect($path, $status, $headers);
	}

	/**
	 * Create a new redirect response to the given HTTPS path.
	 *
	 * @param  string  $path
	 * @param  int     $status
	 * @param  array   $headers
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function secure($path, $status = 302, $headers = array())
	{
		return $this->to($path, $status, $headers, true);
	}

	/**
	 * Create a new redirect response to a named route.
	 *
	 * @param  string  $route
	 * @param  array   $parameters
	 * @param  int     $status
	 * @param  array   $headers
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function route($route, $parameters = array(), $status = 302, $headers = array())
	{
		$path = $this->generator->route($route, $parameters);

		return $this->to($path, $status, $headers);
	}

	/**
	 * Create a new redirect response to a controller action.
	 *
	 * @param  string  $action
	 * @param  array   $parameters
	 * @param  int     $status
	 * @param  array   $headers
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function action($action, $parameters = array(), $status = 302, $headers = array())
	{
		$path = $this->generator->action($action, $parameters);

		return $this->to($path, $status, $headers);
	}

	/**
	 * Create a new redirect response.
	 *
	 * @param  string  $path
	 * @param  int     $status
	 * @param  array   $headers
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function createRedirect($path, $status, $headers)
	{
		$redirect = new base_http_response_redirect($path, $status, $headers);

		$redirect->setRequest($this->generator->getRequest());

		return $redirect;
	}

	/**
	 * Get the URL generator instance.
	 *
	 * @return  \Illuminate\Routing\UrlGenerator
	 */
	public function getUrlGenerator()
	{
		return $this->generator;
	}

}

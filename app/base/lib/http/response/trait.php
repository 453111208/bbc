<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use Symfony\Component\HttpFoundation\Cookie;

trait base_http_response_trait {

	/**
	 * Set a header on the Response.
	 *
	 * @param  string  $key
	 * @param  string  $value
	 * @param  bool    $replace
	 * @return \Illuminate\Http\Response
	 */
	public function header($key, $value, $replace = true)
	{
		$this->headers->set($key, $value, $replace);

		return $this;
	}

	/**
	 * Add a cookie to the response.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Cookie  $cookie
	 * @return \Illuminate\Http\Response
	 */
	public function withCookie(Cookie $cookie)
	{
		$this->headers->setCookie($cookie);

		return $this;
	}

}


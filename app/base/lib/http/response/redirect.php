<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class base_http_response_redirect extends \Symfony\Component\HttpFoundation\RedirectResponse {

    use base_http_response_trait;

	/**
	 * The request instance.
	 *
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * Get the request instance.
	 *
	 * @return  \Illuminate\Http\Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Set the request instance.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;
	}
    
    
}

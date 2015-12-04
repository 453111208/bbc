<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use Exception;

interface base_exception_displayer_interface {

	/**
	 * Display the given exception to the user.
	 *
	 * @param  \Exception  $exception
	 */
	public function display(Exception $exception);

}


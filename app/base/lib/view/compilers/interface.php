<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

interface base_view_compilers_interface {

	/**
	 * Get the path to the compiled version of a view.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function getCompiledContent($object);

	/**
	 * Determine if the given view is expired.
	 *
	 * @param  string  $path
	 * @return bool
	 */
	public function isExpired($object);

	/**
	 * Compile the view at the given path.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function compile(base_view_object_interface $object);

	/**
	 * Compile the view at the given path.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function compileString($content);
    
}

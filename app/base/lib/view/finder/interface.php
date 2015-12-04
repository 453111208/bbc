<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

interface base_view_finder_interface
{
	/**
	 * Get the fully qualified location of the view.
	 *
	 * @param  string  $view
	 * @return string
	 */
	public function find($view);

	/**
	 * Add a namespace hint to the finder.
	 *
	 * @param  string  $namespace
	 * @param  string|array  $hints
	 * @return void
	 */
	public function addNamespace($namespace, $hints);
    
	/**
	 * Prepend a namespace hint to the finder.
	 *
	 * @param  string  $namespace
	 * @param  string|array  $hints
	 * @return void
	 */
	public function prependNamespace($namespace, $hints);

}

<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

interface base_view_object_interface
{

	/**
	 * Verify which path can find object.
	 *
	 * @param string $view
	 * @param array $paths
	 * @return string
	 */
    public function __construct($view, $path);
    
	/**
	 * Verify which path can find object.
	 *
	 * @param string $view
	 * @param array $paths
	 * @return string
	 */
    static public function verifyPath($view, array $paths);

	/**
	 * Get the object's last modification time.
	 *
	 * @param  string  $path
	 * @return int
	 */
    public function lastModified();

	/**
	 * Get the contents of a object.
	 *
	 * @param  string  $path
	 * @return string
	 *
	 * @throws FileNotFoundException
	 */
    public function get();

	/**
	 * Put the contents to a object.
	 *
	 * @param  string  $path
	 * @return string
	 */
    public function put($content);
    

	/**
	 * Get the path of a object.
	 *
	 * @param  string  $path
	 * @return string
	 */
    public function getPath();

	/**
	 * Get the view of a object.
	 *
	 * @param  string  $path
	 * @return string
	 */
    public function getView();

	/**
	 * Get the namespace of the object.
	 *
	 * @param  string  $path
	 * @return string
	 */
    static public function getNamespace();
}

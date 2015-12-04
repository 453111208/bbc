<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

abstract class base_view_object_object
{
	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
    protected $view;

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
    protected $path;



    public function __construct($view, $path)
    {
        $this->view = $view;
        $this->path = $path;
    }

	/**
	 * Get the path currently being compiled.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Get the  of currently object.
	 *
	 * @return string
	 */
	public function getView()
	{
		return $this->view;
	}

	/**
	 * Get the  of object uniq id.
	 *
	 * @return string
	 */
    public function getUniqid()
    {
        return static::getNamespace().'::'.$this->view;
    }
}

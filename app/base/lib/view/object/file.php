<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

abstract class base_view_object_file extends base_view_object_object
{
	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

    public function __construct($view, $path)
    {
        parent::__construct($view, $path);
        $this->files = kernel::single('base_filesystem');
    }

	/**
	 * Get the object's last modification time.
	 *
	 * @param  string  $path
	 * @return int
	 */
    public function lastModified()
    {
        $this->files->lastModified($this->path);
    }

	/**
	 * Get the contents of a object.
	 *
	 * @param  string  $path
	 * @return string
	 *
	 * @throws FileNotFoundException
	 */
    public function get()
    {
        return $this->files->get($this->getPath());
    }

	/**
	 * Put the contents to a object.
	 *
	 * @param  string  $path
	 * @return string
	 */
    public function put($content)
    {
        return $this->files->put($this->getPath(), $content);
    }

}

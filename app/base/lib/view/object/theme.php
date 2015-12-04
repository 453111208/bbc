<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_view_object_theme extends base_view_object_file implements base_view_object_interface
{
    static $namespace = 'theme';

	/**
	 * Verify which path can find object.
     * theme:luckymall@cart#site/cart/index.html
     * theme:[模板@][主题@][文件路径]
     * 只有模板
     * theme:模板@
     * 模板+
	 * http://d
	 * @param string $view
	 * @param array $paths
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
    static public function verifyPath($view, array $paths)
    {
        if (!empty($paths))
        {
            foreach ($paths as $path)
            {
                $viewPath = $path.'/'.$view;
                if (kernel::single('base_filesystem')->exists($viewPath)) return $viewPath;
            }
            throw new \InvalidArgumentException("Object theme: [$view] not found.");
        }
        throw new \InvalidArgumentException('Object theme: [$view], verifyPath\'s paths parameter is null');
    }

	/**
	 * Get the namespace of the object.
	 *
	 * @param  string  $path
	 * @return string
	 */
    static public function getNamespace()
    {
        return static::$namespace;
    }    
}

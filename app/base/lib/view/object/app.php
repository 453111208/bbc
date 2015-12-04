<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_view_object_app extends base_view_object_file implements base_view_object_interface
{

    static $namespace = 'app';

    /**
     * Verify which path can find object.
     *
     * @param string $view
     * @param array $paths
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    static public function verifyPath($view, array $paths)
    {
        $pos = strpos($view, '/');
        $viewPath = substr($view, 0, $pos).'/view'.substr($view, $pos);
        if (!empty($paths))
        {
            foreach ($paths as $path)
            {
                $realViewPath = $path.'/'.$viewPath;
                if (kernel::single('base_filesystem')->exists($realViewPath))
                {
                    return $realViewPath;
                }
            }
            throw new \InvalidArgumentException("Object app: [$view] not found.");
        }

        throw new \InvalidArgumentException('Object app: [$view], verifyPath\'s paths parameter is null');
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

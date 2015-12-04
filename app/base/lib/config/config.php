<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_static_config
{
    static $environment = 'production';

    static protected $_items = array();

    static public function get_path()
    {
        return ROOT_DIR.'/config';
    }

    static private function parse_key($key)
    {
        $segments =  explode('.', $key);
        $group = $segments[0];
        if (count($segments) == 1){
            return array($group, null);
        }else{
            $item = implode('.', array_slice($segments, 1));

            return array($group, $item);
        }
    }

    static public function set($key, $value)
    {
        list($group, $item) = static::parse_key($key);
        static::load($group);
        if (is_null($item)) {
            static::$_items[$group] = $value;
        } else {
            array_set(self::$_items[$group], $item, $value);
        }
    }

    static public function get($key, $default=null)
    {
        list($group, $item) = static::parse_key($key);
        static::load($group);
        return array_get(static::$_items[$group], $item, $default);
    }

    private static function load($group)
    {
        $env = static::$environment;
        
        if (isset(static::$_items[$group]))
        {
            return;
        }

        $items = static::realLoad($env, $group);

        static::$_items[$group] = $items;
    }

    private static function realLoad($environment, $group)
    {
        $items = array();
        
        $path = static::get_path();

		if (is_null($path))
		{
			return $items;
		}

        $files = kernel::single('base_filesystem');
        $file = "{$path}/{$environment}/{$group}.php";

		if ($files->exists($file))
		{
			//$items = static::mergeEnvironment($items, $file);
            $items = $files->getRequire($file);
            return $items;
		}
        else
        {
            $file = "{$path}/{$group}.php";

            if ($files->exists($file))
            {
                $items = $files->getRequire($file);
            }
            return $items;
        }

        /*
        $file = "{$path}/{$group}.php";

        $files = kernel::single('base_filesystem');

        if ($files->exists($file))
        {
            $items = $files->getRequire($file);
        }

        $file = "{$path}/{$environment}/{$group}.php";

		if ($files->exists($file))
		{
			$items = static::mergeEnvironment($items, $file);
		}
        */
        return $items;
        
    }

	/**
	 * Merge the items in the given file into the items.
	 *
	 * @param  array   $items
	 * @param  string  $file
	 * @return array
	 */
	static protected function mergeEnvironment(array $items, $file)
	{
        $files = kernel::single('base_filesystem');
		return array_replace_recursive($items, $files->getRequire($file));
	}
}

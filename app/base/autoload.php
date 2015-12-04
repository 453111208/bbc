<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class ClassLoader
{
	/**
	 * Indicates if a ClassLoader has been registered.
	 *
	 * @var bool
	 */
    protected static $_registed = false;

	/**
	 * The array of class aliases.
	 *
	 * @var array
	 */
    protected static $_aliases = array();

	/**
	 * app支持的类类型. 默认的lib不算在之内
	 *
	 * @var array
	 */
    protected static $_supportAppTypes = ['ctl', 'mdl', 'api', 'ego', 'middleware'];

	/**
	 * app支持的类型和目录的对应关系, 如果$_supportAppTypes里有定义, 而此
	 * 定义中不存在, 那么默认用类型名作为目录名. 
	 *
	 * @var array
	 */
    protected static $_supportAppTypesCorrDirectory = ['ctl' => 'controller',
                                                       'mdl' => 'model'];

	/**
	 * Register the given class loader on the auto-loader stack.
	 *
	 * @return bool
	 */
    public static function register()
    {
        if (!static::$_registed)
        {
            static::$_registed = spl_autoload_register(array('\ClassLoader', 'load'));
        }
    }

	/**
	 * Add the alias to ClassLoader
	 *
	 * @param  string  $class
     * @param  string  $alias
	 * @return bool
	 */
    public static function addAlias($class, $alias)
    {
        static::$_aliases[$class] = $alias;
    }

	/**
	 * Add the aliases to ClassLoader
	 *
	 * @param  array  $aliases
	 * @return bool
	 */
    public static function addAliases($aliases)
    {
        if (is_array($aliases))
        {
            static::$_aliases = array_merge(static::$_aliases, $aliases);
        }
    }

    public static function commonLoad($appId, $className, $type, $classNamePath)
    {
        $typePath = static::$_supportAppTypesCorrDirectory[$type] ?: $type;
        $relativePath = sprintf('%s/%s/%s.php', $appId, $typePath, $classNamePath);
        if(defined('CUSTOM_CORE_DIR')) $paths[] = CUSTOM_CORE_DIR.'/'.$relativePath;
        $paths[] = APP_DIR.'/'.$relativePath;

        foreach ($paths as $path)
        {
            if (file_exists($path))
            {
                return require_once($path);
            }
        }
        throw new RuntimeException('Don\'t find '.$type.' file:'.$className);
        
    }

	/**
	 * Load the given class file.
	 *
	 * @param  string  $class
	 * @return bool
	 */
    public static function load($className)    
    {
        // 检测alias
        if (array_key_exists($className, static::$_aliases)) {
            return class_alias(static::$_aliases[$className], $className);
        }

        list($appId) = $fragments = explode('_', $className);
        if (in_array($fragments[1], static::$_supportAppTypes))
        {
            $type = $fragments[1];
            switch ($type)
            {
                case 'ctl': case 'ego' : case 'api': case 'middleware':
                    static::commonLoad($appId, $className, $type, implode('/', array_slice($fragments, 2)));
                case 'mdl':
                    try
                    {
                        static::commonLoad($appId, $className, $type, implode('/', array_slice($fragments, 2)));
                    }
                    catch(RuntimeException $e)
                    {
                        $paths = [];
                        $relativePath = sprintf('%s/dbschema/%s.php', $appId, implode('_', array_slice($fragments, 2)));
                        if(defined('CUSTOM_CORE_DIR')) $paths[] = CUSTOM_CORE_DIR.'/'.$relativePath;
                        $paths[] = APP_DIR.'/'.$relativePath;

                        foreach ($paths as $path)
                        {
                            if (file_exists($path))
                            {
                                $parent_model_class = app::get($appId)->get_parent_model_class();
                                eval ("class {$className} extends {$parent_model_class}{ }");
                                return true;
                            }
                        }
                        throw new RuntimeException('Don\'t find model file "'.$className.'"');
                    }
            }
        }
        else
        {
            static::commonLoad($appId, $className, 'lib', implode('/', array_slice($fragments, 1)));
        }
    }//End Function    
}


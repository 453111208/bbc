<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

abstract class base_view_compilers_compiler
{
	/**
	 * Get the object currently being compiled.
	 *
	 * @return string
	 */
	public function getObject()
	{
		return $this->object;
	}

	/**
	 * Set the object currently being compiled.
	 *
	 * @param string $path
	 * @return void
	 */
	public function setObject($object)
	{
		$this->object = $object;
	}

	/**
	 * Compile the view at the given path.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function compile(base_view_object_interface $object)
	{
        $this->setObject($object);

        $contents = $this->compileString($object->get());

        if (config::get('cache.enabled', false) == true)
        {
            $cache = [
                'lastModified' => time(),
                'contents' => $contents
            ];

            cacheobject::set($this->getCompiledCacheKey($object), $cache);
        }

        return $contents;
	}


	/**
	 * Get the path to the compiled version of a view.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function getCompiledContent($object)
	{
        // 安装设计要求, 此处是一定可以取道到存的. 但不排除特殊情况, 因此此处如果取不到会再生成一次缓存
        if (cacheobject::get($this->getCompiledCacheKey($object), $cache) === false)
        {
            return $this->compile($object);
        }

        return $cache['contents'];
	}

	/**
	 * Determine if the view at the given path is expired.
	 *
	 * @param  string  $path
	 * @return bool
	 */
	public function isExpired($object)
	{
        if (cacheobject::get($this->getCompiledCacheKey($object), $cache) === false)
        {
            return true;
        }

        $lastModified = $object->lastModified();

        return $lastModified >= $cache['lastModified'];
	}

	/**
	 * Get the view cache key.
	 *
	 * @param  string  $path
	 * @return void
	 */
    protected function getCompiledCacheKey($object)
    {
        return 'view:'.$object->getUniqid();
    }

}

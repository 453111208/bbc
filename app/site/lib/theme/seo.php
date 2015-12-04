<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class site_theme_seo
{
	/**
	 * Seo title
	 *
	 * @var string
	 */
    private static $title;
    
	/**
	 * Seo Keywords
	 *
	 * @var string
	 */
    private static $keywords;

	/**
	 * Seo Description
	 *
	 * @var string
	 */
    private static $description;

	/**
	 * Seo icon href
	 *
	 * @var string
	 */
    private static $icon;

	/**
	 * Is use nofollow
	 *
	 * @var bool
	 */    
    private static $nofollow;

	/**
	 * Is use noindex
	 *
	 * @var string
	 */    
    private static $noindex;
    
    static public function setKeywords($keywords)
    {
        static::$keywords = $keywords;
    }

    static public function setDescription($description)
    {
        static::$description = $description;
    }

    static public function setTitle($title)
    {
        static::$title = $title;
    }

    static public function setIcon($title)
    {
        static::$title = $title;
    }

    static public function setNofollow($nofollow)
    {
        static::$nofollow = $nofollow;
    }

    static public function setNoindex($noindex)
    {
        static::$noindex = $noindex;
    }

    static public function getIcon($icon)
    {
        static::$icon;
    }
    
    static public function getTitle()
    {
        return static::$title;
    }

    static public function getDescription()
    {
        return static::$description;
    }

    static public function getKeywords()
    {
        return static::$keywords;
    }

    static public function getNoindex()
    {
        return static::$noindex?:false;
    }


    static public function getNofollow()
    {
        return static::$nofollow;
    }
    

    
}

<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_view_object_messenger extends base_view_object_object implements base_view_object_interface
{

    static public $namespace = 'messenger';
    static public $contents = null;
    public $content = null;
    static public $lastModified = null;

    /**
     * Create a new messenger instance.
     *
     * @param  string  $view
     * @param  string  $path
     * @return void
     */
    public function __construct($view, $path)
    {
        parent::__construct($view, $path);
        $view = static::$namespace . ':' . $view;
        if(self::$contents[$view])
        {
            $this->content = self::$contents[$view];
        }
    }

	/**
	 * Verify which path can find object.
	 *
	 * @param string $view
	 * @param array $pathcontentss
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
    static public function verifyPath($view, array $paths)
    {
        $tmplName = static::$namespace.":".$view;
        $objMdlSystmpl = app::get('system')->model('messenger_systmpl');
        $systmpl = $objMdlSystmpl->getRow("modified_time,content",array('active'=>'true','tmpl_name'=>$tmplName));
        if($systmpl)
        {
            self::$contents[$tmplName] = $systmpl['content'];
            self::$lastModified[$tmplName] = $systmpl['modified_time'];
            return true;
        }
        else
        {
            $tpl = explode("/",$view);
            $list = explode("_",$tpl[0]);
            $filename = ROOT_DIR."/app/".$list[0].'/view/admin/'.$list[1].'/'.$list[2].'/'.$tpl[1].'.html';

            if (kernel::single('base_filesystem')->exists($filename))
            {
                $tmplfile = fopen("$filename", "r") ;
                self::$contents[$tmplName] = fread($tmplfile,filesize($filename));
                return true;
            }
            throw new \InvalidArgumentException("Object app: [$tmplName] not found.");
        }
        throw new \InvalidArgumentException('Object app: [$tmplName], verifyPath\'s paths parameter is null');
    }


	/**
	 * Get the object's last modification time.
	 *
	 * @param  string  $path
	 * @return int
	 */
    public function lastModified()
    {
        return $this->lastModified;
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
        return $this->content;
    }

	/**
	 * Put the contents to a object.
	 *
	 * @param  string  $path
	 * @return string
	 */
    public function put($content)
    {
        return $this->content;
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

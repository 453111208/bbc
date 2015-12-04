<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
use Symfony\Component\Finder\Finder;
 
class dev_command_touch extends base_shell_prototype{
    
    var $command_files = 'touch所有文件';
    function command_files()
    { #查找包含bom头的文件
        foreach(iterator_to_array(Finder::create()->files()->in(APP_DIR)) as $file)
        {
            $time = time();
            $realFile = $file->getPath().'/'.$file->getFileName();
            echo $realFile.PHP_EOL;
            touch($realFile, $time);
        }
    }
}


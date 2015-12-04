<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_db_datatype_manage 
{

    private $typeDefines = null;

    public function __construct()
    {
        $this->load();
    }
    
    /**
     * 获取datatype 完整定义数组
     *
     * @return string
     */
    public function load()
    {
        if (!$this->typeDefines)
        {
            if(defined('CUSTOM_CORE_DIR') && file_exists(CUSTOM_CORE_DIR.'/base/datatypes.php'))
            {
                $typeDefines = include(CUSTOM_CORE_DIR.'/base/datatypes.php');
            }
            else
            {
                $typeDefines = include(APP_DIR.'/base/datatypes.php');
            }
            $this->typeDefines = $typeDefines;
        }
        
        return $this->typeDefines;
    }

    public function isExistDefine($type)
    {
        return $this->typeDefines[$type] ? true : false;
    }

    public function getDefineSql($type)
    {
        if (!$type) return null;
        return $this->typeDefines[$type]['sql'];
    }

    public function getDefineDoctrineType($type)
    {
        if (!$type) return null;
        return $this->typeDefines[$type]['doctrineType'];
    }

    public function getDefineFuncInput($type)
    {
        if (!$type) return null;
        return $this->typeDefines[$type]['func_input'];
    }
    
    public function getDefineFuncOutput($type)
    {
        if (!$type) return null;
        return $this->typeDefines[$type]['func_output'];
    }
}


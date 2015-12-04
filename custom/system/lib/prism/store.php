<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_prism_store
{

    public function set($key, $value)
    {
        base_kvstore::instance('prism')->store($key,$value,0);

        return true;
    }

    public function get($key, $default = null)
    {
        $value = null;
        base_kvstore::instance('prism')->fetch($key,$value);

        if($value == null)
            return $default;
        else
            return $value;
    }

}


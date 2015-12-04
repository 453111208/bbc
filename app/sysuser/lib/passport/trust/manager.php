<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysuser_passport_trust_manager
{
    public $trustFlags;

    public function __construct()
    {
        $this->trustFlags = config::get('userAuth.trustLogins');
    }

    public function getTrustObjectByFlag($flag)
    {
        $trustClass = 'sysuser_plugin_'.$flag;
        $trustObject = kernel::single($trustClass);
        if ($trustObject instanceof sysuser_plugin_abstract)
        {
            return $trustObject;
        }
        return false;
    }
    
    public function getTrust($flag)
    {
        if (in_array($flag, $this->getTrustFlags()))
        {
            return $this->getTrustObjectByFlag($flag)->getTrustInfo();
        }
    }

    public function getTrusts()
    {
        foreach($this->getTrustFlags() as $flag)
        {
            $trusts[] = $this->getTrustObjectByFlag($flag);
        }
        return $trusts;
    }

    public function getTrustFlags()
    {
        return $this->trustFlags;
    }
}

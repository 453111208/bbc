<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_ctl_default extends topc_controller
{
    public function index()
    {
        if(defined('APP_SITE_INDEX_MAXAGE') && APP_SITE_INDEX_MAXAGE > 1){
            $this->set_max_age(APP_SITE_INDEX_MAXAGE);
        }

        //        throw new Exception();
        $GLOBALS['runtime']['path'][] = array('title'=>app::get('topc')->_('棣栭〉'),'link'=>kernel::base_url(1));
        $this->setLayoutFlag('index');
        return $this->page();
    }
}

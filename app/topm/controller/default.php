<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topm_ctl_default extends topm_controller
{
    public function index()
    {
        if(defined('APP_SITE_INDEX_MAXAGE') && APP_SITE_INDEX_MAXAGE > 1){
            $this->set_max_age(APP_SITE_INDEX_MAXAGE);
        }

        $GLOBALS['runtime']['path'][] = array('title'=>app::get('topm')->_('首页〉'),'link'=>kernel::base_url(1));
        $this->setLayoutFlag('index');
        return $this->page();
    }

    public function switchToPc()
    {
        setcookie('browse', 'pc');
        return redirect::route('topc');
    }
}

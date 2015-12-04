<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_ctl_region extends desktop_controller{

    var $workground = 'desktop_ctl_system';

    function index(){
        return $this->finder('base_mdl_regions');
    }

}

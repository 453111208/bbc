<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

 /**
 * 
 */
 class sysspfb_ctl_admin_reqsuppagree extends desktop_controller
 {
 	public $workground = 'sysspfb.workground.category';
 	
 	function index()
 	{
 		if( $_POST['license'] )
        {
            $this->begin();
            app::get('sysuser')->setConf('sysuser.register.setting_reqsupp_license',$_POST['license']);
            $this->end(true, app::get('sysuser')->_('当前配置修改成功！'));
        }
        $pagedata['license'] = app::get('sysuser')->getConf('sysuser.register.setting_reqsupp_license');
        return $this->page('sysspfb/license.html', $pagedata);
 	}
 }
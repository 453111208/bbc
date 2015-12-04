<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class serveradm_ctl_admin_xhprof extends desktop_controller
{
    var $workground = 'serveradm_ctl_admin_serveradm';
    
    public function index()
    {
        return $this->finder('serveradm_mdl_xhprof',array(
            'title'=>app::get('serveradm')->_("XHProf"),
            'actions'=>array()
        ));
    }
    
    public function intro()
    {
        return $this->page("serveradm/admin/intro.html");
    }
    
    public function doc()
    {
        return $this->page("serveradm/admin/doc.html");
    }
    
    public function show($run_id)
    {
        return "<iframe src='".kernel::base_url(1)."/app/serveradm/vendor/xhprof_html/?run=".$run_id."&source=xhprof' width='100%' height='100%'></iframe>";
    }
}

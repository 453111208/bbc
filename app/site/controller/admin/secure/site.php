<?php

class site_ctl_admin_secure_site extends desktop_controller
{
    public function index() 
    {
        $pagedata['enabled'] = app::get('site')->getConf('site.blacklist.enabled');
        $pagedata['ips'] = join(PHP_EOL ,app::get('site')->getConf('site.blacklist.ips'));
        $pagedata['error_code'] = app::get('site')->getConf('site.blacklist.error_code');
        return $this->page('site/admin/secure/site/index.html', $pagedata);
    }

    public function save() 
    {
        $this->begin();
        $params = $_POST;
        if( isset($params['enabled']) && $params['enabled'] == 1) {
            $ips = explode(PHP_EOL, $params['ips']);
            if(!$params['ips'] || empty($ips)) {
                $this->end(false, app::get('site')->_('ip列表不能为空'));
            }
            app::get('site')->setConf('site.blacklist.enabled', 1);
            app::get('site')->setConf('site.blacklist.ips', $ips);

            if(in_array( $params['error_code'], array('403', '404'))) {
                    app::get('site')->setConf('site.blacklist.error_code', $params['error_code']);
            } else {
                app::get('site')->setConf('desktip.whitelist.error_code', '403');
            }
        } else {
            app::get('site')->setConf('site.blacklist.enabled', 0);
        }
            
        $this->end(true, '设置成功');
    }
}

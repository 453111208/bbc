<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysuser_ctl_admin_trustlogincfg extends desktop_controller{


    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

	/**
	 * 信任登陆finder页 
	 *
	 * @return string
	 */
    function index()
    {
        return $this->finder('sysuser_mdl_trustlogin_cfg', array(
            'title'=>app::get('sysuser')->_('信任登陆配置'),
            'use_buildin_recycle'=>false,
            'use_view_tab'=>true,
            'actions'=>array(
                array(
                    'label'=>app::get('sysuser')->_('开启配置'),
                    'target'=>'dialog::{ title:\''.app::get('sysuser')->_('信任登陆全局配置').'\', width:400, height:200}',
                    'href'=>'?app=sysuser&ctl=admin_trustlogincfg&act=config',
                ),
            )));
    }

    
	/**
	 * 信任登陆开启配置 
	 *
	 * @param string $flag 
	 * @return string
	 */
    public function config()
    {
        $config = app::get('sysuser')->getConf('trustlogin_rule');
        $pagedata['config'] = $config;
        return $this->page('sysuser/trust/config.html', $pagedata);
    }

	/**
	 * 保存信任登陆开启配置
	 *
	 * @param string $flag 
	 * @return string
	 */
    public function saveConfig()
    {
        $post = input::get();
        $config = $post['config'];
        $this->begin();
        app::get('sysuser')->setConf('trustlogin_rule', $config);
        $this->adminlog("信任登录全局状态设置", 1);
        $this->end(true, app::get('sysuser')->_("设置成功！"));
    }

	/**
	 * 信任登陆单个配置
	 *
	 * @param string $flag 
	 * @return string
	 */
    public function setting($flag)
    {
        $trust = kernel::single('sysuser_passport_trust_manager')->getTrustObjectByFlag($flag);
        $setting = $trust->getSetting();
        $pagedata = ['setting' => $setting,
                     'flag' => $flag];
        return $this->page('sysuser/trust/setting.html', $pagedata);
    }

	/**
	 * 保存信任登陆单个配置
	 *
	 * @return null
	 */
    public function saveSetting()
    {
        $this->begin();
        $post = input::get();
        $setting = $post['setting'];
        $flag = $post['flag'];
        $trust = kernel::single('sysuser_passport_trust_manager')->getTrustObjectByFlag($flag);

        $trust->setSetting($setting);
        
        $this->adminlog("信任登录设置[{$flag}]", 1);
        $this->end(true, app::get('sysuser')->_("设置成功！"));
    }

}

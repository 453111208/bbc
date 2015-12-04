<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_ctl_admin_prism extends desktop_controller
{

    public function getConf()
    {
        $pagedata['prismMode']         = config::get('prism.prismMode');
        $pagedata['prismHostUrl']      = config::get('prism.prismHostUrl');
        $pagedata['prismAdminKey']     = config::get('prism.prismAdminKey');
        $pagedata['prismAdminSecret']  = config::get('prism.prismAdminSecret');
        $pagedata['prismUserEmail']    = config::get('prism.prismUserEmail');
        $pagedata['prismUserPassword'] = config::get('prism.prismUserPassword');
        $pagedata['prismUserKey']      = app::get('system')->getConf('prismUserKey');
        $pagedata['prismUserSecret']   = app::get('system')->getConf('prismUserSecret');
        $pagedata['prismAppName']      = config::get('prism.prismAppName');
        $pagedata['prismAppInfo']      = app::get('system')->getConf('prismAppInfo');
        return $this->page('system/admin/prism/getConf.html', $pagedata);
    }


    public function getKeys()
    {
        $keys = app::get('base')->getConf('prismKeys');
        $count = count($keys);
        return $this->page('system/admin/prism/getKeys.html', ['keys' => $keys, 'count'=>$count]);
    }

    public function editKey()
    {
        $keys = app::get('base')->getConf('prismKeys');
        $key = input::get('key');
        $prismKey = $keys[$key];

        return $this->page('system/admin/prism/editKeys.html', ['prismKey' => $prismKey, 'appId'=>$key]);
    }

    public function save()
    {
        $keys = app::get('base')->getConf('prismKeys');

        $appId = input::get('appId');
        $key = input::get('key');
        $secret = input::get('secret');

        $keys[$appId] = ['key'=>$key, 'secret'=>$secret];
        app::get('base')->setConf('prismKeys', $keys);
        $this->adminlog("修改prism的key和secret[appId:{$appId}]", 1);
        $this->splash('success', null, '保存成功');
    }

}

<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topshop_ctl_menu extends topshop_controller {

    public function index()
    {
        $status = 'success';
        $msg = '快捷菜单保存成功';

        $shortcutMenu = input::get('shortcutMenu');

        if( !$shortcutMenu )
        {
            return $this->splash('error',null,'请选择最少五个快捷方式',true);
        }

        if( count($shortcutMenu) > 5 )
        {
            return $this->splash('error',null,'快捷菜单最多选择五个',true);
        }

        $this->setShortcutMenu($shortcutMenu);

        return $this->splash($status,$url,$msg,true);
    }
}


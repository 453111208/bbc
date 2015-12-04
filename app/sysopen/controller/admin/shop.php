<?php
class sysopen_ctl_admin_shop extends desktop_controller{

    public function index()
    {
        return $this->finder('sysopen_mdl_keys',array(
            'use_buildin_filter'=>true,
            'use_view_tab'=>true,
            'title' => app::get('systrade')->_('开发者列表'),
            'use_buildin_delete'=>false,
        ));
    }

    public function _views()
    {
        $mdl_aftersales = app::get('sysopen')->model('keys');
        $sub_menu = array(
            0=>array('label'=>app::get('systrade')->_('全部'),'optional'=>false,'filter'=>array()),
            1=>array('label'=>app::get('systrade')->_('待审核'),'optional'=>false,'filter'=>array('contact_type'=>'applyforopen')),
            2=>array('label'=>app::get('systrade')->_('标准开放'),'optional'=>false,'filter'=>array('contact_type'=>'openstandard')),
            3=>array('label'=>app::get('systrade')->_('禁止'),'optional'=>false,'filter'=>array('contact_type'=>'notallowopen')),
        );
        return $sub_menu;
     }

    public function doApply()
    {
        $openModes = config::get('prism.virtualApp');
        $shopId = $_GET['shop_id'];

        $shopName = app::get('sysshop')->model('shop')->getRow('shop_name,shop_descript', ['shop_id'=>$shopId]);

        $pagedata['shop_name'] = $shopName['shop_name'];
        $pagedata['shop_descript'] = $shopName['shop_descript'];
        $pagedata['modes'] = $openModes;
        $pagedata['shop_id'] = $shopId;
        $this->page('sysopen/admin/doApply.html', $pagedata);
    }

    public function doCreate()
    {
        $this->begin("?app=sysopen&ctl=admin_shop&act=index");
        if($_POST['shop_id'] < 0)
        {
            $this->end(false, 'shop_id不能为空');
        }
        $shopId = $_POST['shop_id'];

        if($_POST['type'] < 0)
        {
            $this->end(false, 'type不能为空');
        }
        $type = $_POST['type'];
        $mark = $_POST['mark'];
        kernel::single('sysopen_key')->create($shopId, $type, $mark);
        $this->adminlog("开通商户开放平台[店铺号:{$shopId}，开放类型:{$type}]", 1);
        $this->end(true);
    }

    public function doDelete()
    {
        $this->begin("?app=sysopen&ctl=admin_shop&act=index");
        if($_GET['shop_id'] <= 0)
        {
            $this->end(false, 'shop_id不能为空');
        }
        $shopId = $_GET['shop_id'];

        kernel::single('sysopen_key')->delete($shopId);
        $this->adminlog("商户开放平台删除商铺key[店铺号:{$shopId}]", 1);
        $this->end(true);
    }

    public function doSuspend()
    {
        $this->begin("?app=sysopen&ctl=admin_shop&act=index");
        if($_GET['shop_id'] < 0)
        {
            $this->end(false, 'shop_id不能为空');
        }

        $shopId = $_GET['shop_id'];
        $mark = $_GET['shop_mark'];

        kernel::single('sysopen_key')->suspend($shopId, $mark);
        $this->adminlog("暂停商户开放平台[店铺号:{$shopId}]", 1);
        $this->end(true);
    }

}

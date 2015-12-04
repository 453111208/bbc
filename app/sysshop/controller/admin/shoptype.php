<?php

/**
 * ShopEx LuckyMall
 *
 * @author     ajx
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysshop_ctl_admin_shoptype extends desktop_controller {

    public $workground = 'sysshop.workground.shoptype';

    /**
     * @brief 构造函数
     *
     * @param $app appid
     *
     * @return null
     */
    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    /**
     * @brief 店铺类型列表方法
     *
     * @return shoptype list
     */
    public function index()
    {
        return $this->finder('sysshop_mdl_shop_type',array(
            'title' => app::get('sysshop')->_('店铺类型列表'),
            'allow_detail_popup' => true,
            'use_buildin_delete' => false,
        ));
    }

    /**
     * @brief 配置类型
     *
     * @param $shoptype_id
     *
     * @return page
     */
    public function edit($shoptype_id)
    {
        $filter = array(
            'shoptype_id'=>$shoptype_id,
        );
        $shoptypeMdl = app::get('sysshop')->model('shop_type');
        $shoptype = $shoptypeMdl->getRow('*',$filter);
        $pagedata['shoptype'] = $shoptype;
        return $this->page('sysshop/admin/shop/edittype.html', $pagedata);
    }

    /**
     * @brief 保存类型配置
     *
     * @return page
     */
    public function saveShoptype()
    {
        try{
            $savedata = $_POST['shoptype'];
            $objShoptype = kernel::single('sysshop_data_shoptype');
            $result = $objShoptype->saveShoptype($savedata);
            $this->adminlog("编辑店铺类型", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("编辑店铺类型", 0);
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        return $this->splash('success',null,$msg);

    }
}

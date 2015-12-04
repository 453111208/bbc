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
     * @brief 企业类型列表方法
     *
     * @return shoptype list
     */
    public function index()
    {
        return $this->finder('sysshop_mdl_shop_type',array(
            'title' => app::get('sysshop')->_('企业类型列表'),
            'allow_detail_popup' => true,
            'use_buildin_delete' => false,
             'actions' => array(
                array(
                    'label'=>app::get('sysshop')->_('添加企业类型'),
                    'href'=>'?app=sysshop&ctl=admin_shoptype&act=addShopType',
                    'target'=>'dialog::{title:\''.app::get('sysshop')->_('添加企业类型').'\',  width:500,height:320}',
                ),
            ),
        ));
    }

        public function addShopType()
    {   

        return $this->page('sysshop/admin/shop/edittype.html');
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
            $savedata["shop_type"]=0;
            $result = $objShoptype->saveShoptype($savedata);
            $this->adminlog("编辑企业类型", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("编辑企业类型", 0);
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        return $this->splash('success',null,$msg);

    }

    public function _views()
    {
         $mdl_all = app::get('sysshop')->model('shop_type');
         $upcat1 = array('use_type' => "1");
         $upcat2 = array('use_type' => "2");
         $upcat3 = array('use_type' => "3");
         $upcat4 = array('use_type' => "4");
         $upcat5 = array('use_type' => "5");
         $all=$mdl_all->count();
         $cat1=$mdl_all->count($upcat1);
         $cat2=$mdl_all->count($upcat2);
         $cat3=$mdl_all->count($upcat3);
         $cat4=$mdl_all->count($upcat4);
         $cat5=$mdl_all->count($upcat5);
         $subMenu = array(
            0=>array(
                'label'=>app::get('sysshop')->_("全部企业类型 ( $all )"),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('sysshop')->_("所属行业 ( $cat1 )"),
                'optional'=>false,
                'filter'=>array(
                    'use_type' => "1",
                ),
            ),
            2=>array(
                'label'=>app::get('sysshop')->_("公司性质 ( $cat2 )"),
                'optional'=>false,
                'filter'=>array(
                    'use_type' => "2",
                ),
            ),
            3=>array(
                'label'=>app::get('sysshop')->_("主要产品 ( $cat3 )"),
                'optional'=>false,
                'filter'=>array(
                    'use_type' => "3",
                ),
            ),
            4=>array(
                'label'=>app::get('sysshop')->_("注册原因 ( $cat4 )"),
                'optional'=>false,
                'filter'=>array(
                    'use_type' => "4",
                ),
            ),
            5=>array(
                'label'=>app::get('sysshop')->_("公司规模 ( $cat5 )"),
                'optional'=>false,
                'filter'=>array(
                    'use_type' => "5",
                ),
            ),
            );
          return $subMenu;
    }

}

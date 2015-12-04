<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysshop_task{

    public function post_install($options)
    {
        kernel::single('base_initial', 'sysshop')->init();
        pamAccount::registerAuthType('sysshop','shop',app::get('sysshop')->_('商家用户系统'));
    }

    public function post_uninstall()
    {
        pamAccount::unregisterAuthType('sysshop');
    }

    public function post_update($dbver)
    {
        if($dbver['dbver'] < 0.4)
        {
            $db = app::get('sysshop')->database();
            $db->executeQuery('INSERT INTO sysshop_account SELECT * FROM pam_seller');
        }

        if($dbver['dbver'] < 0.7 && $dbver['dbver'] > 0.4)
        {
            $objMdlShop = app::get('sysshop')->model('shop');
            $sellerList = $objMdlShop->getList('seller_id,shop_id');
            $objMdlSeller = app::get('sysshop')->model('seller');
            foreach ($sellerList as $v)
            {
                if($v['seller_id'])
                {
                    $objMdlSeller->update( array('shop_id'=>$v['shop_id']), array('seller_id'=>$v['seller_id']));
                }
            }
        }

        if($dbver['dbver'] < 0.8 )
        {
            kernel::single('base_initial', 'sysshop')->init();
        }
    }

}


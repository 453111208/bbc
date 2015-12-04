<?php
/**
 * @brief 商家账号
 */
class sysshop_ctl_admin_seller extends desktop_controller {

    /**
     * @brief  商家账号列表
     *
     * @return
     */
    public function index()
    {
        return $this->finder('sysshop_mdl_seller',array(
            'use_buildin_delete' => false,
            'title' => app::get('sysshop')->_('商家账号列表'),
            'actions'=>array(
                /*
                 * 暂时注释此处，遗留后用
                 array(
                     'label'=>'发送邮件短信',
                     'submit'=>'?app=sysshop&ctl=admin_seller&act=messenger',
                 ),
                 */
                array(
                    'label'=>app::get('sysshop')->_('添加自营用户'),
                    'href'=>'?app=sysshop&ctl=admin_seller&act=addSelfUser',
                    'target'=>'dialog::{title:\''.app::get('sysshop')->_('添加自营用户').'\',  width:500,height:320}',
                ),
            ),
        ));
    }

    //添加自营用户
    public function addSelfUser()
    {
        $objShop = kernel::single('sysshop_data_shop');
        $shopList = $objShop->fetchListShopInfo('shop_id,shop_name',array('shop_type'=>'self'));
        $pagedata['shopList'] = $shopList;
        $this->contentHeaderTitle = '添加自营用户';
        return view::make('sysshop/admin/seller/addSelfUser.html',$pagedata);
    }

    public function saveSelfUser()
    {
        $postdata = utils::_filter_input(input::get('seller'));
        $objSeller = kernel::single('sysshop_data_seller');
        try
        {
            $this->adminlog("添加自营用户[{$postdata['login_account']}]", 1);
           $objSeller->saveSelf($postdata);
        }
        catch(Exception $e)
        {
            $this->adminlog("添加自营用户[{$postdata['login_account']}]", 0);
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        return $this->splash('success',null,"自营用户添加成功");
    }

    /**
     * @brief  商家密码修改
     *
     * @return
     */
    public function sysshopUpdatePwd($seller_id)
    {
        $paminfo = app::get('sysshop')->model('account')->getRow('*',array('seller_id'=>$seller_id));
        $sysinfo['login_account'] = $paminfo['login_account'];
        $sysinfo['seller_id'] = $paminfo['seller_id'];
        $pagedata['data'] = $sysinfo;
        return $this->page('sysshop/admin/seller/updatepwd.html', $pagedata);
    }
    /**
     * @brief  商家密码执行
     *
     * @return
     */
    public function update_shop_pwd()
    {
        try
        {
            $seller_id = $_POST['seller_id'];
            $data = $_POST;
            shopAuth::resetPwd($seller_id,$data);
            $this->adminlog("修改商家密码[$seller_id:{$seller_id}]", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("修改商家密码[$seller_id:{$seller_id}]", 0);
            $msg = $e->getMessage();

            return $this->splash('error',null,$msg);
        }

        $msg = app::get('sysshop')->_('修改成功');

        return $this->splash('success',null,$msg);
    }

}



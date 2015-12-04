<?php

/**
 * @brief 店铺
 */
class sysshop_ctl_admin_shopnotice extends desktop_controller{

    /**
     * @brief  商家通知类型
     *
     * @return
     */
    public function index()
    {
        return $this->finder('sysshop_mdl_shop_notice',array(
                'title'=>app::get('sysshop')->_('商家通知列表'),
                'use_buildin_recycle'=>false,
                'use_view_tab'=>true,
                'actions'=>array(
                    array(
                        'label'=>app::get('sysshop')->_('添加全站通知'),
                        'target'=>'dialog::{ title:\''.app::get('sysshop')->_('添加全站通知').'\', width:800, height:400}',
                        'href'=>'?app=sysshop&ctl=admin_shopnotice&act=addNotice',
                    ),
                    array(
                        'label'=>app::get('sysshop')->_('商家通知类型'),
                        'target'=>'dialog::{ title:\''.app::get('sysshop')->_('商家通知类型').'\', width:400, height:200}',
                        'href'=>'?app=sysshop&ctl=admin_shopnotice&act=addNoticeType',
                    ),
                ),
            )
        );
    }
    //添加商家通知类型
    public function addNoticeType()
    {
        $notice = app::get('sysshop')->getConf('shopnoticetype');
        $this->contentHeaderTitle = '添加通知类型';
        $pagedata['notice'] = $notice;
        $pagedata['count'] = count($notice);
        //echo '<pre>';print_r($pagedata);exit();
        return view::make('sysshop/admin/shop/addNoticeType.html',$pagedata);
    }
    //保存商家通知类型
    public function saveNoticeType()
    {
        $postdata = input::get('notice');
        foreach ($postdata as $key => $value)
        {
            if($value=='')
            {
                $msg = app::get('sysshop')->_('通知类型不能为空!');
                return $this->splash('error',null,$msg);
            }
        }
        $noticetype = app::get('sysshop')->getConf('shopnoticetype');
        if( count($noticetype) > count($postdata) )
        {
            $minustype = array_diff($noticetype,$postdata);
            $shopNoticeMdl = app::get('sysshop')->model('shop_notice');
            $minusTypeList = $shopNoticeMdl->getList('notice_id',array('notice_type'=>$minustype));
            
            if($minusTypeList)
            {
                $msg = app::get('sysshop')->_('该类型下面含有通知，请先删除通知再进行操作!');
                return $this->splash('error',null,$msg);
            }
            //echo '<pre>';print_r($minusTypeList);exit();
        }
        $result = app::get('sysshop')->setConf('shopnoticetype',$postdata);
        if($result)
        {
            $msg = app::get('sysshop')->_('商家通知类型添加成功!');
            return $this->splash('success',null,$msg);
        }
        else
        {
            $msg = app::get('sysshop')->_('商家通知类型添加失败!');
            return $this->splash('error',null,$msg);
        }
    }

    //添加全站通知
    public function addNotice()
    {
        $shopId = input::get('shop_id');
        if($shopId!='')
        {
            $notice['shop_id'] = $shopId;
            $pagedata['notice'] = $notice;
        }
        $notice = app::get('sysshop')->getConf('shopnoticetype');
        $this->contentHeaderTitle = '添加全站通知';
        $pagedata['noticetype'] = $notice;
        $pagedata['count'] = count($notice);
        return view::make('sysshop/admin/shop/addNotice.html',$pagedata);
    }
     //编辑全站通知
    public function noticeEdit()
    {
        $noticeId = input::get('notice_id');
        try
        {
            $params['notice_id'] = $noticeId;
            $noticeInfo = app::get('sysshop')->rpcCall('shop.get.shopnoticeinfo',$params);
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        $notice = app::get('sysshop')->getConf('shopnoticetype');
        $pagedata['noticetype'] = $notice;
        $pagedata['count'] = count($notice);
        $pagedata['notice'] = $noticeInfo;
        $this->contentHeaderTitle = '编辑全站通知';
        return view::make('sysshop/admin/shop/addNotice.html',$pagedata);
    }


    //保存全站通知
    public function saveNotice()
    {
        $params = input::get('notice');
        try
        {
            app::get('sysshop')->rpcCall('shop.savenotice',$params);
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        $url = "?app=sysshop&ctl=admin_shopnotice&act=index";
        return $this->splash('success',$url,"全站通知户添加成功");
    }

}
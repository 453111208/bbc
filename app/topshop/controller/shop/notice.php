<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topshop_ctl_shop_notice extends topshop_controller
{
    //获取商家通知
    public function index()
    {
        $pageSize = 20;
        $filter = input::get();
        $notice = app::get('sysshop')->getConf('shopnoticetype');
        //echo '<pre>';print_r($notice);exit();
        $pagedata['notice'] = $notice;
        $pagedata['notice_type'] = $filter['notice_type'];
        if(!$filter['pages'])
        {
            $filter['pages'] = 1;
        }
        $params = array(
            'shop_id'   => $this->shopId,
            'page_no'   => $pageSize*($filter['pages']-1),
            'page_size' => $pageSize,
            'fields'    =>'notice_title,notice_type,createtime,notice_id,shop_id',
            'notice_type'  =>$filter['notice_type'],
        );
        $noticeData = app::get('topshop')->rpcCall('shop.get.shopnoticelist',$params);
        $count = $noticeData['noticecount'];
        $noticeList = $noticeData['noticeList'];

        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topshop_ctl_shop_notice@index',$filter),
            'current'=>$current,
            'use_app' => 'topshop',
            'total'=>$total,
            'token'=>$filter['pages'],
        );
        $pagedata['noticeList']= $noticeList;
        $pagedata['total'] = $count;

        $this->contentHeaderTitle = app::get('topshop')->_('店铺通知');
        return $this->page('topshop/shop/notice.html', $pagedata);
    }

    public function noticeInfo()
    {
        $noticeId = input::get('notice_id');
        try
        {
            $params['notice_id'] = $noticeId;
            $noticeInfo = app::get('topshop')->rpcCall('shop.get.shopnoticeinfo',$params);
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        $pagedata['noticeInfo'] = $noticeInfo;
        //echo '<pre>';print_r($pagedata);exit();
        $this->contentHeaderTitle = app::get('topshop')->_('店铺通知');
        return $this->page('topshop/shop/noticeinfo.html', $pagedata);
    }
}
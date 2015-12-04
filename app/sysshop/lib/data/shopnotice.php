<?php
class sysshop_data_shopnotice{
    //保存商家通知保
    public function saveShopNotice($postdata)
    {
        $shopNoticeMdl = app::get('sysshop')->model('shop_notice');
        if($postdata['shop_id']=='')
        {
            $postdata['shop_id'] = '0';
        }
        if($postdata['notice_id']!='')
        {
            $postdata['modified_time'] = time();
        }
        else
        {
            $postdata['createtime'] = time();
        }
        $adminId = pamAccount::getAccountId();
        $postdata['admin_id'] = $adminId;
        $result = $shopNoticeMdl->save($postdata);
        if(!$result)
        {
            throw new \LogicException("商家通知保存失败!");
        }
        return true;
    }

    public function getNoticeInfo($params)
    {
        $shopNoticeMdl = app::get('sysshop')->model('shop_notice');
        if($params['notice_id']=='')
        {
            throw new \LogicException("商家通知id不能为空!");
        }

        $noticeInfo = $shopNoticeMdl->getRow($params['fields'],array('notice_id'=>$params['notice_id']));

        return $noticeInfo;
    }

    public function getNoticeList($params)
    {
        $shopNoticeMdl = app::get('sysshop')->model('shop_notice');
        if($params['fields']=='')
        {
            $params['fields'] = '*';
        }
        $filter = array('shop_id'=>$params['shop_id'],'notice_type'=>$params['notice_type']);
        $orderBy    = $params['orderBy'] ? $params['orderBy'] : 'createtime DESC';

        $aData = $shopNoticeMdl->getList($params['fields'], $filter,$params['page_no'],$params['page_size'], $orderBy);
        $noticeCount = $shopNoticeMdl->count($filter);
        $noticeData = array(
                'noticeList'  => $aData,
                'noticecount' => $noticeCount,
            );
        //echo '<pre>';print_r($noticeData);exit();
        return $noticeData;
    }

}


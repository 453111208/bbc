<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysrate_api_info {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取单条评论详情';

    public function getParams()
    {
        $return['params'] = array(
            'role' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'调用角色'],
            'rate_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户要操作的评价ID'],
            'fields'=> ['type'=>'field_list','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'需要返回的字段'],
        );

        //如果参数fields中存在orders，则表示需要获取子订单的数据结构
        $return['extendsFields'] = ['appeal'];

        return $return;
    }

    public function getData($params)
    {
        $filter['rate_id'] = $params['rate_id'];
        $accountId = $params['oauth']['account_id'];
        if( $params['role'] == 'buyer' )
        {
            $filter['user_id'] = $accountId;
        }
        elseif( $params['role'] == 'seller' )
        {
            $shopId = app::get('sysrate')->rpcCall('shop.get.loginId',array('seller_id'=>$accountId),'seller');
            $filter['shop_id'] = $shopId;
        }
        $data = app::get('sysrate')->model('traderate')->getRow($params['fields']['rows'], $filter);
        if( isset($params['fields']['extends']['appeal']) && $data['appeal_status'] != 'NO_APPEAL' )
        {
            $data['appeal'] = app::get('sysrate')->model('appeal')->getRow($params['fields']['extends']['appeal'], ['rate_id'=>$params['rate_id']]);
        }

        return $data;
    }
}


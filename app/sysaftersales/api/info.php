<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取单条售后申请数据
 */
class sysaftersales_api_info {

     /**
     * 接口作用说明
     */
    public $apiDescription = '获取单个售后详情(根据售后单、店铺id、会员id)';

    public function getParams()
    {
        $return['params'] = array(
            'aftersales_bn' => ['type'=>'int','valid'=>'required', 'description'=>'申请售后编号'],
            'shop_id' => ['type'=>'int','valid'=>'', 'description'=>'售后单所属店铺的店铺id'],
            'user_id' => ['type'=>'int','valid'=>'', 'description'=>'售后单所属用户的用户id'],
            'fields'=> ['type'=>'field_list','valid'=>'required', 'description'=>'获取单条售后需要返回的字段'],
        );

        $return['extendsFields'] = ['trade','sku'];

        return $return;
    }

    /**
     * 获取单条申请售后服务信息
     */
    public function getData($params)
    {
        $filter['aftersales_bn'] = $params['aftersales_bn'];
        if($params['shop_id'])
        {
            $filter['shop_id'] = $params['shop_id'];
        }
        if($params['user_id'])
        {
            $filter['user_id'] = $params['user_id'];
        }
        return kernel::single('sysaftersales_data')->getAftersalesInfo($params['fields'], $filter);
    }

}


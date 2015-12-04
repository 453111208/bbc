<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取单条售后申请数据
 */
class sysaftersales_api_infobn {

     /**
     * 接口作用说明
     */
    public $apiDescription = '获取单个售后详情(根据子订单号)';

    public function getParams()
    {
        $return['params'] = array(
            'oid' => ['type'=>'int','valid'=>'required', 'description'=>'申请售后的订单号'],
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
        return kernel::single('sysaftersales_data')->ByOidgetAftersalesInfo($params['fields'], $params['oid']);
    }

}


<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysaftersales_api_verify {

    /**
     * 接口作用说明
     */
    public $apiDescription = '根据子订单编号，验证该子订单号是否可以申请售后服务(未验证订单状态)';

    public function getParams()
    {
        $return['params'] = array(
            'oid' => ['type'=>'string','valid'=>'required', 'description'=>'需要验证的子订单号'],
        );

        return $return;
    }

    public function verify($params)
    {
        $oids = explode(',',$params['oid']);
        $result = kernel::single('sysaftersales_verify')->isAftersales($oids);
        return $result;
    }

}


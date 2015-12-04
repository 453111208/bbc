<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysrate_api_dsr_get {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取单条评论详情';

    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户要操作的评价ID'],
            'catDsrDiff'=> ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'是否需要返回行业平均分同比'],
            'countNum'=> ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'是否需要返回行店铺每个动态评分的数量'],
        );

        return $return;
    }

    public function getData($params)
    {
        return kernel::single('sysrate_dsr')->getShopDsr($params['shop_id'], $params['catDsrDiff'], $params['countNum']);
    }
}


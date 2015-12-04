<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysrate_api_appeal_add {

    /**
     * 接口作用说明
     */
    public $apiDescription = '商家对评论进行申诉';

    public function getParams()
    {
        $return['params'] = array(
            'rate_id' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'需要申诉评论ID'],
            'is_again'=> ['type'=>'bool','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'是否为再次申诉，true再次申诉，false首次申诉'],
            'appeal_type' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'申诉类型(首次申诉必填)，APPLY_DELETE 申请删除评论;APPLY_UPDATE 申请修改评论'],
            'content' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'申诉内容'],
            'evidence_pic' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'申诉图片凭证'],
        );

        return $return;
    }

    public function add($params)
    {
        if($params['oauth']['auth_type'] && $params['oauth']['auth_type'] == "shop")
        {
            $sellerId = $params['oauth']['account_id'];
            $shopId = app::get('sysrate')->rpcCall('shop.get.loginId',array('seller_id'=>$sellerId),'seller');
        }
        return kernel::single('sysrate_appeal')->add($params,$shopId);
    }

}


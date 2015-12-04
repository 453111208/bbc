<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysrate_api_reply {

    /**
     * 接口作用说明
     */
    public $apiDescription = '商家解释，回复评论';

    public function getParams()
    {
        $return['params'] = array(
            'rate_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'商家要回复的评论ID'],
            'reply_content' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'商家回复的内容'],
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
        $flag = kernel::single('sysrate_traderate')->reply($params['rate_id'], $params['reply_content'],$shopId);
        return $flag ? true : false;
    }

}


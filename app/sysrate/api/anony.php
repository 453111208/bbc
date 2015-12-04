<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysrate_api_anony {

    /**
     * 接口作用说明
     */
    public $apiDescription = '将评论的实名修改为匿名，但是修改为匿名之后则不能再次修改为实名';

    public function getParams()
    {
        $return['params'] = array(
            'rate_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户要操作的评价ID'],
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'1', 'description'=>'用户ID'],
        );

        return $return;
    }

    public function set($params)
    {
        return kernel::single('sysrate_traderate')->setAnony($params['rate_id'],$params['user_id']);
    }

}


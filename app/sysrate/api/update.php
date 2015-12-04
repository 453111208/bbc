<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysrate_api_update {

    /**
     * 接口作用说明
     */
    public $apiDescription = '商家申诉修改评价成功，用户有7天的修改时限';

    public function getParams()
    {
        $return['params'] = array(
            'rate_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户要操作的评价ID'],

            //单个子订单评论需要的参数
            'result' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'评价结果,good 好评 neutral 中评 bad 差评'],
            'content' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'评价内容'],
            'rate_pic' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'晒单图片'],
            //'anony' => ['type'=>'int','required'=>0, 'description'=>'是否匿名'],
        );

        return $return;
    }

    public function update($params)
    {
        $data['result'] = $params['result'];
        $data['content'] = $params['content'];
        $data['rate_pic'] = $params['rate_pic'];
        if($params['oauth'])
        {
            $data['user_id'] = $params['oauth']['account_id'];
        }
        if(!$data['user_id'])
        {
            throw new \LogicException(app::get('sysrate')->_('登录信息有误'));
        }

        //$data['anony'] = $params['anony'];
        return kernel::single('sysrate_traderate')->update($params['rate_id'], $data);
    }

}


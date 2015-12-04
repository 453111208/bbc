<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysrate_api_add {

    /**
     * 接口作用说明
     */
    public $apiDescription = '对已完成的订单新增商品评论，店铺评分';

    public function getParams()
    {
        $return['params'] = array(
            'tid' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'新增评论的订单ID'],

            'rate_data' => ['type'=>'json', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'对子订单评论的参数'],
            //单个子订单评论需要的参数
            #'oid' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'新增评论的子订单号'],
            #'result' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'评价结果,good 好评 neutral 中评 bad 差评'],
            #'content' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'评价内容'],
            #'rate_pic' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'晒单图片'],
            #'anony' => ['type'=>'int','required'=>0, 'description'=>'是否匿名'],

            //店铺动态评分参数
            'tally_score' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'商品与描述相符'],
            'attitude_score' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'服务态度评分'],
            'delivery_speed_score' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'发货速度评分'],
            'logistics_service_score' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'物流公司服务评分'],
        );

        return $return;
    }

    public function add($params)
    {
        if($params['oauth'])
        {
            $params['user_id'] = $params['oauth']['account_id'];
            unset($params['oauth']);
        }
        if(!$params['user_id'])
        {
            throw new \LogicException(app::get('sysrate')->_('登录信息有误'));
        }
        $params['rate_data'] = json_decode($params['rate_data'],true);
        $db = app::get('sysrate')->database();
        $db->beginTransaction();
        try
        {
            if( !kernel::single('sysrate_traderate')->add($params) )
            {
                throw new \LogicException(app::get('sysrate')->_('评价添加失败'));
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        return true;
    }
}


<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysrate_api_addFeedback {

    /**
     * 接口作用说明
     */
    public $apiDescription = '商家对平台进行意见反馈';

    public function getParams()
    {
        $return['params'] = array(
            'name' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'张三', 'description'=>'提交意见的姓名'],
            'email' => ['type'=>'string', 'valid'=>'required|email', 'default'=>'', 'example'=>'example@shopex.cn', 'description'=>'平台处理后告知结果的邮箱'],
            'tel' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'021-68100100', 'description'=>'联系的电话或者手机号码'],
            'question' => ['type'=>'string','valid'=>'required|min:10|max:300', 'default'=>'', 'example'=>'', 'description'=>'反馈问题的详细描述'],
        );
        return $return;
    }

    public function doSave($params)
    {
        $objMdlFeedback = app::get('sysrate')->model('feedback');

        $data['name'] = $params['name'];
        $data['email'] = $params['email'];
        $data['tel'] = $params['tel'];
        $data['question'] = $params['question'];

        if( $params['oauth']['auth_type'] == 'shop' )
        {
            $data['seller_id'] = $params['oauth']['account_id'];
        }

        if( !$data['seller_id'] )
        {
            throw new \LogicException('无操作权限，请重新登录');
        }

        $data['shop_id'] = app::get('sysrate')->rpcCall('shop.get.loginId',array('seller_id'=>$data['seller_id']),'seller');

        try
        {
            //检查数据安全
            $data = utils::_filter_input($data);

            $objMdlFeedback->save($data);
        }
        catch (Exception $e)
        {
            throw new \LogicException($e->getMessage());
        }

        return true;
    }
}



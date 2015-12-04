<?php
class sysuser_api_couponUseLog {

    /**
     * 接口作用说明
     */
    public $apiDescription = '修改优惠券使用信息';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'coupon_code' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'优惠券编码','default'=>'','example'=>''],
            'tid' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'订单id','default'=>'','example'=>''],
        );

        return $return;
    }

    public function couponUseLog($apiData)
    {
        $data['tid'] = $apiData['tid'];
        $data['is_valid'] = '0';

        $filter['user_id'] = pamAccount::getAccountId();
        $filter['coupon_code'] = $apiData['coupon_code'];
        return app::get('sysuser')->model('user_coupon')->update($data, $filter);
    }
}

<?php
class sysuser_api_point_count {

    /**
     * 接口作用说明
     */
    public $apiDescription = '下订单计算积分';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'money' => ['type'=>'money','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'订单商品总额'],
        );

        return $return;
    }

    public function count($params)
    {
        // 取到商店积分规则,目前得到的积分就取商品金额等值数值(向上取整)
        $pointRate = app::get('sysconf')->getConf('point.ratio');
        $pointRate = intval($pointRate) ? intval($pointRate) : 1;
        $subtotal_obtain_point = ceil($params['money'] * $pointRate);
        return $subtotal_obtain_point;
    }
}

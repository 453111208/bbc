<?php
class systrade_api_cart_deleteCart{
    public $apiDescription = "删除购物车数据";
    public function getParams()
    {
        $return['params'] = array(
            'cart_id' => ['type'=>'string','valid'=>'','description'=>'购物车id,多个数据用逗号隔开','default'=>'','example'=>'33,44,12,3'],
            'user_id' => ['type'=>'string','valid'=>'','description'=>'用户id','default'=>'','example'=>''],
            'mode' => ['type'=>'string','valid'=>'','description'=>'是否是立即购买','default'=>'','example'=>''],
        );
        return $return;
    }
    public function deleteCart($params)
    {
        if($params['cart_id'])
        {
            $params['cart_id'] = explode(',',$params['cart_id']);
        }
        if($params['user_id'])
        {
            $params['user_id'] = $params['user_id'];
        }
        if($params['mode'])
        {
            $mode = $params['mode'];
        }
        unset($params['mode'],$params['oauth']);
        $res = kernel::single('systrade_data_cart', $params['user_id'])->removeCart($params,$mode);
        return $res;
    }
}

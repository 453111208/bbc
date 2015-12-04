<?php
class systrade_api_cart_updateCart{
    public $apiDescription = "更新购物车数据";
    public function getParams()
    {
        $return['params'] = array(
            'cart_id' => ['type'=>'int','valid'=>'','description'=>'购物车id,多个数据用逗号隔开','default'=>'','example'=>''],
            'user_id' => ['type'=>'int','valid'=>'','description'=>'用户id','default'=>'','example'=>''],
            'totalQuantity' => ['type'=>'int','valid'=>'','description'=>'数量','default'=>'','example'=>''],
            'obj_type' => ['type'=>'string','valid'=>'','description'=>'数据类型','default'=>'','example'=>''],
            'selected_promotion' => ['type'=>'string','valid'=>'','description'=>'购物车选中的促销id','default'=>'','example'=>''],
            'mode' => ['type'=>'string','valid'=>'','description'=>'是否立即购买','default'=>'','example'=>''],
            'is_checked' => ['type'=>'bool','valid'=>'','description'=>'是否被选中','default'=>'','example'=>''],
        );
        return $return;
    }
    public function updateCart($params)
    {
        $user_id = $params['user_id'];
        $data = kernel::single('systrade_data_cart', $user_id)->updateCart($params);
        return $data;
    }
}

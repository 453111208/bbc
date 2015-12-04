<?php
class systrade_api_cart_addCart{
    public $apiDescription = "加入购物车";
    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required','description'=>'会员id','default'=>'','example'=>'3'],
            'quantity' => ['type'=>'string','valid'=>'required','description'=>'商品数量','default'=>'','example'=>'3'],
            'sku_id' => ['type'=>'string','valid'=>'required','description'=>'货品id','default'=>'','example'=>'3'],
            'obj_type' =>['type'=>'string','valid'=>'required','description'=>'对象类型','default'=>'','example'=>'item'],
            'mode' => ['type'=>'string','valid'=>'required','description'=>'购物车类型','default'=>'','example'=>'cart'],
            'goodsType' => ['type'=>'string','valid'=>'','description'=>'商品类型','default'=>'','example'=>''],
        );
        return $return;
    }
    public function addCart($params)
    {
        $user_id = $params['user_id'];
        $data = kernel::single('systrade_data_cart', $user_id)->addCart($params);
        return $data;
    }
}

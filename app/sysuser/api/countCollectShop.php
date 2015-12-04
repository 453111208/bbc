<?php
class sysuser_api_countCollectShop{
    public $apiDescription = "获取会员店铺收藏总数";
    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户ID必填','default'=>'','example'=>''],
        );
        return $return;
    }
    public function getCount($params)
    {
        $filter['user_id'] = $params['user_id'];
		$objMdlShopFav = app::get('sysuser')->model('shop_fav');
        $result = $objMdlShopFav->count($filter);
        return $result;
    }
}

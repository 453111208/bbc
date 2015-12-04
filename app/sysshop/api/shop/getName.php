<?php
class sysshop_api_shop_getName{
    public $apiDescription = "根据店铺id获取店铺名称(带后缀)";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'string','valid'=>'required','description'=>'店铺id串','default'=>'','example'=>'1,3,5'],
        );
        return $return;
    }
    public function getList($params)
    {
        $filter['shop_id'] = explode(',',$params['shop_id']);
        $objMdlShop = app::get('sysshop')->model('shop');
        $objMdlshopType = app::get('sysshop')->model('shop_type');
        $shopdata = $objMdlShop->getList('shop_id,shop_name,shop_type',$filter);

        $type = $objMdlshopType->getList('suffix,shop_type');
        $type = array_bind_key($type, 'shop_type');
        foreach($shopdata as $value)
        {
            $shopName[$value['shop_id']] = $value['shop_name'].$type[$value['shop_type']]['suffix'];
        }
        return $shopName;
    }
}

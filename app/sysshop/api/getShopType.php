<?php
class sysshop_api_getShopType{
    public $apiDescription = "获取所有的店铺类型列表";
    public function getParams()
    {
        $return['params'] = array(
            'seller_id' => ['type'=>'int','valid'=>'','description'=>'店铺类型','default'=>'当前登录的商家','example'=>'1'],
            'is_display' => ['type'=>'int','valid'=>'','description'=>'是否显示给商家','default'=>'当前登录的商家','example'=>'1'],
        );
        return $return;
    }
    public function getList($params)
    {
        $filter = array();
        if($params['shop_type'])
        {
            $filter['shop_type'] = explode(',',$params['shop_type']);
        }

        if($params['is_display'])
        {
            $filter['is_display'] = 1;
        }

        $objMdlShoptype = app::get('sysshop')->model('shop_type');
        $rows = "shop_type,name,brief,suffix";
        $lists = $objMdlShoptype->getList($rows,$filter);
        foreach($lists as $key=>$value)
        {
            $shoptype[$value['shop_type']] = $value;
        }
        return $shoptype;
    }
}

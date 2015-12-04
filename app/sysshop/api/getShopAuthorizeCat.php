<?php
class sysshop_api_getShopAuthorizeCat{
    public $apiDescription = "获取店铺签约的类目";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
            'fields' => ['type'=>'field_list','valid'=>'','description'=>'要获取店铺签约的类目字段集','default'=>'cat_id,cat_name','example'=>'cat_id,cat_name'],
        );
        return $return;
    }

    public function getAuthorizeCat($params)
    {
        $filter['shop_id'] = $params['shop_id'];
        $row = $params['fields'] ? $params['fields'] : "cat_id,cat_name";

        $objMdlShopCat = app::get('sysshop')->model('shop_rel_lv1cat');
        $objMdlShop = app::get('sysshop')->model('shop');

        $shopType = $objMdlShop->getRow('shop_type',$filter);
        $relCats = $objMdlShopCat->getList('cat_id,shop_id',$filter);

        $catIds = array_column($relCats,'cat_id');

        if(!$catIds[0] && $shopType['shop_type'] == "self")
        {
            $newParams['fields'] = $row;
            $dataCat = app::get('sysshop')->rpcCall('category.cat.get.list',$newParams);
        }
        else
        {
            $newParams['fields'] = $row;
            $newParams['cat_id'] = implode(',',$catIds);
            $dataCat = app::get('sysshop')->rpcCall('category.cat.get',$newParams);
        }
        return $dataCat;
    }
}



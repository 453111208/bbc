<?php
class sysshop_api_shopAuthorize{

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取商家签约的类目和品牌信息(id集)和店铺类型';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
            'fields' => ['type'=>'field_list','valid'=>'','description'=>'要获取店铺签约的类目和品牌字段集','default'=>'cat.cat_id,cat,cat_name,brand.brand_id,brand.brand_name','example'=>'cat.cat_id,cat,cat_name,brand.brand_id,brand.brand_name'],
        );
        $return['extendsFields'] = ['cat','brand'];
        return $return;
    }

    public function getCatBrand($params)
    {
        $filter['shop_id'] = $params['shop_id'];
        $catRows = $params['fields']['extends']['cat'];
        $brandRows = $params['fields']['extends']['brand'];
        $catRows = $catRows ? $catRows : "cat_id";
        $brandRows = $brandRows ? $brandRows : "brand_id";

        $objMdlShopCat = app::get('sysshop')->model('shop_rel_lv1cat');
        $objMdlShop = app::get('sysshop')->model('shop');
        $objMdlShopBrand = app::get('sysshop')->model('shop_rel_brand');

        $result[$filter['shop_id']] = $objMdlShop->getRow('shop_type',$filter);
        $cats = $objMdlShopCat->getList($catRows,$filter);
        foreach($cats as $cat)
        {
            $result[$filter['shop_id']]['cat'][] = $cat['cat_id'];
        }

        $brandIds = $objMdlShopBrand->getList($brandRows,$filter);
        if($brandIds)
        {
            foreach($brandIds as $brandId)
            {
                $result[$filter['shop_id']]['brand'][] = $brandId['brand_id'];
            }
        }
        return $result;
    }
}

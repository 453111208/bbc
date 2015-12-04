<?php
class syscategory_api_getCatRelBrand{
    public $apiDescription = "获取类目关联的品牌";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'string','valid'=>'', 'description'=>'签约店铺id','example'=>'3','default'=>''],
            'cat_id' => ['type'=>'int','valid'=>'required','description'=>'类目id','default'=>'','example'=>'1'],
            'fields' => ['type'=>'fields_list','valid'=>'','description'=>'品牌字段集','default'=>'','example'=>'brand_id,brand_name'],
        );
        return $return;
    }

    public function getData($params)
    {
        $catId = intval($params['cat_id']);
        $shopId = $params['shop_id'];
        $row = $params['fields'];

        if(!$catId)
        {
            throw new \LogicException(app::get('syscategory')->_('参数cat_id有误'));
        }

        $objMdlCat = app::get('syscategory')->model('cat');
        $cat = $objMdlCat->getRow('cat_id,level,is_leaf,cat_path',array('cat_id'=>$catId));
        if(!$cat || !$cat['level'])
        {
            throw new \LogicException(app::get('syscategory')->_('参数类目cat_id='.$catId.'不存在'));
        }

        if($shopId)
        {
            $shopAuthorize = app::get('syscategory')->rpcCall('shop.authorize.catbrandids.get',array('shop_id'=>$shopId));
            if(!isset($shopAuthorize[$shopId])){
                throw new \LogicException(app::get('syscategory')->_('不存在该店铺shop_id='.$shopId));
            }
            $authorize = $shopAuthorize[$shopId];
            $shopType = $authorize['shop_type'];
            $arrPath =array_filter(explode(',',$cat['cat_path']));

            //如果有签约的品牌，直接返回签约品牌
            if($authorize['brand'])
            {
                $brandIds = $authorize['brand'];
                $brands = $this->__getBrand($brandIds,$row);
                return $brands;
            }

            //如果没有签约品牌，获取签约的类目的品牌
            if($cat['level'] == 3 && (in_array($arrPath[1],$authorize['cat']) || $shopType == "self"))
            {
                $relBrand = $this->__getRelBrand($catId);
                if(!$relBrand)
                {
                    throw new \LogicException(app::get('syscategory')->_('该类目下无关联品牌'));
                }
                $brandIds = array_column($relBrand,'brand_id');
                $brands = $this->__getBrand($brandIds,$row);
                return $brands;
            }
            else
            {
                throw new \LogicException(app::get('syscategory')->_('不存在shop_id='.$shopId.'的店铺'));
            }
        }

        $lv3CatIds = $this->__getLv3CatId($catId,$cat['level']);
        if(!$lv3CatIds)
        {
            throw new \LogicException(app::get('syscategory')->_('参数cat_id='.$catId.'有误'));
        }

        $relBrand = $this->__getRelBrand($lv3CatIds);
        if(!$relBrand)
        {
            throw new \LogicException(app::get('syscategory')->_('该类目下无关联品牌'));
        }

        $brandIds = array_column($relBrand,'brand_id');
        $brands = $this->__getBrand($brandIds,$row);
        return $brands;
    }

    //获取三级类目的ids
    private function __getLv3CatId($catId,$lv=3)
    {
        $objMdlCat = app::get('syscategory')->model('cat');
        switch($lv)
        {
            case "1":
                $lv2Ids = $objMdlCat->getList('cat_id',array('parent_id'=>$catId));
                if(!$lv2Ids) return false;
                $lv2cids = array_column($lv2Ids,'cat_id');

                $lv3Ids = $objMdlCat->getList('cat_id',array('parent_id'=>$lv2cids));
                $lv3Ids = array_column($lv3Ids,'cat_id');
                break;

            case "2":
                $lv3Ids = $objMdlCat->getList('cat_id',array('parent_id'=>$catId));
                $lv3Ids = array_column($lv3Ids,'cat_id');
                break;

            case "3":
                $lv3Ids = $catId;
                break;
        }
        return $lv3Ids;
    }

    //获取三级类目关联的品牌
    private function __getRelBrand($catIds)
    {
        $filter['cat_id'] = $catIds;
        $objMdlRelBrand = app::get('syscategory')->model('cat_rel_brand');
        $datas = $objMdlRelBrand->getList('brand_id,cat_id',$filter);
        return $datas;
    }

    //获取指定品牌信息
    private function __getbrand($brandIds,$row="")
    {
        if(!$row)
        {
            $row = "brand_id,brand_name";
        }
        $filter['brand_id'] = $brandIds;
        $objMdlBrand = app::get('syscategory')->model('brand');
        $brands = $objMdlBrand->getList($row,$filter);
        return $brands;
    }
}

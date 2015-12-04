<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class syscategory_mdl_brand extends dbeav_model {

    public $defaultOrder = array('order_sort',' ASC');

    /**
     *根据品牌名称获取品牌ID
     *
     * @param string $brandName 品牌名称
     * @return int   品牌ID
     */
    public function getBrandIdByName($brandName)
    {
        $row = $this->getRow('brand_id',array('brand_name'=>$brandName));
        return $row['brand_id'];
    }

    /**
     * 根据品牌ID，获取一条品牌数据
     * todo 品牌数据以后做缓存或存储到kvstore，因此调用该函数，不直接调用数据库
     *
     * @param int $brandId 品牌ID
     *
     * @return array $row 一条品牌数据
     */
    public function getBrandRow($brandId)
    {
        $row = $this->getRow('*',array('brand_id'=>$brandId));
        $row['order_sort'] = intval($row['order_sort']);
        return $row;
    }

    public function doDelete($brandId)
    {

        $objMdlCatBrand = app::get('syscategory')->model('cat_rel_brand');
        //$objMdlShopBrand = app::get('syscategory')->model('shop_rel_brand');
        $objMdlBrand = app::get('syscategory')->model('brand');

        $CatInfo = $objMdlCatBrand->getList('cat_id,brand_id',array('brand_id'=>$brandId));
        if($CatInfo)
        {
            $msg = app::get('syscategory')->_('品牌关联分类，不可被删除');
            throw new \LogicException($msg);
            return false;
        }

        /*$shopBrandInfo = $objMdlShopBrand->getList('shop_id,brand_id',array('brand_id'=>$brandId));
        if($shopBrandInfo)
        {
            $msg = app::get('syscategory')->_('该品牌被店铺签约，不可被删除');
            throw new \LogicException($msg);
            return false;
        }*/

        $delResult = $objMdlBrand->delete(array('brand_id'=>$brandId));
        if(!$delResult)
        {
            $msg = app::get('syscategory')->_('品牌删除失败');
            throw new \LogicException($msg);
            return false;
        }
        return true;
    }
}


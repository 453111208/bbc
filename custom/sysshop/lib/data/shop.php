<?php

/**
 * @brief 企业相关
 */
class sysshop_data_shop{

    /**
     * @brief 开通企业
     *
     * @param $enterapplyId int
     *
     * @return  bool
     */
    public function openShop($enterapplyId)
    {
        $objMdlShop = app::get('sysshop')->model('shop');
        $objMdlSeller = app::get('sysshop')->model('seller');
        $objMdlSellerRel = app::get('sysshop')->model('shop_rel_seller');
        $objMdlShopInfo = app::get('sysshop')->model('shop_info');
        $objMdlShopCat = app::get('sysshop')->model('shop_rel_lv1cat');
        $objMdlShopBrand = app::get('sysshop')->model('shop_rel_brand');
        $objMdlEnterapply = app::get('sysshop')->model('enterapply');
        $params = $objMdlEnterapply->getRow("*",array('enterapply_id'=>$enterapplyId));

        if(!$params['seller_id'])
        {
            $msg = "企业管理员不可为空";
            throw new \LogicException($msg);
            return false;
        }
        if(!$params['shop_name'])
        {
            $msg = "企业名称不可为空";
            throw new \LogicException($msg);
            return false;
        }

        $shop = unserialize($params['shop']);
        $shop['seller_id'] = $params['seller_id'];
        $shop['shop_name'] = $params['shop_name'];
        $shop['shop_type'] = $params['shop_type'];
        $shop['shopuser_name'] = $params['shopuser_name'];
        $shop['open_time'] = time();
        $shop['status'] = 'active';
        if(!$shop['shop_cat'])
        {
            $msg = "企业签约类目不可为空";
            throw new \LogicException($msg);
            return false;
        }

        $db = app::get('sysshop')->database();
        $db->beginTransaction();
        try
        {
            $shop_id = $objMdlShop->insert($shop);
            if(!$shop_id)
            {
                $msg = "企业开通失败，shop信息保存出错";
                throw new \LogicException($msg);
            }

            $shopInfo = unserialize($params['shop_info']);
            $shopInfo['company_name'] = $params['company_name'];
            $shopInfo['seller_id'] = $params['seller_id'];
            $shopInfo['shop_id'] = $shop_id;
            $result = $objMdlShopInfo->insert($shopInfo);
            if(!$result)
            {
                $msg = "商店明细表插入失败";
                throw new \LogicException($msg);
            }

            $shopcat = array(
                'cat_id' => $shop['shop_cat'],
                'shop_id'=>$shop_id,
            );
            $result = $objMdlShopCat->insert($shopcat);
            if(!$result)
            {
                $msg = "商店关联类目表出错";
                throw new \LogicException($msg);
            }

            if($shop['shop_type'] != "cat")
            {
                $shopbrand = array(
                    'brand_id'=>$shop['shop_brand'],
                    'shop_id'=>$shop_id,
                    'brand_warranty'=>$shop['brand_warranty'],
                );
                $result = $objMdlShopBrand->insert($shopbrand);
                if(!$result)
                {
                    $msg = "商店关联品牌表出错";
                    throw new \LogicException($msg);
                }
            }

            $sellerShop = array(
                'shop_id'=>$shop_id,
                'seller_id' => $params['seller_id'],
                'shop_name' => $params['shop_name'],
            );
            $result = $objMdlSellerRel->insert($sellerShop);
            if(!$result)
            {
                $msg = "商店关联企业表出错";
                throw new \LogicException($msg);
            }
            $logdata = unserialize($params['enterlog']);
            $los= array(
                array(
                    'plan'=>'开通企业',
                    'times' => time(),
                    'hint' => '企业已开通',
                    'status' => 'opened',
                ),
            );
            $losdata = array_merge($logdata,$los);
            $enterapplyData['enterapply_id'] = $enterapplyId;
            $enterapplyData['status'] = 'finish';
            $enterapplyData['enterlog'] = $losdata;
            $result = $objMdlEnterapply->save($enterapplyData);
            if(!$result)
            {
                $msg = "开通企业log记录失败";
                throw new \LogicException($msg);
            }
            //修改seller表
            $seller = array(
                'shop_id'=>$shop_id,
                'seller_id' => $params['seller_id'],
            );
            $result = $objMdlSeller->save($seller);
            if(!$result)
            {
                $msg = "开通企业失败";
                throw new \LogicException($msg);
            }
            $db->commit();
            return true;
        }
        catch(\LogicException $e)
        {
            $db->rollback();
            throw new \LogicException($e->getMessage());
            return false;
        }
    }

    /**
     * @brief 更改企业状态
     *
     * @param $shopId 企业编号
     * @param $status 更改的企业状态
     *
     * @return  bool
     */
    public function updateShopStatus($shopdata)
    {
        $objMdlShop = app::get('sysshop')->model('shop');
        $shopdata['close_time'] = time();
        $result = $objMdlShop->save($shopdata);
        if(!$result)
        {
            $msg = "修改企业状态失败";
            throw new \LogicException($msg);
            return false;
        }

        if($shopdata['status'] != "dead") return true;

        $objItem = kernel::single('sysitem_data_item');
        unset($shopdata['status'],$shopdata['close_time']);
        $result = $objItem->batchCloseItem($shopdata,'instock',$msg);
        if(!$result)
        {
            throw new \LogicException($msg);
            return false;
        }
        return true;
    }


    /**
     * @brief 更新企业
     *
     * @param $shopData
     *
     * @return bool
     */
    public function saveShop($shopData)
    {

        $objMdlShop = app::get('sysshop')->model('shop');
        $shopName = $objMdlShop->getRow('shop_name',array('shop_name'=>$shopData['shop_name']));
        if($shopName['shop_name']&&empty($shopData['shop_id']))
        {
            $msg = "该企业名称已经存在，请重新设置企业名称.";
            throw new \LogicException($msg);
        }
        $result = $objMdlShop->save($shopData);
        return $result;
    }
    public function getShopById($shopId,$fields="*")
    {
        $objMdlShop = app::get('sysshop')->model('shop');
        $shopData = $objMdlShop->getRow($fields,array('shop_id'=>$shopId));
        return $shopData;
    }
    public function shopInfoUpdate($data,$shopId)
    {
        $objMdlShopInfo = app::get('sysshop')->model('shop_info');
        $objMdlShopInfo->update($data,array('shop_id'=>$shopId));
    }

    /**
     * @brief 根据条件查询企业信息
     *
     * @param string $row 需要获取的字段
     * @param array  $filter 查询条件
     * @param bool  $isRow  是否为查询单条数据
     *
     * @return array
     */
    public function getShopInfo($row,$filter,$isRow=true)
    {
        $row =str_append($row,'shop_name,shop_type');
        $objMdlShop = app::get('sysshop')->model('shop');
        $objMdlShopType = app::get('sysshop')->model('shop_type');

        $shopData = $objMdlShop->getList($row,$filter);
        $shopTypeData = $objMdlShopType->getList('suffix,name,shop_type');
        $type = array_bind_key($shopTypeData,'shop_type');

        foreach( $shopData as $k=>&$rowData )
        {
            //为企业名称填充后缀
            if($rowData['shop_name'] && $rowData['shop_type'])
            {
                $rowData['shopname'] = $rowData['shop_name'].$type[$rowData['shop_type']]['suffix'];
                $rowData['shoptype'] = $type[$rowData['shop_type']]['name'];
            }

            if( $isRow ) break;
        }

        return  $isRow ? $shopData[0] : $shopData;
    }

    /**
     * @brief 根据查询条件，获取多条企业信息
     *
     * @param string $fields 需要返回的字段
     * @param array  $filter 查询条件
     * @param $offset
     * @param $limit
     *
     * @return array
     */
    public function fetchListShopInfo($fields="*", $filter, $offset=0, $limit=-1)
    {
        $objMdlShop = app::get('sysshop')->model('shop');
        $shopData = $objMdlShop->getList($fields,$filter,$offset,$limit);
        return $shopData;
    }

    /**
     * @brief 根据shopid获取 以及每个分类对应的佣金比例和入驻金
     *
     * @param shopid
     *
     * @return data
     */
    public function shopRelCatInfo($shopId)
    {
        $objMdlShopRelCat =  app::get('sysshop')->model('shop_rel_lv1cat');
        $catInfo = $objMdlShopRelCat->getList('*',array('shop_id'=>$shopId));
        return $catInfo;
    }

    public function getShopCatFee($shopCatInfo)
    {
        foreach ($shopCatInfo as $key => $value)
        {
            $shopCat[$key]['fee_confg'] = unserialize($shopCatInfo[$key]['fee_confg']);
            $shopCat[$key]['cat_id'] = $value['cat_id'];
            $shopCat[$key]['shop_id'] = $value['shop_id'];
        }
        foreach ($shopCat as $item => $fmt)
        {
            foreach ($fmt['fee_confg'] as $key => $value)
            {
                $lvName[$key] = $this->__getCatName($key);
                foreach ($value as $ke => $va)
                {
                    $lv2Name[$ke] = $this->__getCatName($ke);
                    foreach ($va as $k => $v)
                    {
                        $lv3Name[$k] = $this->__getCatName($k);
                    }
                }
            }

        }

        foreach ($shopCat as $item => $fmt)
        {
            foreach ($fmt['fee_confg'] as $key => $value)
            {
                $data[$key][$key]['cat_id'] = $key;
                $data[$key][$key]['cat_name'] = $lvName[$key];
                $data[$key][$key]['cat_fee'] = $value['lvfee'];
                unset($data[$key]['lvfee']);
                foreach ($value as $ke => $va)
                {
                    $data[$key][$ke][$ke]['cat_id'] = $ke;
                    $data[$key][$ke][$ke]['cat_name'] = $lv2Name[$ke];
                    $data[$key][$ke][$ke]['cat_fee'] = $va['lv2fee'];
                    unset($data[$key]['lvfee']);
                    foreach ($va as $k => $v)
                    {
                        $data[$key][$ke][$k]['cat_id'] = $k;
                        $data[$key][$ke][$k]['cat_name'] = $lv3Name[$k];
                        $data[$key][$ke][$k]['cat_fee'] = $v;
                        unset($data[$key][$ke]['lv2fee']);
                    }
                }
            }
        }
        return $data;
    }

    private function __getCatName($catId)
    {
        $data = app::get('sysshop')->rpcCall('category.cat.get.info',array('cat_id'=>$catId,'cat_name'));
        return $data[$catId]['cat_name'];
    }

    /**
     * @brief 根据shopid获取商店类目关联信息
     *
     * @param shopid
     *
     * @return data
     */
    public function getShopRelCat($shopId,$row="*")
    {
        $objMdlShopRelCat =  app::get('sysshop')->model('shop_rel_lv1cat');
        $objMdlCat =  app::get('syscategory')->model('cat');
        $catInfo = $objMdlShopRelCat->getList('cat_id',array('shop_id'=>$shopId));
        foreach($catInfo as $key=>$value)
        {
            $catIds['cat_id'][] = $value['cat_id'];
        }
        $cats = $objMdlCat->getList($row,$catIds);
        return $cats;
    }

    /**
     * @brief 企业关联的品牌(品牌旗舰店每个品牌仅此一家)
     *
     * @param $shopId 企业编号
     *
     * @return array
     */
    public function getShopRelBrand($shopId,$row="*")
    {
        $filter['shop_id'] = $shopId;
        $objMdlShopRelBrand = app::get('sysshop')->model('shop_rel_brand');
        $objMdlBrand = app::get('syscategory')->model('brand');
        $relBrandIds = $objMdlShopRelBrand->getList('brand_id',$filter);
        $brandIds = array();
        foreach($relBrandIds as $v)
        {
            $brandIds['brand_id'][] = $v['brand_id'];
        }
        $result = $objMdlBrand->getList($row,$brandIds);
        return $result;

    }


    /**
     * @brief 获取企业详细信息（包含入驻的所有信息）
     *
     * @param $shopId
     * @param $row
     * @param $extends
     *
     * @return
     */
    public function getShopDetail($shopId,$row,$extends)
    {
        $shopData['shop'] = $this->getShopInfo($row,array('shop_id'=>$shopId));
        if(!$shopData['shop'])
        {
        }
        if($extends['info'])
        {
            $row = $extends['info'];
            $objMdlShopInfo = app::get('sysshop')->model('shop_info');
            $shopData['shop_info'] = $objMdlShopInfo->getRow($row,array('shop_id'=>$shopId));
        }

        if($extends['cat'])
        {
            $row = $extends['cat'];
            $shopData['cat'] = $this->getShopRelCat($shopId,$row);
        }

        if($extends['brand'])
        {
            $row = $extends['brand'];
            $shopData['brand'] = $this->getShopRelBrand($shopId,$row);
        }

        return $shopData;
    }
}



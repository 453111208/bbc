<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysshop_data_cat {

    public function __construct($app)
    {
        $this->app = $app;
        $this->objMdlShopCat = app::get('sysshop')->model('shop_cat');
    }

    /**
     * @brief 存储企业分类数据
     *
     * @param array $data 企业分类数据
     * @param string $msg 添加企业分类成功或失败信息
     *
     * @return bool
     */
    public function addShopCat($data,$shopId)
    {
        $count = $this->objMdlShopCat->count(array('shop_id'=>$shopId));

        foreach( (array)$data as $parentId => $val )
        {
            if( $val['cat_name'] )
            {
                $insertData = array();

                $insertData['cat_name'] = trim($val['cat_name']);
                $insertData['order_sort'] = intval($val['order_sort']);
                $insertData['modified_time'] = time();
                $insertData['shop_id'] = $shopId;
                $count++;
                if( $count > 100 )
                {
                    $msg = app::get('sysshop')->_('企业分类总数量不能超过100个');
                    throw new \LogicException($msg);
                    return false;
                }
                $parentId = $this->objMdlShopCat->insert($insertData);
                if( !$parentId )
                {
                    $msg = app::get('sysshop')->_('企业分类保存失败');
                    throw new \LogicException($msg);
                    return false;
                }
            }

            foreach( (array)$val as $row )
            {
                if( !is_array($row) ) continue;
                $subCatData = array();
                $subCatData['shop_id'] = $shopId;

                $subCatData['cat_name'] = trim($row['cat_name']);
                $subCatData['order_sort'] = intval($row['order_sort']);
                $subCatData['modified_time'] = time();
                $subCatData['parent_id'] = $parentId;
                $subCatData['cat_path'] = $parentId.',';
                $subCatData['level'] = 2;
                $subCatData['is_leaf'] = 1;
                $count++;
                if( $count > 100 )
                {
                    $msg = app::get('sysshop')->_('企业分类总数量不能超过100个');
                    throw new \LogicException($msg);
                    return false;
                }
                $catId = $this->objMdlShopCat->insert($subCatData);
                if( !$catId )
                {
                    $msg = app::get('sysshop')->_('企业分类保存失败');
                    throw new \LogicException($msg);
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * @brief 更新企业分类数据
     *
     * @param array $data
     *
     * @return bool
     */
    public function updateShopCat($data,$shopId)
    {
        foreach( (array)$data as $catId=>$row )
        {
            $filter['cat_id'] = $catId;
            $filter['shop_id'] = $shopId;

            $upData['cat_name'] = $row['cat_name'];
            $upData['order_sort'] = intval($row['order_sort']);
            $upData['modified_time'] = time();
            if( !$this->objMdlShopCat->update($upData, $filter) )
            {
                $msg = app::get('sysshop')->_('企业分类保存失败');
                throw new \LogicException($msg);
                return false;
            }
        }
        return true;
    }

    /**
     * @brief 删除一行
     *
     * @param $catId
     *
     * @return
     */
    public function removeShopCatRow($catId,$shopId)
    {
        if( !$this->objMdlShopCat->delete(array('cat_id'=>$catId,'shop_id'=>$shopId)) ) return false;
        return true;
    }

    /**
     * @brief 批量删除删除企业分类
     *
     * @param array $catData
     *
     * @return bool
     */
    public function removeShopCat($data=array(),$shopId)
    {
        $catData = $this->objMdlShopCat->getList('*', array('cat_id'=>$data));
        foreach( $catData as $val )
        {
            //判断删除的分类是否属于该企业
            if( $val['shop_id'] != $shopId) continue;
            $tmpCatId = $val['cat_id'];
            $tmpCatData[$tmpCatId] = $val;
        }

        foreach( (array)$data as $catId )
        {
            //todo@wei 判断该分类下是否有商品，有商品则解除绑定 return false

            //如果为子叶节点则直接删除
            if( $tmpCatData[$catId]['is_leaf'] == 1 )
            {
                $delCatId[] = $catId;//可以删除的catId
            }
            else
            {
                $catids[] = $catId; //父类节点
            }
        }

        if( $delCatId && !$this->objMdlShopCat->delete(array('cat_id'=>$delCatId))) return false;

        $parentCatData = $this->objMdlShopCat->getList('cat_id', array('parent_id'=>$catids));
        foreach( $parentCatData as $val )
        {
            $tmpparentCatId = $val['cat_id'];
            $tmpparentCatData[$tmpparentCatId] = $val;
        }

        foreach( (array)$catids as $catid )
        {
            if( !$tmpparentCatData[$catid] )
            {
                $delparentcatIds[] = $catid;
            }
        }

        if($delparentcatIds && !$this->objMdlShopCat->delete(array('cat_id'=>$delparentcatIds)) ) return false;

        return true;
    }

    public function fetchShopCat($fields="*", $filter, $offset=0, $limit=-1)
    {
        $tmpData = $this->objMdlShopCat->getList($fields, $filter, $offset, $limit,'order_sort asc');
        foreach( $tmpData as $row )
        {
            if( $row['level'] == '1' )
            {
                $data[$row['cat_id']] = $row;
            }
            else
            {
                $children[$row['parent_id']][$row['cat_id']] = $row;
            }
        }

        foreach( $children as $parentId=>$val )
        {
            if( $data[$parentId] )
            {
                $data[$parentId]['children'] = $children[$parentId];
            }
            else
            {
                foreach( $val as $catId=>$row )
                {
                    $data[$catId] = $row;
                }
            }
        }

        return $data;
    }

    /**
     *  检查企业分类名称是否可以插入 更新
     */
    private function __checkCat($data)
    {
        if( $data['del'] )
        {
            $delCatId = array_keys($data['del']);
        }

        $catData = array();
        $catParentData = array();
        if( $data['new'] )
        {
            foreach( (array)$data['new'] as $parentId=>$cat )
            {
                foreach($cat as $col=>$row)
                {
                    if( is_array($row) )
                    {
                        $this->__checkCatName($row['cat_name']);
                        if( in_array($row['cat_name'], $catData[$parentId]) )
                        {
                            $msg = app::get('sysshop')->_("企业分类名称{$row['cat_name']}不能重复");
                            throw new \LogicException($msg);
                        }
                        $catData[$parentId][] = $row['cat_name'];
                    }
                    elseif( $col == 'cat_name' )
                    {
                        $this->__checkCatName($row);
                        if( in_array($row,$catParentData) )
                        {
                            $msg = app::get('sysshop')->_("企业分类名称{$row}不能重复");
                            throw new \LogicException($msg);
                        }
                        $catParentData[] = $row;
                    }
                }
            }
        }

        $this->__checkCatRepeat($data['cat'], $catData, $catParentData, $delCatId);

        $this->__checkCatRepeat($data['up'], $catData, $catParentData, $delCatId);

        return true;
    }

    private function __checkCatRepeat($checkData, &$catData, &$catParentData, $delCatId)
    {
        foreach( (array)$checkData as $catId=>$catRow )
        {
            //如果在删除里面则忽略
            if( $delCatId && in_array($catId, $delCatId) )
            {
                continue;
            }

            $this->__checkCatName($catRow['cat_name']);

            if( $catRow['parent_id'] )
            {
                if( in_array($catRow['cat_name'], $catData[$catRow['parent_id']]) )
                {
                    $msg = app::get('sysshop')->_("企业分类名称{$catRow['cat_name']}不能重复");
                    throw new \LogicException($msg);
                }
                $catData[$catRow['parent_id']][] = $catRow['cat_name'];
            }
            else
            {
                if( in_array($catRow['cat_name'],$catParentData) )
                {
                    $msg = app::get('sysshop')->_("企业分类名称{$catRow['cat_name']}不能重复");
                    throw new \LogicException($msg);
                }
                $catParentData[] = $catRow['cat_name'];
            }
        }

        return true;
    }

    private function __checkCatName($catName)
    {
        if( empty($catName) )
        {
            $msg = app::get('sysshop')->_("企业分类名称不能为空");
            throw new \LogicException($msg);
        }

        if( mb_strlen(trim($catName),'utf8') > 20 )
        {
            $msg = app::get('sysshop')->_("企业分类名称不能超过20个字");
            throw new \LogicException($msg);
        }

        return true;
    }

    /**
     * @brief POST提交的数据，带事物的统一保存
     *
     * @param array $data
     *
     * @return bool
     */
    public function storeCat($data,$shopId)
    {
        $this->__checkCat($data);

        $db = app::get('sysshop')->database();
        $db->beginTransaction();

        try
        {
            //删除企业分类
            if( $data['del'] && !$this->removeShopCat($data['del'],$shopId) )
            {
                throw new \LogicException('批量删除删除企业分类失败');
            }

            if( $data['up'] && !$this->updateShopCat($data['up'],$shopId) )
            {
                throw new \LogicException('更新企业分类数据失败');
            }

            if( $data['new'] && !$this->addShopCat($data['new'],$shopId) )
            {
                throw new \LogicException('存储企业分类数据失败');
            }
            $db->commit();

        }
        catch(\Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;
    }

    /**
     * @brief 提交的数据，查询一级类目id
     *
     * @param int $shopId
     *
     * @return 类目id array()
     */
    public function getCatInfo($shopId)
    {
        $shopRelCatMdl = app::get('sysshop')->model('shop_rel_lv1cat');
        $catId = $shopRelCatMdl->getList('*',array('shop_id'=>$shopId));
        return $catId;
    }

    /**
     * @brief 根据shopid  catid  获取三级类目费率id
     *
     * @param data array(catid shopid)
     *  @param catid 三级类目id
     * @return 三级类目的费率
     */
    public function getCatServiceRate($data)
    {
        $catId = $data['cat_id'];
        //判断企业是不是自营企业 gongjiapeng
        $ojbShop = kernel::single('sysshop_data_shop');
        $selfShopType = $ojbShop->getShopById(array('shop_id'=>$data['shop_id']),'shop_type');
        if($selfShopType['shop_type']=='self')
        {
            $catLv3Fee = 0.00;
            return $catLv3Fee;
        }

        $catParams = array(
            'cat_id' => $catId,
            'fields' =>'cat_id,cat_path,level,is_leaf',
        );
        $catinfo = app::get('sysshop')->rpcCall('category.cat.get.info',$catParams);
        $cat = $catinfo[$catId];

        if(!$cat || !$cat['is_leaf'] || $cat['level'] !=3)
        {
            $msg = "必须传入3级类目id";
            throw new \LogicException($msg);
        }
        $parentCat = array_filter(explode(',',$cat['cat_path']));

        $shopRelCatMdl = app::get('sysshop')->model('shop_rel_lv1cat');
        $catFee = $shopRelCatMdl->getRow('fee_confg',array('shop_id'=>$data['shop_id'],'cat_id'=>$parentCat[1]));
        $feeList = unserialize($catFee['fee_confg']);
        $lv3Fee = $feeList[$parentCat[1]][$parentCat[2]][$catId];
        $catLv3Fee = kernel::single('ectools_math')->number_div(array($lv3Fee,100));

        if($lv3Fee)
        {
            return $catLv3Fee;
        }
        else
        {
            $msg = "没有该三级分类的费率";
            throw new \LogicException($msg);
        }
    }
}



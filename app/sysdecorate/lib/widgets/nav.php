<?php

class sysdecorate_widgets_nav {

    public $navMenuNum = 0;

    /**
     * 处理弹出框页面的中需要配置的数据
     *
     * @param string $params 挂件配置的参数
     * @param string $dialogName  弹出框名称
     *
     */
    public function setting($params, $widgetsParams, $dialogName)
    {
        if( $dialogName == 'add' )
        {

            $widgetsParams['cat'] = $this->__preCatParams($params['cat']);

            if( $params['old_name'] )
            {
                unset($widgetsParams['link'][$params['old_name']]);
            }

            if( $params['link']['name'] )
            {
                $linkParams = $params['link'];
                if( $widgetsParams['link'][$linkParams['name']] )
                {
                    throw new \LogicException(app::get('sysdecorate')->_('导航菜单已存在，请换一个'));
                }
                $this->__checkLinkParams($linkParams);
                $widgetsParams['link'][$linkParams['name']]['name'] = $linkParams['name'];
                $widgetsParams['link'][$linkParams['name']]['url'] = $linkParams['url'];
                $widgetsParams['link'][$linkParams['name']]['order_sort'] = $linkParams['order_sort'] ? $linkParams['order_sort'] : 0;

            }

            $this->navMenuNum += count($widgetsParams['link']);
            if( $this->navMenuNum > 8 )
            {
                throw new \LogicException(app::get('sysdecorate')->_('导航菜单不能超过8个'));
            }
        }

        if( $dialogName == 'default' )
        {
            $widgetsParams = $params;
        }

        return $widgetsParams;
    }

    /**
     * 处理分类参数
     *
     * @param array $catParams 分类参数
     * @param bool  $isCatName 是否需要显示分类名称
     */
    private function __preCatParams($catParams, $isCatName=false, $shopId)
    {
        foreach( $catParams as $catId=>$cat )
        {
            if( !$cat['cat_id'] )
            {
                if( isset($cat['order_sort']) )
                {
                    unset($catParams[$catId]['order_sort']);
                }
            }
            else
            {
                $this->navMenuNum += 1;
                $catIds[] = $cat['cat_id'];
            }

            foreach( $cat['children'] as $childrenCatId=>$row )
            {
                if( !$row['cat_id'] )
                {
                    unset($catParams[$catId]['children'][$childrenCatId]);
                }
                else
                {
                    $this->navMenuNum += 1;
                    $catIds[] = $row['cat_id'];
                }
            }
        }

        if( $isCatName )
        {
            $return['shopCatName'] = $this->__getShopCatName($catIds, $shopId);
            $return['params'] = $catParams;
        }
        else
        {
            $return = $catParams;
        }

        return $return;
    }

    private function __getShopCatName($catIds, $shopId)
    {
        if( empty($catIds) ) return array();

        $catFilter['cat_id'] = implode(',',$catIds);
        $catFilter['shop_id'] = $shopId;
        $tmpCatData = app::get('sysdecorate')->rpcCall('shop.cat.get',$catFilter);
        foreach( (array)$tmpCatData as $catId=>$row )
        {
            $data[$catId] = $row['cat_name'];
            foreach( (array)$row['children'] as $childrenCatId=>$childrenRow )
            {
                $data[$childrenRow['cat_id']] = $childrenRow['cat_name'];
            }
        }
        return $data;
    }

    private function __checkLinkParams($linkParams)
    {

        if( mb_strlen($linkParams['name'],'utf-8') > 8 )
        {
            throw new \LogicException(app::get('sysdecorate')->_('导航名称不能超过8字'));
        }

        $baseUrl = kernel::base_url(1);
        if( substr($linkParams['url'],0,4) != 'http' || stristr($linkParams['url'],$baseUrl) === false )
        {
            throw new \LogicException(app::get('sysdecorate')->_('只能为站内链接'));
        }

        return true;
    }

    /**
     * 获取弹出框页面的数据
     *
     * @param string $params 挂件配置的参数
     * @param string $dialogName  弹出框名称
     * @param int $shopId 店铺ID
     *
     */
    public function getDialogData($params, $dialogName, $shopId)
    {
        $data = array();

        if( $dialogName == 'default' )
        {
            $data = $this->__preCatParams($params['cat'], true, $shopId);
            foreach( $params['link'] as $k=>$v )
            {
                $data['params'][$k] = $v;
            }
        }

        if( $dialogName == 'add' )
        {
            $data['editType'] = input::get('edit');
            if( $data['editType'] == 'link' )
            {
                $linkData = $params['link'][input::get('key')];
                if( empty($linkData) )
                {
                    throw new \LogicException(app::get('sysdecorate')->_('参数错误'));
                }
                $data['link'] = $linkData;
            }

            $data['cat'] = app::get('sysdecorate')->rpcCall('shop.cat.get',array('shop_id'=>$shopId,'fields'=>'cat_id,cat_name,is_leaf,parent_id,level'));
            $data['selectCat'] = $params['cat'];
        }

        return $data;
    }

    /**
     * @brief 获取菜单挂件数据
     *
     * @param array $params
     * @param int $shopId
     *
     * @return array
     */
    public function getData($params, $shopId)
    {
        $data = $this->__preGetCatData($params['cat'], $shopId);
        $menuData = $data['menuData'];
        $menuSort = $data['menuSort'];

        $linkParams = $params['link'];
        //自定义链接菜单
        if( $linkParams )
        {
            foreach( $linkParams as $name=>$params )
            {
                $linkOrderSort[$name] = $params['order_sort'] ? $params['order_sort'] : 0;
                $menuSort[$name] = $linkOrderSort[$name];
            }

            asort($linkOrderSort);
            foreach( $linkOrderSort as $name=>$orderSort )
            {
                $menuData[$name] = $linkParams[$name];
            }
        }

        asort($menuSort);
        foreach( $menuSort as $id=>$sort)
        {
            $returnMenuData[$id] = $menuData[$id];
        }

        return $returnMenuData;
    }

    private function __preGetCatData($catParams, $shopId)
    {
        if( !$catParams ) return array();

        foreach( $catParams as $key=>$catRow )
        {
            foreach( (array)$catRow['children'] as $childrenCatId=>$val )
            {
                $catIds[] = $val['cat_id'];
                if( !$catRow['cat_id'] )
                {
                    $catOrderSort[$val['cat_id']] = $val['order_sort'] ? $val['order_sort'] : 0;
                }
                else
                {
                    $childrenCatOrderSort[$catRow['cat_id']][$childrenCatId] = $val['order_sort'] ? $val['order_sort'] : 0;
                }
            }

            //有父分类
            if( $catRow['cat_id'] )
            {
                $catIds[] = $catRow['cat_id'];
                $catOrderSort[$catRow['cat_id']] = $catRow['order_sort'] ? $catRow['order_sort'] : 0;
            }
        }

        $shopCatName = $this->__getShopCatName($catIds, $shopId);
        asort($catOrderSort);
        foreach( (array)$catOrderSort as $catId=>$row )
        {
            $menuData[$catId]['menu'] = $shopCatName[$catId];
            $menuData[$catId]['cat_id'] = $catId;
            if( isset($childrenCatOrderSort[$catId]) )
            {
                asort($childrenCatOrderSort[$catId]);
                foreach( $childrenCatOrderSort[$catId] as $childrenCatId=>$val )
                {
                    $menuData[$catId]['children'][$childrenCatId]['menu'] = $shopCatName[$childrenCatId];
                    $menuData[$catId]['children'][$childrenCatId]['cat_id'] = $childrenCatId;
                }
            }
            $menuSort[$catId] = $row;
        }
        $data['menuData'] = $menuData;
        $data['menuSort'] = $menuSort;
        return $data;
    }

}


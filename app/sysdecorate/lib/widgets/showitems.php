<?php

class sysdecorate_widgets_showitems {

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
            $name = $params['title'];

            if( !$name )
            {
                throw new \LogicException(app::get('sysdecorate')->_('请填写有效的栏目名称'));
            }

            if( (empty($params['name']) || ($params['name'] && $params['name'] != $name) ) && $widgetsParams[$name] )
            {
                throw new \LogicException(app::get('sysdecorate')->_('栏目名称已存在,不能重复'));
            }

            if( !empty($params['name']) )
            {
                unset($widgetsParams[$params['name']]);
            }
            $widgetsParams[$name]['title'] = $name;
            $widgetsParams[$name]['num'] = $params['num'];
            $widgetsParams[$name]['order_sort'] = $params['order_sort'];
            $widgetsParams[$name]['filter']['goods_keywords'] = $params['goods_keywords'];
            $widgetsParams[$name]['filter']['shopCatIds'] = $params['shopCatIds'];
        }

        if( $dialogName == 'default' )
        {
            $data = $params['data'];
            $widgetsParamsTmp = array();
            foreach( (array)$data as $key=>$order_sort )
            {
                $widgetsParamsTmp[$key] = $widgetsParams[$key];
                $widgetsParamsTmp[$key]['order_sort'] = $order_sort['order_sort'];
            }
            $widgetsParams = array();
            $widgetsParams = $widgetsParamsTmp;
        }

        return $widgetsParams;
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
            $data = $params;
        }

        if( $dialogName == 'add' )
        {
            if( input::get('key') )
            {
                $data['params'] = $params[input::get('key')];
            }

            // 商家分类及此商品关联的分类标示selected
            $data['shopCatList'] = app::get('sysdecorate')->rpcCall('shop.cat.get',array('shop_id'=>$shopId,'fields'=>'cat_id,cat_name,is_leaf,parent_id,level'));
        }

        return $data;
    }


    /**
     * @brief 获取店铺商品数据
     *
     * @param $params
     *
     * @return
     */
    public function getData($params, $shopId, $usePlatform=null)
    {
        $goodsParams = $this->ksort($params);

        foreach( $goodsParams as $title=>$params )
        {
            $itemParams = array();
            if($params['filter']['goods_keywords'])
            {
                $itemParams['search_keywords'] = $params['filter']['goods_keywords'];
            }
            $itemParams['shop_cat_id'] = implode(',', $params['filter']['shopCatIds']);
            $itemParams['shop_id'] = $shopId;
            if($usePlatform)
            {
                $itemParams['use_platform'] = $usePlatform;
            }
            $itemParams['page_size'] = $params['num'];
            $itemParams['fields'] = 'item_id,image_default_id,title,price';

            $itemsList = app::get('sysdecorate')->rpcCall('item.search',$itemParams);
            $items[$title] = $itemsList['list'];
        }
        return $items;
    }

     /**
     * @brief 排序
     *
     * @param $params
     *
     * @return
     */
     private function ksort($data)
     {
        foreach ($data as $key => $value) {
            $age[] = $value['order_sort'];
        }
        array_multisort($age, SORT_ASC, $data);
        return $data;
     }
}


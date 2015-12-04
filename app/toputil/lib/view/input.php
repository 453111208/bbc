<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class toputil_view_input{

    function input_category($params)
    {
        $selectedCatId = $params['value'];
        $shopId = $params['shop_id'];

        if($selectedCatId && $shopId)
        {
            $selectedCatInfo = $this->__getCatList($selectedCatId,$shopId);
        }
        elseif(!$selectedCatId && $shopId)
        {
            $selectedCatInfo = $this->__getCatInfo($shopId);
        }

        $pagedata['value'] = json_encode($selectedCatInfo);
        $pagedata['callback'] = $params['callback'] ? $params['callback'] : false;
        return view::make('toputil/smarty/cat-select.html', $pagedata)->render();
    }

    private function __getCatInfo($shopId)
    {
        $shopAuthorize = app::get('toputil')->rpcCall('shop.authorize.catbrandids.get',array('shop_id'=>$shopId));
        $catId = $shopAuthorize[$shopId]['cat'];
        $shopType = $shopAuthorize[$shopId]['shop_type'];
        if(!$catId && $shopType == "self")
        {
            $catList = app::get('toputil')->rpcCall('category.cat.get.info',array('parent_id'=>'0','fields'=>'cat_id,cat_name,child_count'));
        }
        elseif($catId)
        {
            $catList = app::get('toputil')->rpcCall('category.cat.get.info',array('cat_id'=>implode(',',$catId),'fields'=>'cat_id,cat_name,child_count'));
        }
        else{
            $catList = array();
        }

        if($catList)
        {
            foreach($catList as $key=>$value) {
                $newList[0]['options'][$key] = array(
                    'value' => $value['cat_id'],
                    'text' => $value['cat_name'],
                    'hasChild' => ($value['child_count'] >0) ? true : false,
                );
            }
        }
        return $newList;
    }

    private function __getCatList($sCatId,$shopId)
    {
        $shopAuthorize = app::get('toputil')->rpcCall('shop.authorize.catbrandids.get',array('shop_id'=>$shopId));
        $catId = $shopAuthorize[$shopId]['cat'];
        $shopType = $shopAuthorize[$shopId]['shop_type'];
        if(!$catId && $shopType == "self")
        {
            $catList = app::get('toputil')->rpcCall('category.cat.get.list');
        }
        elseif($catId)
        {
            $catList = app::get('toputil')->rpcCall('category.cat.get',array('cat_id'=>implode(',',$catId)));
        }

        foreach($catList as $key=>$value)
        {
            $lv1Data[$value['parent_id']]['options'][$key] = array(
                'value' => $value['cat_id'],
                'text' => $value['cat_name'],
                'hasChild' => ($value['child_count'] >0) ? true : false,
            );
            foreach($value['lv2'] as $key2=>$value2)
            {
                $lv2Data[$value2['parent_id']]['options'][$key2] = array(
                    'value' => $value2['cat_id'],
                    'text' => $value2['cat_name'],
                    'hasChild' => ($value2['child_count'] >0) ? true : false,
                );

                foreach($value2['lv3'] as $key3=>$value3)
                {
                    $lv3Data[$value3['parent_id']]['options'][$key3] = array(
                        'value' => $value3['cat_id'],
                        'text' => $value3['cat_name'],
                        'hasChild' => ($value3['child_count'] >0) ? true : false,
                    );
                    if($value3['cat_id'] == $sCatId)
                    {
                        $lv3Data[$value3['parent_id']]['selectedIndex'] = $sCatId;
                        $lv3ParentId = $value3['parent_id'];
                    }
                }
                if($lv3ParentId == $key2)
                {
                    $lv2Data[$value2['parent_id']]['selectedIndex'] = $key2;
                    $lv2ParentId = $value2['parent_id'];
                }
            }

            if($key == $lv2ParentId)
            {
                $lv1Data[$value['parent_id']]['selectedIndex'] = $key;
                $lv1ParentId = $value['parent_id'];
            }
        }

        $newList[$lv1ParentId] = $lv1Data[$lv1ParentId];
        $newList[$lv2ParentId] = $lv2Data[$lv2ParentId];
        $newList[$lv3ParentId] = $lv3Data[$lv3ParentId];
        return $newList;
    }

    function input_item_select($params)
    {
    }
}

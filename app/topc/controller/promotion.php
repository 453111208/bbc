<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topc_ctl_promotion extends topc_controller {

    public function getPromotionItem()
    {
        $filter = input::get();

        $promotionInfo = app::get('topc')->rpcCall('promotion.promotion.get', array('promotion_id'=>$filter['promotion_id']));
        if($promotionInfo['valid'])
        {
            if(!$filter['pages'])
            {
                $filter['pages'] = 1;
            }
            $pageSize = 20;
            $params = array(
                'page_no' => $pageSize*($filter['pages']-1),
                'page_size' => $pageSize,
                'fields' =>'item_id,shop_id,title,image_default_id,price',
            );
            //获取促销商品列表
            $promotionItem = $this->__promotionItemList($promotionInfo,$params);
            //echo '<pre>';print_r($promotionItem);exit();
            $count = $promotionItem['total_found'];
            $promotionItemList = $promotionItem['list'];

            //处理翻页数据
            $current = $filter['pages'] ? $filter['pages'] : 1;
            $filter['pages'] = time();
            if($count>0) $total = ceil($count/$pageSize);
            $pagedata['pagers'] = array(
                'link'=>url::action('topc_ctl_promotion@getPromotionItem',$filter),
                'current'=>$current,
                'total'=>$total,
                'token'=>$filter['pages'],
            );
            $pagedata['promotionItemList']= $promotionItemList;
            $pagedata['count'] = $count;
            $pagedata['promotionInfo'] = $promotionItem['promotionInfo'];
            $pagedata['promotiontype'] = $promotionInfo['promotion_type'];
        }
        else
        {
            return abort(404);
        }
        return $this->page("topc/promotion/promotion.html",$pagedata);
    }

    //获取促销的类型以及商品数据
    private function __promotionItemList($promotionInfo,$params)
    {
        switch ($promotionInfo['promotion_type'])
        {
            case 'fullminus':
                $params['fullminus_id'] = $promotionInfo['rel_promotion_id'];
                $promotionItem = app::get('topc')->rpcCall('promotion.fullminusitem.list', $params);
                $promotionItem['promotionInfo']['condition_value'] = $this->getConditionValue($promotionItem['promotionInfo']['condition_value']);
                break;
            case 'coupon':
                $params['coupon_id'] = $promotionInfo['rel_promotion_id'];
                $promotionItem = app::get('topc')->rpcCall('promotion.couponitem.list', $params);
                break;
            case 'fulldiscount':
                $params['fulldiscount_id'] = $promotionInfo['rel_promotion_id'];
                $promotionItem = app::get('topc')->rpcCall('promotion.fulldiscountitem.list', $params);
                $promotionItem['promotionInfo']['condition_value'] = $this->getConditionValue($promotionItem['promotionInfo']['condition_value']);
                break;
            case 'freepostage':
                $params['freepostage_id'] = $promotionInfo['rel_promotion_id'];
                $promotionItem = app::get('topc')->rpcCall('promotion.freepostageitem.list', $params);
                break;
            case 'xydiscount':
                $params['xydiscount_id'] = $promotionInfo['rel_promotion_id'];
                $promotionItem = app::get('topc')->rpcCall('promotion.xydiscountitem.list', $params);
                break;
            default:
                $params['fullminus_id'] = $promotionInfo['rel_promotion_id'];
                $promotionItem = app::get('topc')->rpcCall('promotion.fullminusitem.list', $params);
                break;
        }
        //echo '<pre>';print_r($promotionItem);exit();
        return $promotionItem;
    }

    public function getConditionValue($data)
    {
        $conditionValue = explode(",",$data);
        foreach ($conditionValue as $key => $value)
        {
            $fmt[$key] = explode("|",$value);
        }
        return $fmt;
        
    }

}


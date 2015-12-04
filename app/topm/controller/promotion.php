<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topm_ctl_promotion extends topm_controller {

    public function getPromotionItem()
    {
        $filter = input::get();
        $pagedata = $this->__commonData($filter);
        //echo '<pre>';print_r($pagedata);exit();
        return $this->page("topm/shop/promotion/index.html",$pagedata);
    }

    public function __commonData($filter)
    {
        //$filter = input::get();

        $promotionInfo = app::get('topm')->rpcCall('promotion.promotion.get', array('promotion_id'=>$filter['promotion_id']));
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

            $count = $promotionItem['total_found'];
            $promotionItemList = $promotionItem['list'];
            if( userAuth::check())
            {
                $pagedata['nologin'] = 1;
            }
            //处理翻页数据
            $current = $filter['pages'] ? $filter['pages'] : 1;
            $filter['pages'] = time();
            if($count>0) $total = ceil($count/$pageSize);
            $pagedata['pagers'] = array(
                'link'=>url::action('topm_ctl_promotion@getPromotionItem',$filter),
                'current'=>$current,
                'total'=>$total,
                'token'=>$filter['pages'],
            );
            $pagedata['promotionInfo'] = $promotionItem['promotionInfo'];
            $pagedata['promotionItemList']= $promotionItemList;
            $pagedata['count'] = $count;
            $pagedata['title'] = $promotionItem['promotionInfo']['promotion_tag'];
            $pagedata['promotiontype'] = $promotionInfo['promotion_type'];
        }
        else
        {
            return abort(404);
        }
        return $pagedata;
    }

    //获取促销的类型以及商品数据
    private function __promotionItemList($promotionInfo,$params)
    {
        switch ($promotionInfo['promotion_type'])
        {
            case 'fullminus':
                $params['fullminus_id'] = $promotionInfo['rel_promotion_id'];

                $promotionItem = app::get('topm')->rpcCall('promotion.fullminusitem.list', $params);
                break;
            case 'coupon':
                $params['coupon_id'] = $promotionInfo['rel_promotion_id'];
                $promotionItem = app::get('topm')->rpcCall('promotion.couponitem.list', $params);
                break;
            case 'fulldiscount':
                $params['fulldiscount_id'] = $promotionInfo['rel_promotion_id'];

                $promotionItem = app::get('topm')->rpcCall('promotion.fulldiscountitem.list', $params);
                break;
            case 'freepostage':
                $params['freepostage_id'] = $promotionInfo['rel_promotion_id'];
                $promotionItem = app::get('topm')->rpcCall('promotion.freepostageitem.list', $params);
                break;
            case 'xydiscount':
                $params['xydiscount_id'] = $promotionInfo['rel_promotion_id'];
                $promotionItem = app::get('topm')->rpcCall('promotion.xydiscountitem.list', $params);
                break;
            default:
                $params['fullminus_id'] = $promotionInfo['rel_promotion_id'];
                $promotionItem = app::get('topm')->rpcCall('promotion.fullminusitem.list', $params);
                break;
        }

        return $promotionItem;
    }

    public function ajaxPromotionItemShow()
    {
        $pagedata = $this->__commonData(input::get());

        $data['html'] = view::make('topm/shop/promotion/promotionitem.html',$pagedata)->render();
        $data['pagers'] = $pagedata['pagers'];
        $data['success'] = true;
        return response::json($data);exit;
    }

}


<?php
class syspromotion_api_activity_registerActivity{

    public $apiDescription = "报名活动";

    public function getParams()
    {
        $data['params'] = array(
            'activity_id' => ['type'=>'int', 'valid'=>'required|int', 'default'=>'', 'example'=>'', 'description'=>'活动id'],
            'shop_id' => ['type'=>'int', 'valid'=>'required|int', 'default'=>'', 'example'=>'', 'description'=>'店铺id'],
            'item_info' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'商品及活动价格'],
        );
        return $data;
    }

    public function registerActivity($params)
    {
        $filter['activity_id'] = $params['activity_id'];
        $itemContent = explode(';', $params['item_info']);
        $activityItem = array();
        $nowtime = time();
        //获取店铺信息
        $shopParams = array(
            'shop_id' => $params['shop_id'],
            'fields' =>'cat.cat_name,cat.cat_id,brand.brand_name,brand.brand_id,info',
        );
        $shopdata = app::get('syspromotion')->rpcCall('shop.get.detail',$shopParams);
        foreach ($shopdata['cat'] as $key => $value)
        {
            $catId[$key] = $value['cat_id'];
        }
        $shoptype = $shopdata['shop']['shop_type'];
        // 获取活动规则信息
        $activityParams = array(
            'activity_id' => $params['activity_id'],
            'fields' => '*',
        );
        $activityInfo = app::get('syspromotion')->rpcCall('promotion.activity.info', $activityParams);
        foreach ($activityInfo['limit_cat'] as $key => $value)
        {
            $activityItemIds[] = $key;
        }
        //判断活动数据
        if($activityInfo['apply_begin_time']< $nowtime && $nowtime<$activityInfo['apply_end_time'])
        {
            if(!(array_intersect($catId,$activityItemIds) && $activityInfo['shoptype'][$shoptype]))
            {
                throw new \LogicException(app::get('syspromotion')->_('抱歉,您不符合申请标准！'));
            }
            else
            {
                if(count($itemContent)>$activityInfo['enroll_limit'])
                {
                    throw new \LogicException(app::get('syspromotion')->_('抱歉,申请报名商品数量超出活动限制数量,申请无效！'));
                }
            }
        }
        else
        {
            throw new \LogicException(app::get('syspromotion')->_('抱歉,当前时间不在活动申请时间范围,申请无效！'));
        }

        foreach($itemContent as $v)
        {
            $tmp = explode(':', $v);
            $activityItem[] = array(
                'item_id' => $tmp[0],
                'activity_price' => $tmp[1],
            );
        }
        foreach ($activityItem as $key => $value)
        {
            $searchParams['fields'] = 'price';
            $searchParams['item_id'] = $value['item_id'];
            $itemPrice = app::get('topshop')->rpcCall('item.search',$searchParams);
            $activityItem[$key]['minprice'] = $itemPrice['list'][0]['price']*($activityInfo['discount_min']/100);
            $activityItem[$key]['maxprice'] = $itemPrice['list'][0]['price']*($activityInfo['discount_max']/100);
            if($value['activity_price']<$activityItem[$key]['minprice'] || $value['activity_price']>$activityItem[$key]['maxprice'])
            {
                throw new \LogicException(app::get('syspromotion')->_('请在商品折扣范围内设置促销价格！'));
            }
        }
        //echo '<pre>';print_r($activityItem);exit();
        $data = array(
            'activity_id' => $params['activity_id'],
            'shop_id' => $params['shop_id'],
            'item_info' => $activityItem,
            'activity_info' => $activityInfo,
        );
        //echo '<pre>';print_r($data);exit();
        $objActivity = kernel::single('syspromotion_activity');
        return $objActivity->saveRegisterActivity($data);

    }
}

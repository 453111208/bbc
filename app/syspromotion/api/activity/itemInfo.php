<?php
/**
 * promotion.activity.item.info
 */
class syspromotion_api_activity_itemInfo{
    public $apiDescription = "获取参与活动的商品详情";
    public function getParams()
    {
        $data['params'] = array(
            'activity_id' => ['type'=>'int', 'valid'=>'sometimes|required|integer', 'default'=>'', 'example'=>'', 'description'=>'活动id'],
            'item_id' => ['type'=>'int', 'valid'=>'required|integer', 'default'=>'', 'example'=>'', 'description'=>'参加活动的商品id'],
            'valid' => ['type'=>'bool', 'valid'=>'sometimes|required|boolean', 'default'=>'', 'example'=>'', 'description'=>'活动状态'],
        );
        return $data;
    }
    public function getInfo($params)
    {
        $data = array();
        $objItemActivity = kernel::single('syspromotion_activity');
        if($params['valid'])
        {
            $itemFilter['item_id'] = $params['item_id'];
            $itemFilter['start_time|lthan'] = time();
            $itemFilter['end_time|than'] = time();
            $data = $objItemActivity->getItemInfo('*', $itemFilter);
            if($data)
            {
                $data['activity_info'] = $objItemActivity->getInfo('*', array('activity_id'=>$data['activity_id']));
            }
        }
        else
        {
            $itemFilter['item_id'] = $params['item_id'];
            $itemFilter['activity_id'] = $params['activity_id'];
            if($itemFilter['activity_id'])
            {
                $data = $objItemActivity->getItemInfo('*', $itemFilter);
                $data['activity_info'] = $objItemActivity->getInfo('*', array('activity_id'=>$params['activity_id']));
            }
        }
        return $data;
    }
}


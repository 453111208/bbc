<?php
class sysshoppubt_api_count{
    public $apiDescription = "交易咨询统计";
    public function getParams()
    {
        $return['params'] = array(
            'item_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'交易id'],
            'shop_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'企业id'],
            'user_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'消费者id'],
            'type' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'咨询类型'],
            'types' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'交易类型'],
        );
        return $return;
    }
    public function countAsk($params)
    {
        if($params['item_id'])
        {
            $filter['item_id'] = intval($params['item_id']);
            $filter['is_display'] = 'true';
        }
        if($params['shop_id'])
        {
            $filter['shop_id'] = ($params['shop_id']);
        }
        if($params['types'])
        {
            $filter['type'] = ($params['types']);
        }
        $objMdlConsultation = app::get('sysshoppubt')->model('consultation');
        $lists = $objMdlConsultation->getList('consultation_id,consultation_type',$filter);
        $count= array(
            'all' => 0,
            'item' => 0,
            'store_delivery' => 0,
            'payment' => 0,
            'invoice' => 0,
        );
        foreach($lists as $k=>$v)
        {
            $count['all'] +=1;
            if($v['consultation_type'] == "item")
            {
                $count['item'] +=1;
            }
            elseif($v['consultation_type'] == "store_delivery")
            {
                $count['store_delivery'] +=1;
            }
            elseif($v['consultation_type'] == "payment")
            {
                $count['payment'] +=1;
            }
            elseif($v['consultation_type'] == "invoice")
            {
                $count['invoice'] +=1;
            }
        }
        return $count;
    }
}

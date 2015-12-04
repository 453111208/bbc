<?php
class sysshop_api_shop_getCatFee{
    public $apiDescription = "获取店铺关联的类目费率";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
        );
        return $return;
    }
    public function getCatFee($params)
    {
        $filter['shop_id'] = $params['shop_id'];
        $catRows = "cat_id,fee_confg";
        $objMdlRelCat = app::get('sysshop')->model('shop_rel_lv1cat');
        $cats = $objMdlRelCat->getList($catRows,$filter);
        if(!$cats)  return array();

        foreach($cats as $value)
        {
            $catId[] = $value['cat_id'];
            $feeConf[] = unserialize($value['fee_confg']);
        }
        $catParams = array(
            'cat_id' => implode(',',$catId),
            'fields' => 'cat_name,cat_id,cat_path',
        );
        $catList = app::get('sysshop')->rpcCall('category.cat.get',$catParams,'seller');

        foreach($feeConf as $k=>$fee)
        {
            foreach($fee as $key=>$value)
            {
                $data[$key][$key]['cat_id'] = $key;
                $data[$key][$key]['cat_name'] = $catList[$key]['cat_name'];
                $data[$key][$key]['cat_fee'] = $value['lvfee'];
                unset($data[$key]['lvfee']);
                foreach ($value as $ck2 => $va)
                {
                    $data[$key][$ck2][$ck2]['cat_id'] = $ck2;
                    $data[$key][$ck2][$ck2]['cat_name'] = $catList[$key]['lv2'][$ck2]['cat_name'];
                    $data[$key][$ck2][$ck2]['cat_fee'] = $va['lv2fee'];
                    unset($data[$key]['lvfee']);
                    foreach ($va as $ck3 => $v)
                    {
                        $data[$key][$ck2][$ck3]['cat_id'] = $ck3;
                        $data[$key][$ck2][$ck3]['cat_name'] = $catList[$key]['lv2'][$ck2]['lv3'][$ck3]['cat_name'];
                        $data[$key][$ck2][$ck3]['cat_fee'] = $v;
                        unset($data[$key][$ck2]['lv2fee']);
                    }
                }
            }
        }
        return $data;
    }
}

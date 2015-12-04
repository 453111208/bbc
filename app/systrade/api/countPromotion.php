<?php
class systrade_api_countPromotion{

    public $apiDescription = '获取某促销的使用次数';
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'promotion_id' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'促销id'],
            // 'user_id' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'用户id'],
        );
        return $return;
    }

    public function countPromotion($params)
    {
        if($oauth = $params['oauth'])
        {
            $user_id = $oauth['account_id'];
        }

        $objMdlPromDetail = app::get('systrade')->model('promotion_detail');
        $filter = array('promotion_id'=>$params['promotion_id'], 'user_id'=>$user_id);
        $tids = $objMdlPromDetail->getList('tid', $filter);
        $objMdlTrade = app::get('systrade')->model('trade');
        $tidWithProm = array();
        foreach($tids as $v)
        {
            $tradeInfo = $objMdlTrade->getRow('status',array('tid'=>$v['tid']));
            if($tradeInfo['status'] != 'TRADE_CLOSED_BY_SYSTEM')
            {
                $tidWithProm[] = $v['tid'];
            }
        }
        $uniqueTid =array_unique($tidWithProm);
        return count($uniqueTid);
    }
}

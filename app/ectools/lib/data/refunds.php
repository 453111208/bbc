<?php
class ectools_data_refunds{
    public function create($params)
    {
        if($params['money'])
        {
            $params['cur_money'] = $params['money'];
        }
        $params['status'] = "succ";
        $params['refund_id'] =time();
        $objMdlRefunds = app::get('ectools')->model('refunds');
        $result = $objMdlRefunds->save($params);
        if(!$result)
        {
            throw new \LogicException("创建件退款单失败");
            return false;
        }
        return $result;
    }
}

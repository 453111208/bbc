<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysclearing_mdl_settlement_detail extends dbeav_model {

  
    public function _filter($filter)
    {
        if($filter['timearea'])
        {
            $timeArray = explode('-', $filter['timearea']);
            $filter['settlement_time|than']  = strtotime($timeArray[0]);
            $filter['settlement_time|lthan'] = strtotime($timeArray[1]);
            unset($filter['timearea']);
        }
        return parent::_filter($filter);
    }
}


<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

class base_mdl_rpcpoll extends dbeav_model{
    
    public function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderby=null)
    {
        if ($orderby)
            $orderby .= ", calltime DESC";
        else
            $orderby = "calltime DESC";
        $rpc_lists = parent::getList($cols,$filter,$offset,$limit,$orderby);
        if ($rpc_lists)
        {
            foreach ($rpc_lists as &$rpc_info)
            {
                if ($rpc_info['result'])
                {
                    $rpc_info['result'] = unserialize($rpc_info['result']);
                    if ($rpc_info['result'])
                        $rpc_info['result'] = "rsp:" . $rpc_info['result']['rsp'] . ", msg_id:" . $rpc_info['result']['msg_id'] . ", res:" . $rpc_info['result']['res'];
                }
            }
        }
        
        return $rpc_lists;
    }
}

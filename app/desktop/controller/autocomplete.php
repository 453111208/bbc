<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_ctl_autocomplete extends base_routing_controller
{
    function index()
    {
        $params = input::get('params');
        $params = explode(':',$params);
        $svckey = $params[0];
        $cols = explode(',',$params[1]);
        $key = input::get($cols[0]);
        $autocomplete = kernel::servicelist('autocomplete.'.$svckey);
        foreach($autocomplete as $service)
        {
            $return = $service->get_data($key,$cols);
        }
        echo "window.autocompleter_json=".json_encode($return)."";
    }

}

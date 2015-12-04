<?php
class system_ctl_getApiJson
{
    public function index($api)
    {
        $api = input::get('api');
        if($api != null)
        {

            $tools = kernel::single('system_prism_apiJson');
            $json = $tools->getJson();
            echo $json[$api];
        }
    }
}

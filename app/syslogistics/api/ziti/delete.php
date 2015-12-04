<?php

class syslogistics_api_ziti_delete {

    public $apiDescription = "删除自提点";

    public function getParams()
    {
        $return['params'] = array(
            'id' =>['type'=>'string','valid'=>'required', 'description'=>'自提点ID','default'=>'','example'=>'10'],
        );

        return $return;
    }

    public function delete($params)
    {
        $objMdlZiti = app::get('syslogistics')->model('ziti');
        return $objMdlZiti->delete(['id'=>$params['id']]);
    }
}


<?php

class syslogistics_api_ziti_get {

    public $apiDescription = "根据自提ID，获取单条自提点信息";

    public function getParams()
    {
        $return['params'] = array(
            'id' =>['type'=>'string','valid'=>'required', 'description'=>'自提点ID','default'=>'','example'=>'10'],
        );

        return $return;
    }

    public function get($params)
    {
        $objMdlZiti = app::get('syslogistics')->model('ziti');
        $data = $objMdlZiti->getRow('*', ['id'=>$params['id']]);
        $data['area_id'] = $data['area'];
        $data['area'] = area::getSelectArea($data['area'],'');
        return $data;
    }
}


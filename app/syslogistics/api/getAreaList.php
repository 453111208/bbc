<?php
class syslogistics_api_getAreaList{

    public $apiDescription = "获取地区数据";
    public function getParams()
    {
        $return['params'] = array(
            'area' =>['type'=>'string','valid'=>'required', 'description'=>'地区id集合，逗号隔开','default'=>'','example'=>'1'],
        );
        return $return;
    }
    public function getList($params)
    {
        try{
            if(!$params['area']) throw new Exception('地区id不能为空');
           $areaData =  area::getSelectArea($params['area']);
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
        return $areaData;
    }
}

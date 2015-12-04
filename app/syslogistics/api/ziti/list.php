<?php

class syslogistics_api_ziti_list {

    public $apiDescription = "根据收货地区id获取自提点列表";

    public function getParams()
    {
        $return['params'] = array(
            'area_id' =>['type'=>'string','valid'=>'required', 'description'=>'收货地区id','default'=>'','example'=>'110100,110101'],
        );

        return $return;
    }

    public function get($params)
    {
        if( !area::checkArea($params['area_id']) )
        {
            throw new LogicException('请选择正确地区');
        }

        $objMdlZiti = app::get('syslogistics')->model('ziti');
        $areaIds = explode(',',$params['area_id']);
        if( count($areaIds) == 2 )
        {
            $list = $objMdlZiti->getList('*', ['area_city_id'=>$areaIds[0]]);
        }
        else
        {
            $list = $objMdlZiti->getList('*', ['area_state_id'=>$areaIds[0],'area_city_id'=>$areaIds[1]]);
        }
        if( empty($list) ) return array();

        $data = array();
        $tmpData = array();
        foreach( $list as $key=>$row )
        {
            if( (count($areaIds) == 2 && $row['area_district_id'] == $areaIds[1]) || $row['area_district_id'] == $areaIds[2] )
            {
                $row['area'] = area::getSelectArea($row['area'],'');
                $tmpData[$key] = $row;
                unset($list[$key]);
            }
            else
            {
                $row['area'] = area::getSelectArea($row['area'],'');
                $data[$key] = $row;
            }
        }

        $data = array_merge($tmpData, $data);
        return $data;
    }
}


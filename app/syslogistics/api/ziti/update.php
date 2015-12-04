<?php

class syslogistics_api_ziti_update {

    public $apiDescription = "根据自提ID，修改自提点数据";

    public function getParams()
    {
        $return['params'] = array(
            'id' =>['type'=>'string','valid'=>'required', 'description'=>'自提点ID','default'=>'','example'=>'10'],
            'name' =>['type'=>'string','valid'=>'max:20', 'description'=>'自提点名称','default'=>'','example'=>'商派自提点'],
            'area_id' =>['type'=>'string','valid'=>'', 'description'=>'自提地区ID','default'=>'','example'=>'430000,430000,430102'],
            'addr' =>['type'=>'string','valid'=>'max:50', 'description'=>'自提详细地址','default'=>'','example'=>'桂林路396号2号楼'],
            'tel' =>['type'=>'string','valid'=>'', 'description'=>'自提点联系方式','default'=>'','example'=>'13918765432'],
        );

        return $return;
    }

    public function update($params)
    {
        $objMdlZiti = app::get('syslogistics')->model('ziti');
        $data = $objMdlZiti->getRow('id,name,area', ['id'=>$params['id']] );
        if( empty($data) ) throw new LogicException('更新的自提点不存在');

        if( $params['area_id'] && $data['area'] != $params['area_id'] && !area::checkArea($params['area_id']) )
        {
            throw new LogicException('请选择完整地区');
        }

        if( trim($params['name']) && $data['name'] != trim($params['name']) )
        {
            $name = $objMdlZiti->getRow('id', ['name'=>trim($params['name'])] );
            if( $name && $name['id'] != $params['id']  )
            {
                throw new LogicException('更新的自提点名称重复');
            }
            $updata['name'] = trim($params['name']);
        }

        if( $params['area_id'] )
        {
            $areaIds = explode(',', $params['area_id']);
            if( count($areaIds) == 2 )
            {
                $updata['area_state_id'] = 1;
                $updata['area_city_id'] = $areaIds[0];
                $updata['area_district_id'] = $areaIds[1];
            }
            else
            {
                $updata['area_state_id'] = $areaIds[0];
                $updata['area_city_id'] = $areaIds[1];
                $updata['area_district_id'] = $areaIds[2];
            }

            $updata['area'] = $params['area_id'];
        }

        if( trim($params['addr'])  )
        {
            $updata['addr'] = $params['addr'];
        }

        if( trim($params['tel']) )
        {
            $updata['tel'] = $params['tel'];
        }

        return $objMdlZiti->update($updata, ['id'=>$params['id']]);
    }
}


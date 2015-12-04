
<?php

/**
 * ShopEx licence
 * @author ajx
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class syslogistics_ctl_admin_ziti extends desktop_controller {

    public $workground = 'syslogistics.workground.logistics';


    /**
     * 物流公司列表
     * @var string $key
     * @var int $offset
     * @access public
     * @return int
     */
    public function index()
    {
        return $this->finder('syslogistics_mdl_ziti',array(
            'title' => app::get('syslogistics')->_('自提点列表'),
            'actions' => array(
                array(
                    'label'=>app::get('syslogistics')->_('添加自提点'),
                    'href'=>'?app=syslogistics&ctl=admin_ziti&act=edit',
                    'target'=>'dialog::{title:\''.app::get('syslogistics')->_('添加自提点').'\',  width:600,height:260}',
                ),
            ),
        ));
    }

    public function edit()
    {
        $pagedata['areaData'] = area::areaKvdata();
        $pagedata['areaPath'] = area::getAreaIdPath();

        if( input::get('id') )
        {
            $data = app::get('syslogistics')->rpcCall('logistics.ziti.get',['id'=>input::get('id')]);
            foreach( (array)explode(',',$data['area_id']) as $areaId)
            {
                if( $parentId )
                {
                    $areaData[$areaId] = $pagedata['areaPath'][$parentId];
                    $parentId = $areaId;
                }
                else
                {
                    $areaData[$areaId] = area::getAreaDataLv1();
                    $parentId = $areaId;
                }
            }
            $pagedata['selectArea'] = $areaData;

            $data['area'] = $data['area'].":".$data['area_id'];
            $pagedata['data'] = $data;
        }
        else
        {
            $pagedata['areaLv1'] = area::getAreaDataLv1();
        }

        return view::make('syslogistics/ziti/edit.html', $pagedata);
    }

    public function save()
    {
        $this->begin('?app=syslogistics&ctl=admin_ziti&act=index');

        $params['name'] = input::get('name');
        $params['addr'] = input::get('addr');
        $params['tel'] = input::get('tel');
        $areaIds = explode(':',input::get('area_id'));
        $params['area_id'] = $areaIds[1];
        if( !$params['area_id'] )
        {
            throw new LogicException('请选择自提地区');
        }

        try{
        if( input::get('id',false) )
        {
            $params['id'] = input::get('id');
            app::get('syslogistics')->rpcCall('logistics.ziti.update',$params);
        }
        else
        {
            app::get('syslogistics')->rpcCall('logistics.ziti.add',$params);
        }
        }catch( LogicException $e){
            $this->end(false, $e->getMessage());
        }

        $this->end(true, app::get('syslogistics')->_('操作成功'));
    }

    public function setting()
    {
        if( $_POST )
        {
            app::get('syslogistics')->setConf('syslogistics.ziti.open',$_POST['open']);
            $pagedata['open'] = $_POST['open'];
        }
        else
        {
            $pagedata['open'] = app::get('syslogistics')->getConf('syslogistics.ziti.open');
        }
        return $this->page('syslogistics/ziti/setting.html', $pagedata);

    }
}


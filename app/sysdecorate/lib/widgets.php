<?php

class sysdecorate_widgets {

    public function __construct()
    {
        $this->objMdlWidgetsInstance = app::get('sysdecorate')->model('widgets_instance');
    }

    public function config()
    {
        //common 通用模块
        $config['pc']['common']['title'] = '通用模块';

        $config['pc']['common']['params']['nav']['title'] = '导航菜单配置';//店铺导航挂件
        $config['pc']['common']['params']['nav']['dialog'] = ['default','add'];//店铺导航挂件

        $config['pc']['common']['params']['shopsign']['title'] = '店铺招牌配置';
        $config['pc']['common']['params']['shopsign']['dialog'] = ['default'];//店铺招牌

        //index 店铺首页
        $config['pc']['index']['title'] = '店铺首页';
        $config['pc']['index']['params']['custom']['title'] = '自定义区域配置';
        $config['pc']['index']['params']['custom']['dialog'] = ['default'];//自定义HTMl挂件

        $config['pc']['index']['params']['showitems']['title'] = '多商品展示配置';
        $config['pc']['index']['params']['showitems']['dialog'] = ['default','add'];//商品展示TAB挂件



        //wap端配置
        $config['wap']['params']['wapslider']['title'] = '轮播广告页面配置';
        $config['wap']['params']['wapslider']['dialog'] = ['default'];//自定义HTMl挂件
        $config['wap']['params']['wapslider']['sort'] = 1;

        $config['wap']['params']['waptags']['title'] = '标签配置';
        $config['wap']['params']['waptags']['dialog'] = ['default','add'];//商品展示TAB挂件
        $config['wap']['params']['waptags']['sort'] = 2;

        $config['wap']['params']['wapshowitems']['title'] = '商品展示配置';
        $config['wap']['params']['wapshowitems']['dialog'] = ['default','add'];
        $config['wap']['params']['wapshowitems']['sort'] = 3;

        $config['wap']['params']['wapimageslider']['title'] = '图片广告页配置';
        $config['wap']['params']['wapimageslider']['dialog'] = ['default','add'];
        $config['wap']['params']['wapimageslider']['sort'] = 4;

        $config['wap']['params']['wapcustom']['title'] = '自定义配置';
        $config['wap']['params']['wapcustom']['dialog'] = ['default'];
        $config['wap']['params']['wapcustom']['sort'] = 5;

        $config['wap']['params']['waplogo']['title'] = '店铺招牌配置';
        $config['wap']['params']['waplogo']['dialog'] = ['default'];
        $config['wap']['params']['waplogo']['sort'] = 6;


        return $config;
    }

    /**
     * 设置挂件临时配置参数
     *
     * @param int $widgetsId 挂件ID
     * @param string $widgetsName 挂件名称
     * @param int $shopId 店铺ID
     * @param array  $params  挂件配置参数
     *
     */
    public function setParams($widgetsId, $widgetsName, $dialogName, $params, $shopId)
    {
        if( empty($widgetsName) || empty($dialogName) )
        {
            throw new \LogicException(app::get('sysdecorate')->_('参数错误'));
        }

        //获取挂件类
        $classWigetsName = 'sysdecorate_widgets_'.$widgetsName;

        try{
            $objWidgets = kernel::single($classWigetsName);
        } catch (Exception $e) {
            #echo $e->getMessage();
        }

        //配置信息处理
        if( method_exists($objWidgets, 'setting') )
        {
            $widgetsParams = $this->getParams($widgetsName, $shopId);
            $settingParams = $objWidgets->setting($params, $widgetsParams['params'], $dialogName);
        }
        else
        {
            $settingParams = $params;
        }

        $this->widgetsParams[$widgetsName] = $settingParams;

        //app::get('sysdecorate')->setConf('widgetsParams'.$shopId,$this->widgetsParams);

        $data['shop_id'] = $shopId;
        $data['params'] = $settingParams;
        $data['widgets_type'] = $widgetsName;
        $data['modified_time'] = time();
        if( $widgetsId )
        {
            $data['widgets_id'] = $widgetsId;
        }

        return $this->objMdlWidgetsInstance->save($data);
    }

    /**
     * 获取挂件临时配置的参数
     *
     * @param string $widgetsName 挂件名称
     * @param int $shopId 店铺ID
     */
    private function getParams($widgetsName, $shopId)
    {
        //$this->widgetsParams = app::get('sysdecorate')->getConf('widgetsParams'.$shopId);

        $tmpParams = $this->objMdlWidgetsInstance->getList('widgets_id,shop_id,widgets_type,params', array('widgets_type'=>$widgetsName,'shop_id'=>$shopId));
        $this->widgetsParams = array_bind_key($tmpParams,'widgets_type');

        return $this->widgetsParams[$widgetsName] ? $this->widgetsParams[$widgetsName] : array();
    }

    /**
     * 获取挂件弹出框的页面数据
     *
     * @param string $widgetsName 挂件名称
     * @param string $dialogName  弹出框名称
     * @param int $shopId 店铺ID
     *
     */
    public function getDialogData($widgetsName, $dialogName, $shopId)
    {
        //获取挂件数据类
        $classWigetsName = 'sysdecorate_widgets_'.$widgetsName;
        try{
            $objWidgets = kernel::single($classWigetsName);
        } catch (Exception $e) {
            #echo $e->getMessage();
        }

        $params = $this->getParams($widgetsName, $shopId);

        if( $objWidgets )
        {
            $data = $objWidgets->getDialogData($params['params'], $dialogName, $shopId);
        }
        else
        {
            $data = $params['params'];
        }

        $return['data'] = $data;
        $return['widgets_id'] = $params['widgets_id'];
        return $return;
    }

    /**
     * 获取挂件的数据
     *
     * @param string $widgetsName 挂件名称
     * @param int $shopId 店铺ID
     */
    public function getWidgetsData($widgetsName, $shopId, $plat=null)
    {
        //获取挂件数据类
        $classWigetsName = 'sysdecorate_widgets_'.$widgetsName;
        try{
            $objWidgets = kernel::single($classWigetsName);
        } catch (Exception $e) {
            #echo $e->getMessage();
        }

        $params = $this->getParams($widgetsName, $shopId);

        if( $objWidgets && $params )
        {
            $data = $objWidgets->getData($params['params'], $shopId, $plat);
        }
        else
        {
            $data = $params['params'];
        }

        return $data;
    }

    /**
     * @brief 安装挂件
     *
     * @param string $tplFile 挂件挂在模版的页面
     * @param string $widgetsType 挂件名称
     * @param array  $params 挂件参数
     *
     * @return
     */
    public function install($tplFile, $widgetsType , $params, $shopId)
    {
        $tplFile = $tplFile ? $tplFile : 'index';//店铺首页
        $data['core_file'] = $tplFile;
        $data['widgets_type'] = $widgetsType;
        $data['shop_id'] = $shopId;
        $data['params'] = serialize($this->widgetsParams);
        $data['modified_time'] = time();
        $this->objLibInstance->insert($data);
        return true;
    }

    /**
     * 设置挂件临时配置参数
     *
     * @param int $widgetsId 挂件ID
     * @param string $widgetsName 挂件名称
     * @param int $shopId 店铺ID
     * @param array  $params  挂件配置参数
     *
     */
    public function saveWap($widgetsId, $widgetsName, $params)
    {
        $shopId = shopAuth::getShopId();

        if( empty($widgetsName) )
        {
            throw new \LogicException(app::get('sysdecorate')->_('参数错误'));
        }

        $data['shop_id'] = $shopId;
        $data['params'] = $params;
        $data['widgets_type'] = $widgetsName;
        $data['modified_time'] = time();
        if( $widgetsId )
        {
            $widgetsInfo = $this->objMdlWidgetsInstance->getList('params', array('widgets_id|noequal'=>$widgetsId,'widgets_type'=>$widgetsName,'shop_id'=>$shopId));
            //echo '<pre>';print_r($widgetsInfo);exit();
            foreach ($widgetsInfo as $key => $value)
            {
               $tagsname[$key] = $value['params']['tagsname'];
            }
            if(in_array($params['tagsname'],$tagsname))
            {
                throw new \LogicException(app::get('sysdecorate')->_('挂件名称已经存在'));
            }
            $data['widgets_id'] = $widgetsId;
        }
        else
        {
            $widgetsInfo = $this->objMdlWidgetsInstance->getList('params',array('widgets_type'=>$widgetsName,'shop_id'=>$shopId));
            foreach ($widgetsInfo as $key => $value)
            {
               $tagsname[$key] = $value['params']['tagsname'];
            }

            if($params['tagsname'] && in_array($params['tagsname'],$tagsname))
            {
                throw new \LogicException(app::get('sysdecorate')->_('挂件名称已经存在'));
            }
        }
        if($data['params']=='')
        {
            return $this->objMdlWidgetsInstance->delete(array('shop_id'=>$data['shop_id'],'widgets_id'=>$data['widgets_id']));
        }

        return $this->objMdlWidgetsInstance->save($data);
    }
    //获取wap端的配置信息
    public function getWapInfo($widgetsName, $shopId,$widgetsId)
    {
        if( !$shopId ) $shopId = shopAuth::getShopId();

        $filter = array('widgets_type'=>$widgetsName,'shop_id'=>$shopId,'widgets_id'=>$widgetsId);
        $tmpParams = $this->objMdlWidgetsInstance->getList('widgets_id,shop_id,widgets_type,params,modified_time', $filter);
        return $tmpParams;
    }
    //wap端挂件删除
    public function delWapInfo($widgetsId,$shopId,$widgetsName)
    {
        if( !$shopId ) $shopId = shopAuth::getShopId();
        if(!$widgetsId)
        {
            throw new \LogicException(app::get('sysdecorate')->_('挂件id不能为空!'));
        }
        $reault = $this->objMdlWidgetsInstance->delete(array('widgets_id'=>$widgetsId,'shop_id'=>$shopId));
        if($reault)
        {
            if($widgetsName=='waptags')
            {
                $sort = unserialize(app::get('topshop')->getConf('wap_decorate.tagSort'));
                if(is_array($widgetsId))
                {
                    foreach ($widgetsId as $key => $value)
                    {
                        unset($sort[$value]);
                    }
                    app::get('topshop')->setConf('wap_decorate.tagSort',serialize($sort));
                }
                else
                {
                    unset($sort[$widgetsId]);
                    app::get('topshop')->setConf('wap_decorate.tagSort',serialize($sort));
                }
            }
            elseif($widgetsName=='wapshowitems')
            {
                $sort = unserialize(app::get('topshop')->getConf('wap_decorate.showItemSort'));
                if(is_array($widgetsId))
                {
                    foreach ($widgetsId as $key => $value)
                    {
                        unset($sort[$value]);
                    }
                    app::get('topshop')->setConf('wap_decorate.showItemSort',serialize($sort));
                }
                else
                {
                    unset($sort[$widgetsId]);
                    app::get('topshop')->setConf('wap_decorate.showItemSort',serialize($sort));
                }
            }
        }
    }

}

<?php
class topshop_ctl_shop_setting extends topshop_controller{

    public function index()
    {
        $shopdata = app::get('topshop')->rpcCall('shop.get',array('shop_id'=>shopAuth::getShopId()),'seller');
        $pagedata['shop'] = $shopdata;
        $this->contentHeaderTitle = app::get('topshop')->_('店铺设置');
        return $this->page('topshop/shop/setting.html', $pagedata);
    }

    public function saveSetting()
    {
        $postData = input::get();
        try
        {
            $result = app::get('topshop')->rpcCall('shop.update',$postData);
            if( $result )
            {
                $msg = app::get('topshop')->_('设置成功');
                $result = 'success';
            }
            else
            {
                $msg = app::get('topshop')->_('设置失败');
                $result = 'error';
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            $result = 'error';
        }
        $url = url::action('topshop_ctl_shop_setting@index');
        return $this->splash($result,$url,$msg,true);

    }

}



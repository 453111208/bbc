<?php

/**
 * @brief 商家商品管理
 */
class topshop_ctl_open extends topshop_controller {

    public function index()
    {
        $shopId = $this->shopId;
        $this->contentHeaderTitle = app::get('topshop')->_('开发者中心');

        $requestParams = ['shop_id'=>$shopId];
        $openInfo = app::get('topshop')->rpcCall('open.shop.develop.info', $requestParams);
        $shopConf = app::get('topshop')->rpcCall('open.shop.develop.conf', $requestParams);
        $pagedata['openInfo'] = $openInfo;
        $pagedata['shopConf'] = $shopConf;

        return $this->page('topshop/open/index.html', $pagedata);
    }

    public function applyForOpen()
    {
        $shopId = $this->shopId;
        $requestParams = ['shop_id'=>$shopId];
        $res = app::get('topshop')->rpcCall('open.shop.develop.apply', $requestParams);
        redirect::action('topshop_ctl_open@index')->send();exit;
    }

    public function setConf()
    {
        $shopId = $this->shopId;
        $confs = input::get();

        try
        {
            $requestParams = [
                'shop_id' => $shopId,
                'developMode' => $confs['developer'] ? $confs['developer'] : 'PRODUCT',
                ];
            app::get('topshop')->rpcCall('open.shop.develop.setConf', $requestParams);
            $ret = ['result'=>'修改成功'];
        }
        catch(Exception $e)
        {
            $ret = ['result'=>$e->getMessage()];
        }
        return response::json($ret);exit;
    }

}



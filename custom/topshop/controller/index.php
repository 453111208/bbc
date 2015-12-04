<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topshop_ctl_index extends topshop_controller {

	public function index()
	{
        $shopId = $this->shopId;
		//获取企业数据
        $params = array(
            'shop_id' => $shopId,
            'fields' =>'shop_id,shop_name,shop_type,shop_logo,shop_descript,status,brand.brand_name,cat.cat_id,cat.cat_name',
        );

		$shopInfo = app::get('topshop')->rpcCall('shop.get.detail',$params,'seller');
        //获取企业入驻信息以及入驻佣金  类目
        if($shopInfo['shop']['shop_type']!='self')
        {
        	$shopCatInfo = app::get('topshop')->rpcCall('shop.get.cat.fee',array('shop_id'=>$shopId),'seller');
			$pagedata['shopCatInfo'] = $shopCatInfo;
        }

		//业务提想没有付款的订单数量
        $countUnTradeFee = app::get('topshop')->rpcCall('trade.count',array('shop_id'=>$shopId,'status'=>'WAIT_BUYER_PAY'));
		//业务提想等代发货的订单数量
        $countReadysSend = app::get('topshop')->rpcCall('trade.count',array('shop_id'=>$shopId,'status'=>'WAIT_SELLER_SEND_GOODS'));
		//业务提想等代收货的的订单数量
        $countReadyRec = app::get('topshop')->rpcCall('trade.count',array('shop_id'=>$shopId,'status'=>'WAIT_BUYER_CONFIRM_GOODS'));

		//获取企业上架商品数量
        $countShopOnsaleItem = app::get('topshop')->rpcCall('item.count',array('shop_id'=>$shopId,'status'=>'onsale'));
		//获取企业下架商品数量
        $countShopInstockItem = app::get('topshop')->rpcCall('item.count',array('shop_id'=>$shopId,'status'=>'instock'));

		//昨日数据
		$yesterdayInfo = $this->getAverPrice('yesterday');
		//前日数据
		$beforInfo = $this->getAverPrice('beforday');
		//本周数据
		$weekInfo = $this->getAverPrice('week');
		//本月数据
		$monthInfo = $this->getAverPrice('month');

		$pagedata['countShopOnsaleItem'] = $countShopOnsaleItem;
		$pagedata['countShopInstockItem'] = $countShopInstockItem;
		$pagedata['countUnTradeFee'] = $countUnTradeFee;
		$pagedata['countReadysSend'] = $countReadysSend;
		$pagedata['countReadyRec'] = $countReadyRec;
		$pagedata['shop'] = $shopInfo['shop'];
		$pagedata['shopBrandInfo'] = $shopInfo['brand'];
		$pagedata['yesterday'] = $yesterdayInfo;
		$pagedata['beforInfo'] = $beforInfo;
		$pagedata['weekInfo'] = $weekInfo;
		$pagedata['monthInfo'] = $monthInfo;

		$this->contentHeaderTitle = app::get('topshop')->_('我的工作台');
		return $this->page('topshop/index.html', $pagedata);
	}

	/**
	 * 获取平均客单价
	 * @param data
	 * @return data
	 */
    public function getAverPrice($data)
    {
        switch($data)
        {
        case "yesterday":
            $stattime = strtotime(date("Y-m-d", time()-86400) . ' 00:00:00');
            $filterType = "nequal";
            break;
        case "beforday":
            $stattime = strtotime(date("Y-m-d", time()-86400*2) . ' 00:00:00');
            $filterType = "nequal";
            break;
        case "week":
            $stattime = strtotime(date("Y-m-d", time()-86400*7) . ' 00:00:00');
            $filterType = "bthan";
            break;
        case "month":
            $stattime = strtotime(date("Y-m-d", time()-86400*30) . ' 00:00:00');
            $filterType = "bthan";
            break;
        }
        $filter = array(
            'shop_id' => $this->shopId,
            'type' => $filterType,
            'createtime' => $stattime,
        );
        $getData = app::get('topshop')->rpcCall('stat.trade.data.count.get',$filter);

		$data = array();
        foreach ($getData as $key => $value)
        {
			$data['shop_id'] =$value['shop_id'];
			$data['new_trade'] +=$value['new_trade'];
			$data['new_fee'] +=$value['new_fee'];
			$data['ready_trade'] +=$value['ready_trade'];
			$data['ready_fee'] +=$value['ready_fee'];
			$data['ready_send_trade'] +=$value['ready_send_trade'];
			$data['ready_send_fee'] +=$value['ready_send_fee'];
			$data['already_send_trade'] +=$value['already_send_trade'];
			$data['already_send_fee'] +=$value['already_send_fee'];
			$data['cancle_trade'] +=$value['cancle_trade'];
			$data['complete_trade'] +=$value['complete_trade'];
			$data['complete_fee'] +=$value['complete_fee'];
			$data['createtime'] =$value['createtime'];
		}

		if($data['new_trade']==0)
		{
			$data['averPrice'] = 0;
		}
		else
		{
			$data['averPrice'] = number_format($data['new_fee'] / $data['new_trade'], 2, '.',' ');
		}
		return $data;
	}

	/**
	 * 判断浏览器
	 * @param null
	 * @return null
	 */
	public function browserTip()
	{
		return $this->page('topshop/common/browser_tip.html');
	}

    public function feedback()
    {
        $status = 'success';
        $msg = '提交成功';

        try
        {
            app::get('topshop')->rpcCall('feedback.add', input::get(), 'seller');
        }
        catch (LogicException $e)
        {
            $msg = $e->getMessage();
            $status = 'error';
        }

        return $this->splash($status,$url,$msg,true);
    }

    public function nopermission()
    {
        $pagedata['url'] = input::get('next_page', request::server('HTTP_REFERER'));
        return view::make('topshop/permission.html',$pagedata);
    }

    public function onlySelfManagement()
    {
        $pagedata['url'] = input::get('next_page', request::server('HTTP_REFERER'));
        return view::make('topshop/onlySelfManagement.html',$pagedata);
    }
}

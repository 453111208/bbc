<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topshop_ctl_sysstat_sysstat extends topshop_controller
{
	/**
	 * 根据时间shopid获取商家运营情况
	 * @param null
	 * @return array
	 */
	public function index()
	{
		$sendtype = input::get();
		$type = $sendtype['sendtype'];
		if(!$type || !in_array($type,array('yesterday','beforday','week','month')))
		{
			$type='yesterday';
		}
		$postSend['sendtype'] = $type;
		//api参数
		$all = $this->__getParams('all',$postSend,'trade');

		//获取交易数据
		$data = app::get('topshop')->rpcCall('sysstat.data.get',$all,'seller');
		//业务数据
		$sysDataInfo = $this->getDataInfo();
		//商品排行
		$sysItemInfo = app::get('topshop')->rpcCall('sysstat.data.get',array('inforType'=>'item','timeType'=>$type,'limit' =>5,'start'=>0),'seller');
		$pagedata['sysItemInfo'] = $sysItemInfo['sysTrade'];

		$pagedata['sysstat'] = $data['sysstat'];
		$pagedata['sendtype'] = $type;
		$pagedata['sysDataInfo'] = $sysDataInfo;
		$this->contentHeaderTitle = app::get('topshop')->_('运营报表-商家运营概况');
		return $this->page('topshop/sysstat/sysstat.html', $pagedata);
	}

	//获取页面下面数据
	private function getDataInfo()
	{
		//昨日数据
		$yesterday = app::get('topshop')->rpcCall('sysstat.data.get',array('inforType'=>'trade','timeType'=>'yesterday'),'seller');
		//前日数据
		$before = app::get('topshop')->rpcCall('sysstat.data.get',array('inforType'=>'trade','timeType'=>'beforday'),'seller');
		//本周数据
		$week = app::get('topshop')->rpcCall('sysstat.data.get',array('inforType'=>'trade','timeType'=>'week'),'seller');
		//本月数据
		$month = app::get('topshop')->rpcCall('sysstat.data.get',array('inforType'=>'trade','timeType'=>'month'),'seller');
		$data = array(
			'yesterday'=>$yesterday['sysstat'],
			'beforInfo'=>$before['sysstat'],
			'week'=>$week['sysstat'],
			'month'=>$month['sysstat'],
		);

		return $data;
	}

	/**
	 * 异步获取数据  图表用
	 * @param null
	 * @return array
	 */

	public function ajaxTrade()
	{
		$postData = input::get();
		//api的参数
		$all = $this->__getParams('graphall',$postData,'trade');
		$datas =  app::get('topshop')->rpcCall('sysstat.data.get',$all,'seller');

		return response::json($datas);
	}

	//api参数组织
	private function __getParams($type,$postSend,$objType,$data=null)
	{
		if($type=='all')
		{
			$params = array(
				'inforType'=>$objType,
				'timeType'=>$postSend['sendtype'],
				'starttime'=>$postSend['starttime'],
				'endtime'=>$postSend['endtime'],
				'dataType'=>$type
			);
		}
		elseif($type=='graphall')
		{
			$params = array(
				'inforType'=>$objType,
				'tradeType'=>$postSend['trade'],
				'timeType'=>$postSend['sendtype'],
				'starttime'=>$postSend['starttime'],
				'endtime'=>$postSend['endtime'],
				'dataType'=>$type
			);
		}
		return $params;
	}
}

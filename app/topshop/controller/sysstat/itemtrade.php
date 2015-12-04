<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topshop_ctl_sysstat_itemtrade extends topshop_controller
{
	/**
	 * 商品销售分析
	 * @param null
	 * @return null
	 */
	public function index()
	{
		$postSend = input::get();
		$type = $postSend['sendtype'];

		$objFilter = kernel::single('sysstat_data_filter');
		$params = $objFilter->filter($postSend);

		$itemtime = array('starttime'=>$postSend['itemtime']);
		if(!$postSend || !in_array($postSend['sendtype'],array('yesterday','beforday','week','month','selecttime')))
		{
			$type='yesterday';
		}
		$postSend['sendtype'] = $type;
		//api参数
		$all = $this->__getParams('all',$postSend,'item');
		$notAll = $this->__getParams('notall',$postSend,'item',$params);

		$itemInfo = app::get('topshop')->rpcCall('sysstat.data.get',$notAll,'seller');

		$topParams = array('inforType'=>'item','timeType'=>$type,'starttime'=>$postSend['itemtime'],'limit'=>5);
		$topFiveItem = app::get('topshop')->rpcCall('sysstat.data.get',$topParams,'seller');

		//获取页面显示的时间
		$pagetimes = app::get('topshop')->rpcCall('sysstat.datatime.get',$all,'seller');
		//api的参数
		$countAll = $this->__getParams('all',$postSend,'itemcount');
		//处理翻页数据
		$countData = app::get('topshop')->rpcCall('sysstat.data.get',$countAll,'seller');
		$count = $countData['count'];
		if($type == 'selecttime')
		{
			$pagetime = $pagetimes['before'];
		}
		else
		{
			$pagetime = $pagetimes;
		}

		if($count>0) $total = ceil($count/$params['limit']);
		$current = $postSend['pages'] ? $postSend['pages'] : 1;
		$pagedata['limits'] = $params['limit'];
		$pagedata['pages'] = $current;
		$postSend['pages'] = time();
		$pagedata['pagers'] = array(
			'link'=>url::action('topshop_ctl_sysstat_itemtrade@index',$postSend),
			'current'=>$current,
			'total'=>$total,
			'token'=>$postSend['pages']
		);
		$pagedata['sendtype'] = $type;
		$pagedata['itemInfo'] = $itemInfo['sysTrade'];
		$pagedata['pagetime'] = $pagetime;
		$pagedata['topFiveItem'] = $topFiveItem['sysTrade'];
		$this->contentHeaderTitle = app::get('topshop')->_('运营报表-商品销售分析');
		return $this->page('topshop/sysstat/itemtrade.html', $pagedata);
	}

	/**
	 * 异步获取数据  图表用
	 * @param null
	 * @return array
	 */

	public function ajaxTrade()
	{
		$postData = input::get();

		$orderBy = $postData['trade'].' '.'DESC';
		$postData['orderBy'] = $orderBy;
		$postData['limit'] = 10;

		$grapParams = $this->__getParams('itemgraphall',$postData,'item');
		$datas =  app::get('topshop')->rpcCall('sysstat.data.get',$grapParams,'seller');
		$ajaxdata = array('dataInfo'=>$data,'datas'=>$datas);

		return response::json($ajaxdata);
	}

	//api参数组织
	private function __getParams($type,$postSend,$objType,$data=null)
	{
		if($type=='all')
		{
			$params = array(
				'inforType'=>$objType,
				'timeType'=>$postSend['sendtype'],
				'starttime'=>$postSend['itemtime'],
			);
		}
		elseif($type=='notall')
		{
			$params = array(
				'inforType'=>$objType,
				'timeType'=>$postSend['sendtype'],
				'starttime'=>$postSend['itemtime'],
				'limit'=>$data['limit'],
				'start'=>$data['start']
			);
		}
		elseif($type=='itemgraphall')
		{
			$params = array(
				'inforType'=>$objType,
				'tradeType'=>$postSend['trade'],
				'timeType'=>$postSend['sendtype'],
				'starttime'=>$postSend['itemtime'],
				'dataType'=>$type,
				'limit'=>$postSend['limit'],
				'orderBy'=>$postSend['orderBy'],
			);
		}
		return $params;
	}

}

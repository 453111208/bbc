<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topshop_ctl_sysstat_stattrade extends topshop_controller
{
	public function index()
	{
		$postSend = input::get();
		$type     = $postSend['sendtype'];
		$objFilter = kernel::single('sysstat_data_filter');
		$params = $objFilter->filter($postSend);
		if($postSend['compare'])
		{
			$pagedata['compare'] = $postSend['compare'];
		}
		if(!$postSend || !in_array($postSend['sendtype'],array('yesterday','beforday','week','month','selecttime','select')))
		{
			$type='yesterday';
		}
		$postSend['sendtype'] = $type;
		//api参数
		$all = $this->__getParams('all',$postSend,'trade');
		$notAll = $this->__getParams('notall',$postSend,'trade',$params);
		//获取数据
		$data = app::get('topshop')->rpcCall('sysstat.data.get',$notAll,'seller');

		if($type=='selecttime')
		{
			$pagedata['sysstat'] = $data['sysstat'];
		}
		else
		{
			$pagedata['sysstat'] = $data['sysstat']['commonday'];
		}
		//获取页面显示的时间
		$pagetime = app::get('topshop')->rpcCall('sysstat.datatime.get',$all,'seller');

		//api的参数
		$countAll = $this->__getParams('notall',$postSend,'tradecount',$params);

		//处理翻页数据
		$countData = app::get('topshop')->rpcCall('sysstat.data.get',$countAll,'seller');
		$count = $countData['count'];

		$current = $postSend['pages'] ? $postSend['pages'] : 1;
		$pagedata['pages'] = $current;
		$pagedata['limits'] = $params['limit'];
		$postSend['pages'] = time();
		if($count>0) $total = ceil($count/$params['limit']);
		$pagedata['pagers'] = array(
			'link'=>url::action('topshop_ctl_sysstat_stattrade@index',$postSend),
			'current'=>$current,
			'total'=>$total,
			'token'=>$postSend['pages'],
		);
		$pagedata['sendtype'] = $type;
		$pagedata['pagetime'] = $pagetime;
		$pagedata['sysTradeData'] = $data['sysTradeData'];
		$this->contentHeaderTitle = app::get('topshop')->_('运营报表-交易数据分析');
		return $this->page('topshop/sysstat/stattrade.html', $pagedata);
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

	/*	$tradetype= $postData['trade'];
		$sendtype = $postData['sendtype'];
		$compare = $postData['compare'];

		if($sendtype=='selecttime')
		{
			$filter = array(
				'starttime'=>$postData['starttime'],
				'endtime'=>$postData['endtime']
			);
		}
		if($sendtype=='select')
		{
			$filter = array(
				'starttime' => $postData['starttime'],
			);
		}*/


		$datas =  app::get('topshop')->rpcCall('sysstat.data.get',$all,'seller');
		//echo '<pre>';print_r($datas);exit();
		return response::json($datas);

		/*if($sendtype == 'yesterday' || $sendtype == 'beforday')
		{
			$data = $dataInfo['commonday'];
		}
		else
		{
			$data = $dataInfo;
		}


		return response::json($datas);*/
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
		elseif($type=='notall')
		{
			$params = array(
				'inforType'=>$objType,
				'timeType'=>$postSend['sendtype'],
				'starttime'=>$postSend['starttime'],
				'endtime'=>$postSend['endtime'],
				'dataType'=>$type,
				'limit'=>$data['limit'],
				'start'=>$data['start']
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

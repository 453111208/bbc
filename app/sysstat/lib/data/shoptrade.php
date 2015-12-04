<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class sysstat_data_shoptrade
{

	/**
	 * 根据时间条件获取对应阶段商家的数据统计信息
	 * @param null
	 * @return null
	 */
	public function getTradeInfo($filter=array())
	{
		//$postFilter = $this->__check($filter);
		$tradeStaticMdl = app::get('sysstat')->model('trade_statics');
		$tradeData = $tradeStaticMdl->getList('*',$filter);
		return $tradeData;
	}

	/**
	 * 根据时间条件获取对应阶段商家的数据统计信息
	 * @param null
	 * @return null
	 */
	public function getTradeInfoList($filter=array())
	{

		//$postFilter = $this->__check($filter['filter']);
		$tradeStaticMdl = app::get('sysstat')->model('trade_statics');
		$rows       = '*';
		$rowsFilter = $filter['filter']  ? $filter['filter']  : null;
		$start      = $filter['start']   ? $filter['start']   : 0;
		$limit      = $filter['limit']   ? $filter['limit']   : -1;
		$orderBy    = 'createtime DESC';

		$tradeData = $tradeStaticMdl->getList($rows, $rowsFilter, $start, $limit, $orderBy);
		foreach ($tradeData as $key => $value) {
			if($value['new_trade']==0)
			{
				$tradeData[$key]['averPrice'] = 0;
			}
			else
			{
				$tradeData[$key]['averPrice'] = number_format($value['new_fee'] / $value['new_trade'], 2, '.', '');
			}
		}
		return $tradeData;
	}

	/**
	 * 根据时间条件获取对应阶段商家的数据总数
	 * @param null
	 * @return null
	 */
	public function getTradeCount($filter=array())
	{
		//$postFilter = $this->__check($filter);
		$itemStaticMdl = app::get('sysstat')->model('trade_statics');
		$itemData = $itemStaticMdl->count($filter);

		return $itemData;
	}

	/**
	 * 根据时间条件获取对应阶段商家的商品数据统计信息
	 * @param null
	 * @return null
	 */
	public function getItemStaticsInfo($filter=array())
	{

		//$postFilter = $this->__check($filter['filter']);
		$itemStaticMdl = app::get('sysstat')->model('item_statics');
		$rows       = '*';
		$rowsFilter = $filter['filter']  ? $filter['filter']  : null;
		$start      = $filter['start']   ? $filter['start']   : 0;
		$limit      = $filter['limit']   ? $filter['limit']   : -1;
		$orderBy    = $filter['orderBy'] ? $filter['orderBy'] : 'amountnum DESC';

		$itemData = $itemStaticMdl->getList($rows, $rowsFilter, $start, $limit, $orderBy);
		return $itemData;
	}

	/**
	 * 根据时间条件获取对应阶段商家的商品数据总数
	 * @param null
	 * @return null
	 */
	public function getItemCount($filter=array())
	{
		//$postFilter = $this->__check($filter);
		$itemStaticMdl = app::get('sysstat')->model('item_statics');
		$itemData = $itemStaticMdl->count($filter);

		return $itemData;
	}


	/**
	 * 检查条件
	 * @param null
	 * @return null
	 */
	private function __check($filter)
	{
		if(!$filter)
		{
			throw new \LogicException(app::get('sysstat')->_('赛选条件不能空!'));
		}
		if(!$filter['filter']['shop_id'])
		{
			throw new \LogicException(app::get('sysstat')->_('店铺id不能空!'));
		}
		return true;
	}

	/**
	 * 检查条件
	 * @param inforType string 类型条件 trade  item
	 * @param type string 类型条件 如：天 月 周
	 * @param filter string
	 * @return array data
	 */
	public function getTimeInfo($inforType,$type,$shopId,$filter=null,$limit = -1,$start = 0,$orderBy=null)
	{
		$statFilter = $this->__getTypeFilter($type,$shopId,$filter,$limit,$start,$orderBy);
		switch ($inforType) {
			case 'trade':
				return $this->getTypeTradeInfo($type,$statFilter);
				break;
			case 'tradecount':
				return $this->getTradeCount($this->checkCountFilter($type,$statFilter));
				break;
			case 'item':
				return $this->getTypeItemInfo($type,$statFilter,$orderBy);
				break;
			case 'itemcount':
				return $this->getItemCount($this->checkCountFilter($type,$statFilter));
				break;
		}
	}
	//获取总数的检查条件
	private function checkCountFilter($type,$statFilter)
	{
		if($type == 'yesterday' || $type == 'beforday')
		{
			$filter = $statFilter['commonday']['filter'];
		}
		elseif($type == 'selecttime')
		{
			$filter = $statFilter['before']['filter'];
		}
		else
		{
			$filter = $statFilter['filter'];
		}
		return $filter;
	}
	/**
	 * 检查条件
	 * @param type string 处理特殊类型的  如：时间段
	 * @param type filter 查询过滤条件
	 * @return array data
	 */
	private function getTypeTradeInfo($type,$filter)
	{
		if($type == 'selecttime')
		{
			$before = $this->getTradeInfoList($filter['before']);
			$after = $this->getTradeInfoList($filter['after']);
			$data = array(
				'before'=>$before,
				'after'=>$after
			);
		}
		elseif($type == 'yesterday' || $type == 'beforday')
		{
			$commonday = $this->getTradeInfoList($filter['commonday']);
			$beforeweek = $this->getTradeInfoList($filter['beforeweek']);
			$data = array(
				'commonday'=>$commonday,
				'beforeweek'=>$beforeweek
			);
		}
		else
		{
			$data = $this->getTradeInfoList($filter);
		}
		return $data;
	}

	private function getTypeItemInfo($type,$filter,$orderBy)
	{
		if($type == 'selecttime')
		{
			$itemInfo = $this->getItemStaticsInfo($filter['before']);
			$data=$itemInfo;
		}
		elseif($type == 'yesterday' || $type == 'beforday')
		{
			$data = $this->getItemStaticsInfo($filter['commonday']);
		}
		else
		{
			$data = $this->getItemStaticsInfo($filter);
		}
		return $data;
	}
	private function __getTypeFilter($type,$shopId,$filter,$limit,$start,$orderBy)
	{
		$filterTime = $this->__checkTime($type,$filter);

		switch ($type) {
			case 'yesterday':
				return array(
						'commonday'=>array(
							'filter'=>array('shop_id'=>$shopId,'createtime|nequal'=>$filterTime['yesterday']),
							'limit'=>$limit,
							'start'=>$start,
							'orderBy'=>$orderBy
							),
						'beforeweek'=>array(
							'filter'=>array('shop_id'=>$shopId,'createtime|nequal'=>$filterTime['beforeweek']),
							'limit'=>$limit,
							'start'=>$start,
							'orderBy'=>$orderBy
							),
						);
				break;
			case 'beforday':
				return array(
						'commonday'=>array(
							'filter'=>array('shop_id'=>$shopId,'createtime|nequal'=>$filterTime['beforday']),
							'limit'=>$limit,
							'orderBy'=>$orderBy
							),
						'beforeweek'=>array(
							'filter'=>array('shop_id'=>$shopId,'createtime|nequal'=>$filterTime['beforeweek']),
							'limit'=>$limit,
							'start'=>$start,
							'orderBy'=>$orderBy
							),
						);
				break;
			case 'beforeweek':
				return array(
						'filter'=>array('shop_id'=>$shopId,'createtime|bthan'=>$filterTime),
						'limit'=>$limit,
						'start'=>$start,
						'orderBy'=>$orderBy
						);
				break;
			case 'week':
				return array(
						'filter'=>array('shop_id'=>$shopId,'createtime|bthan'=>$filterTime),
						'limit'=>$limit,
						'start'=>$start,
						'orderBy'=>$orderBy
						);
				break;
			case 'month':
				return array(
						'filter'=>array('shop_id'=>$shopId,'createtime|bthan'=>$filterTime),
						'limit'=>$limit,
						'start'=>$start,
						'orderBy'=>$orderBy
						);
				break;
			case 'select':
				return array(
						'filter'=>array('shop_id'=>$shopId,'createtime|bthan'=>$filterTime['before']['start'],'createtime|sthan'=>$filterTime['before']['end']),
						'limit'=>$limit,
						'start'=>$start,
						'orderBy'=>$orderBy
						);
				break;
			case 'selecttime':
				return array(
							'before'=>array(
								'filter'=>array('shop_id'=>$shopId,'createtime|bthan'=>$filterTime['before']['start'],'createtime|sthan'=>$filterTime['before']['end']),
								'limit'=>$limit,
								'start'=>$start,
								'orderBy'=>$orderBy
							),
							'after'=>array(
								'filter'=>array('shop_id'=>$shopId,'createtime|bthan'=>$filterTime['after']['start'],'createtime|sthan'=>$filterTime['after']['end']),
								'limit'=>$limit,
								'start'=>$start,
								'orderBy'=>$orderBy
							),
						);
				break;
		}
	}

	public function __checkTime($type,$filter=null)
	{
		switch ($type) {
			case 'yesterday':
				return array('yesterday'=>strtotime(date('Y-m-d 00:00:00', strtotime('-1 day'))),
								'beforeweek'=>strtotime(date('Y-m-d 00:00:00', strtotime('-8 day')))
							);
				break;
			case 'beforday':
				return array('beforday'=>strtotime(date('Y-m-d 00:00:00', strtotime('-2 day'))),
								'beforeweek'=>strtotime(date('Y-m-d 00:00:00', strtotime('-9 day')))
							);
				break;
			case 'beforeweek':
				return strtotime(date('Y-m-d 00:00:00', strtotime('-8 day')));
				break;
			case 'week':
				return strtotime(date('Y-m-d 00:00:00', strtotime('-7 day')));
				break;
			case 'month':
				return strtotime(date('Y-m-d 00:00:00', strtotime('-30 day')));
				break;
			case 'selecttime':
				return $this->getSelectTime($filter);
				break;
			case 'select':
				return $this->getSelectTime($filter);
				break;
			case 'comparebefore':
				return strtotime(date('Y-m-d 00:00:00', strtotime('-3 day')));
				break;
			case 'compareweek':
				return strtotime(date('Y-m-d 00:00:00', strtotime('-8 day')));
				break;
		}
	}

	private function getSelectTime($filter)
	{
		$start = explode('-', $filter['starttime']);
		$end = explode('-', $filter['endtime']);
		$selectTime = array(
			'before'=>array('start'=>strtotime($start[0]),'end'=>strtotime($start[1])),
			'after'=>array('start'=>strtotime($end[0]),'end'=>strtotime($end[1]))
		);
		return $selectTime;
	}

	public function getStatInfo($getData,$type=null)
	{
		if($type=='yesterday' || $type=='beforday')
		{
			foreach ($getData['commonday'] as $key => $value)
			{
				foreach ($value as $k => $v)
				{
					$commonday[$k] += $v;
				}
				$commonday['shop_id'] =$value['shop_id'];
				$commonday['createtime'] =$value['createtime'];
			}
			$commonday['averPrice'] = number_format($commonday['new_fee'] / $commonday['new_trade'], 2, '.', '');
			foreach ($getData['beforeweek'] as $key => $value)
			{
				foreach ($value as $k => $v)
				{
					$beforeweek[$k] += $v;
				}
				$beforeweek['shop_id'] =$value['shop_id'];
				$beforeweek['createtime'] =$value['createtime'];
			}
			$beforeweek['averPrice'] = number_format($beforeweek['new_fee'] / $beforeweek['new_trade'], 2, '.', '');
			$data = array(
				'commonday'=>$commonday,
				'beforeweek'=>$beforeweek,
			);
		}
		elseif($type=='selecttime')
		{
			foreach ($getData['before'] as $key => $value)
			{
				foreach ($value as $k => $v)
				{
					$before[$k] += $v;
				}
				$before['shop_id'] =$value['shop_id'];
				$before['createtime'] =$value['createtime'];
			}
			$before['averPrice'] = number_format($before['new_fee'] / $before['new_trade'], 2, '.', '');
			foreach ($getData['after'] as $key => $value)
			{
				foreach ($value as $k => $v)
				{
					$after[$k] += $v;
				}
				$after['shop_id'] =$value['shop_id'];
				$after['createtime'] =$value['createtime'];
			}
			$after['averPrice'] = number_format($after['new_fee'] / $after['new_trade'], 2, '.', '');
			$data = array(
				'before'=>$before,
				'after'=>$after,
			);
		}
		else
		{
			foreach ($getData as $key => $value)
			{
				foreach ($value as $k => $v)
				{
					$commonday[$k] += $v;
				}
				$commonday['shop_id'] =$value['shop_id'];
				$commonday['createtime'] =$value['createtime'];
			}
			$commonday['averPrice'] = number_format($commonday['new_fee'] / $commonday['new_trade'], 2, '.', '');
			$data = array(
				'commonday'=>$commonday,
			);
		}

		return $data;
	}

	//图表的方法
	public function graphdata($type,$postdata,$compareData=null,$sendtype=null,$comparetime=null)
	{
		$datatTime = $this->getDefaultTime($sendtype);
		$comparetTime = $this->getDefaultTime($comparetime);
		if($postdata == null)
		{
			$postdata[]['createtime'] = $datatTime;
		}
		if($compareData == null &&($sendtype=='yesterday'||$sendtype=='beforday') && $comparetime!=null)
		{
			$compareData[]['createtime'] = $comparetTime;
		}

		$datainfo['label'] = $this->getLable($sendtype);

		foreach ($postdata as $key => $value)
		{
			$data[] = array(
					$value['createtime']* 1000 ,
					$value[$type] ? intval($value[$type]) : 0,
				);
		}
		$datainfo['data']=$data;
		if($compareData!=null)
		{
			if($sendtype=='yesterday')
			{
				if($comparetime=='comparebefore')
				{
					$labelTime = date('Y/m/d',strtotime(date("Y/m/d", time()-86400*2) . ' 00:00:00'));
				}
				if($comparetime=='compareweek')
				{
					$labelTime = date('Y/m/d',strtotime(date("Y/m/d", time()-86400*8) . ' 00:00:00'));
				}
			}
			if($sendtype=='beforday')
			{
				if($comparetime=='comparebefore')
				{
					$labelTime = date('Y/m/d',strtotime(date("Y/m/d", time()-86400*3) . ' 00:00:00'));
				}
				if($comparetime=='compareweek')
				{
					$labelTime = date('Y/m/d',strtotime(date("Y/m/d", time()-86400*9) . ' 00:00:00'));
				}
			}
			$compareinfo['label'] = $labelTime;
			foreach ($postdata as $key => $value) {
				$compare[] = array(
						$value['createtime']* 1000 ,
						$compareinfo[$key][$type] ? intval($compareinfo[$key][$type]) : 0,
					);
			}

			$compareinfo['data']=$compare;
			$fmt = array($datainfo,$compareinfo);
		}
		else
		{
			$fmt = array($datainfo);
		}
		return $fmt;
	}

	private function getDefaultTime($sendtype)
	{
		switch ($sendtype) {
			case 'yesterday':
				return strtotime(date("Y-m-d", time()-86400) . ' 00:00:00');
				break;
			case 'beforday':
				return strtotime(date("Y-m-d", time()-86400*2) . ' 00:00:00');
				break;
			case 'week':
				return strtotime(date("Y-m-d", time()-86400*7) . ' 00:00:00');
				break;
			case 'month':
				return strtotime(date("Y-m-d", time()-86400*30) . ' 00:00:00');
				break;
			case 'selectTime':
				return $this->getSelectTime($filter);
				break;
			case 'comparebefore':
				return strtotime(date("Y-m-d", time()-86400*3) . ' 00:00:00');
				break;
			case 'compareweek':
				return strtotime(date("Y-m-d", time()-86400*8) . ' 00:00:00');
				break;
		}
	}
	private function getLable($type)
	{
		$label = array(
			'yesterday'=>date('Y/m/d',strtotime(date("Y/m/d", time()-86400) . ' 00:00:00')),
			'beforday'=>date('Y/m/d',strtotime(date("Y/m/d", time()-86400*2) . ' 00:00:00')),
			'week'=>date('Y/m/d',strtotime(date("Y/m/d", time()-86400*7) . ' 00:00:00')).'-'.date('Y/m/d',strtotime(date("Y/m/d", time()-86400) . ' 00:00:00')),
			'month'=>date('Y/m/d',strtotime(date("Y/m/d", time()-86400*30) . ' 00:00:00')).'-'.date('Y/m/d',strtotime(date("Y/m/d", time()-86400) . ' 00:00:00')),
			'compareweek'=>date('Y/m/d',strtotime(date("Y/m/d", time()-86400*8) . ' 00:00:00')),
			'comparebefore'=>date('Y/m/d',strtotime(date("Y/m/d", time()-86400*2) . ' 00:00:00')),
		);
		return $label[$type];
	}

	public function compareData($data)
	{
		$compare = array();
		foreach ($data['after'] as $key => $value)
		{
			foreach ($value as $k => $v)
			{
				if($v-$data['before'][$key][$k]==0 || $data['before'][$key][$k]==0)
				{
					$compare[$key][$k] = 0;
				}
				else
				{
					if(($v-$data['before'][$key][$k])/$data['before'][$key][$k]==1)
					{
						$compare[$key][$k] = 100;
					}
					$compare[$key][$k] =sprintf("%.2f", ($v-$data['before'][$key][$k])/$data['before'][$key][$k]*100).'%';

				}
			}
		}

		$newData = array();
		if(count($data['before'])>count($data['after']))
		{
			foreach ($data['before'] as $key => $value)
			{
				$newData[$key]['before'] =$data['before'][$key];
				$newData[$key]['mid'] = $data['after'][$key];
				$newData[$key]['last'] = $compare[$key];
			}
		}
		else
		{
			foreach ($data['after'] as $key => $value)
			{
				$newData[$key]['before'] = $data['before'][$key];
				$newData[$key]['mid'] = $data['after'][$key];
				$newData[$key]['last'] = $compare[$key];
			}
		}

		return $newData;

	}


	//商家业务图表的方法
	public function tradegraphdata($type,$postdata,$sendtype=null,$filter=null)
	{
		$datatTime = $this->getDefaultTime($sendtype);
		if($postdata == null)
		{
			$postdata[]['createtime'] = $datatTime;
		}
		if($sendtype=='selecttime')
		{

			$beforeinfo['label'] = $filter['starttime'];
			foreach ($postdata['before'] as $key => $value)
			{
				$data[] = array(
						$value['createtime']* 1000 ,
						$value[$type] ? intval($value[$type]) : 0,
					);
			}
			$beforeinfo['data']=$data;

			$afterinfo['label'] = $filter['endtime'];

			foreach ($postdata['before'] as $key => $value)
			{
				$datas[] = array(
						$value['createtime']* 1000 ,
						$postdata['after'][$key][$type] ? intval($postdata['after'][$key][$type]) : 0,
					);
			}

			$afterinfo['data']=$datas;
			$fmt = array($beforeinfo,$afterinfo);
		}
		else
		{
			$datainfo['label'] = $this->getLable($sendtype)?$this->getLable($sendtype):$filter['starttime'];

			foreach ($postdata as $key => $value)
			{
				$data[] = array(
						$value['createtime']* 1000 ,
						$value[$type] ? intval($value[$type]) : 0,
					);
			}
			$datainfo['data']=$data;
			$fmt = array($datainfo);
		}
		return $fmt;
	}

	//商家商品图表的方法
	public function itemgraphdata($type,$postdata,$sendtype=null,$filter=null)
	{
		if($postdata == null)
		{
			for ($i=0; $i <10 ; $i++)
			{
				$data[] = array(
						$i,
						0,
					);
			}
		}
		if($filter)
		{
			$datainfo['label'] = $filter['starttime'];
		}
		else
		{
			$datainfo['label'] = $this->getLable($sendtype);
		}
		foreach ($postdata as $key => $value)
		{
			$data[] = array(
					$key,
					$value[$type] ? intval($value[$type]) : 0,
				);
		}
		$datainfo['data']=$data;
		$fmt = array($datainfo);
		return $fmt;
	}



	public function getPageTime($type,$selecttime)
	{
		switch ($type) {
			case 'yesterday':
				return date('Y/m/d',$selecttime['yesterday']).' - '.date('Y/m/d',$selecttime['yesterday']);
				break;
			case 'beforday':
				return date('Y/m/d',$selecttime['beforday']).' - '.date('Y/m/d',$selecttime['beforday']);
				break;
			case 'week':
				return date('Y/m/d',$selecttime).'-'.date('Y/m/d',strtotime(date("Y-m-d", time()) . ' 00:00:00'));
				break;
			case 'month':
				return date('Y/m/d',$selecttime).' - '.date('Y/m/d',strtotime(date("Y-m-d", time()) . ' 00:00:00'));
				break;
			case 'select':
				return date('Y/m/d',$selecttime['before']['start']).'-'.date('Y/m/d',$selecttime['before']['end']);
				break;
			case 'selecttime':
				return array('before'=>date('Y/m/d',$selecttime['before']['start']).'-'.date('Y/m/d',$selecttime['before']['end']),
							'after'=>date('Y/m/d',$selecttime['after']['start']).'-'.date('Y/m/d',$selecttime['after']['end'])
					);
				break;
		}
	}

	public function checkFilter(&$filter)
	{
		$selecttime = $this->getSelectTime($filter);
		$beforetime = $selecttime['before']['end']-$selecttime['before']['start'];

		$selecttime['after']['end'] = $selecttime['after']['start']+$beforetime;
		$filterTime = array(
			'starttime' =>date('Y/m/d',$selecttime['before']['start']).'-'.date('Y/m/d',$selecttime['before']['end']),
			'endtime' =>date('Y/m/d',$selecttime['after']['start']).'-'.date('Y/m/d',$selecttime['after']['end']),
		);
		return $filterTime;

	}


	//时间切片
	public function getData($sendtype,$type,$data)
	{
		if($sendtype=='selecttime')
		{
			if($type=='useday'){return $data;}
			if($type=='useweek')
			{
				$beforenewdata = array_chunk($data['before'],7);
				$afternewdata = array_chunk($data['after'],7);
				$beforedata = $this->getDataInfo($beforenewdata);
				$afterdata = $this->getDataInfo($afternewdata);
				$datas = array(
					'before'=>$beforedata,
					'after'=>$afterdata,
				);
				return $datas;
			}
			if($type=='usemonth')
			{
				$beforenewdata = array_chunk($data['before'],30);
				$afternewdata = array_chunk($data['after'],30);
				$beforedata = $this->getDataInfo($beforenewdata);
				$afterdata = $this->getDataInfo($afternewdata);
				$datas = array(
					'before'=>$beforedata,
					'after'=>$afterdata,
				);
				return $datas;
			}
		}
		elseif($sendtype=='yesterday' || $sendtype=='beforday')
		{
			return $data;
		}
		else
		{
			if($type=='useday'){return $data;}
			if($type=='useweek'){$newdata = array_chunk($data,7);}
			if($type=='usemonth'){$newdata = array_chunk($data,30);}
			return $this->getDataInfo($newdata);
		}
	}
	//时间切片
	public function getDataInfo($newdata)
	{
		foreach ($newdata as $keys => $values)
		{
			foreach ($values as $key => $value)
			{
				foreach ($value as $k => $v)
				{
					$datas[$keys][$k] += $v;
				}
			}
			$datas[$keys]['shop_id'] =end($values)['shop_id'];
			$datas[$keys]['createtime'] =end($values)['createtime'];
		}

		return $datas;
	}


}

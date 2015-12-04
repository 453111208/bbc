<?php
class sysstat_mdl_analysis_shopsale extends dbeav_model
{

	public function get_order($filter=null)
	{
		//订单成交量、订单成交额
        return app::get('systrade')->database()->createQueryBuilder()
                                   ->select('count(1) as saleTimes,sum(payment) as salePrice ,shop_id as shopname')
                                   ->from('systrade_trade')
                                   ->where($this->_filter($filter))
                                   ->execute()->fetch();
	}

	public function get_sale_num($filter=null)
	{
        $qb = app::get('systrade')->database()->createQueryBuilder();
        return $qb->select('sum(I.num) as sale_num')
                  ->from('systrade_trade', 'O')
                  ->leftJoin('O', 'systrade_order', 'I', 'O.tid=I.tid')
                  ->where($this->_filter($filter)?:1)
                  ->execute()->fetchColumn();
	}

	public function count($filter=null)
	{
		$filter['time_from'] = strtotime(sprintf('%s 00:00:00', $filter['time_from']));
		$filter['time_to'] = strtotime(sprintf('%s 23:59:59', $filter['time_to']));
		$date_range = array();
		for($i=$filter['time_from']; $i<=$filter['time_to']; $i+=86400)
        {
			$date_range[] = date("Y-m-d", $i);
		}
		return count($date_range);
	}


	public function getlist($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null)
	{
		$filter['time_from'] = strtotime(sprintf('%s 00:00:00', $filter['time_from']));
		$filter['time_to'] = strtotime(sprintf('%s 23:59:59', $filter['time_to']));
		$date_range = array();
		for($i=$filter['time_from']; $i<=$filter['time_to']; $i+=86400){
			$date_range[] = date("Y-m-d", $i);
		}
		if($orderType == 'time desc'){
			$date_range = array_reverse($date_range);
		}
		if($limit > 0){
			$date_range = array_slice($date_range, $offset, $limit);
		}
        $db = app::get('ectools')->database();
        $analysis_info = $db->executeQuery('select * from ectools_analysis where service=?', ['sysstat_analysis_shopsale'])->fetch();

		if($analysis_info)
        {
            $qb = $db->createQueryBuilder();
            $qb->select('*')->from('ectools_analysis_logs')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->where('analysis_id='.$qb->createPositionalParameter($analysis_info['id']))
                ->andWhere('time>='.$qb->createPositionalParameter($filter['time_from']))
                ->andWhere('time<='.$qb->createPositionalParameter($filter['time_to']))
                ->andWhere('flag=0');
            if(isset($this->_params['type'])) $qb->andWhere('type='.$qb->createPositionalParameter($this->_params['type']));
            $rows = $qb->execute()->fetchAll();

			foreach($rows AS $row){
			$date = date('Y-m-d', $row['time']);
			$tmp[$date][$row['target']] = $row['value'];
			}
		}

		foreach($date_range AS $k=>$date){
			$data[$k] = array(
					'time' => $date,
					'saleTimes'=>($tmp[$date][1])?$tmp[$date][1]:0,
					'salePrice'=>($tmp[$date][2])?$tmp[$date][2]:0,
					//'shopname'=>1,
					//'refund_num'=>($tmp[$date][3])?$tmp[$date][3]:0,
					//'refund_ratio'=>($tmp[$date][4])?$tmp[$date][4]:0,
				);
		}

		$this->tidy_data($data, $cols);
		return $data;
	}


	public function _filter($filter,$qb = null)
	{
		if(isset($filter['time_from']) && $filter['time_from']){
			$where[] = ' created_time >='.$filter['time_from'];
		}
		if(isset($filter['time_to']) && $filter['time_to']){
			$where[] = ' created_time <'.$filter['time_to'];
		}
		if(isset($filter['status']) && $filter['status']){
			$where[] = ' status =\''.$filter['status'].'\'';
		}

		return implode($where,' AND ');
	}

	public function get_schema(){
		$schema = array (
			'columns' => array (
				'time' => array (
					'type' => 'varchar(200)',
					'pkey' => true,
					'label' => app::get('sysstat')->_('日期'),
					'width' => 130,
					'editable' => false,
					'in_list' => true,
					'default_in_list' => true,
					'realtype' => 'mediumint(8) unsigned',
				),
				'saleTimes' => array (
					'type' => 'number',
					'label' => app::get('sysstat')->_('订单成交量'),
					'width' => 75,
					'editable' => true,
					'filtertype' => 'normal',
					'filterdefault' => 'true',
					'in_list' => true,
					'is_title' => true,
					'default_in_list' => true,
					'realtype' => 'varchar(50)',
					'orderby' => false,
				),
				'salePrice' => array (
					'type' => 'money',
					'default' => 0,
					'required' => true,
					'label' => app::get('sysstat')->_('订单成交额'),
					'width' => 110,
					'editable' => false,
					'filtertype' => 'number',
					'in_list' => true,
					'default_in_list' => true,
					'realtype' => 'mediumint(8) unsigned',
					'orderby' => false,
				),

			/*	'refund_num' => array (
					'type' => 'number',
					'default' => 0,
					'label' => app::get('sysstat')->_('商品退换量'),
					'width' => 110,
					'editable' => false,
					'hidden' => true,
					'in_list' => true,
					'default_in_list' => true,
					'realtype' => 'mediumint(8) unsigned',
					'orderby' => false,
				),
				'refund_ratio' => array (
					'type' => 'number',
					'default' => 0,
					'label' => app::get('sysstat')->_('商品退换率'),
					'width' => 110,
					'editable' => false,
					'hidden' => true,
					'in_list' => true,
					'default_in_list' => true,
					'realtype' => 'mediumint(8) unsigned',
					'orderby' => false,
				),*/
			),
			'idColumn' => 'time',
			'in_list' => array (
				0 => 'time',
				1 => 'saleTimes',
				2 => 'salePrice',
				//3 => 'refund_num',
				//4 => 'refund_ratio',
			),
			'default_in_list' => array (
				0 => 'time',
				1 => 'saleTimes',
				2 => 'salePrice',
				//3 => 'refund_num',
				//4 => 'refund_ratio',
			),
		);
		return $schema;
	}
}

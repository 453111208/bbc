<?php
class sysstat_mdl_analysis_sale extends dbeav_model
{
	public function get_pay_money($filter=null)
	{
        $db = app::get('ectools')->database();
        $qb = $db->createQueryBuilder();

        return $qb->select('sum(P.money) as amount')
           ->from('ectools_payments', 'P')
           ->where($qb->expr()->andX(
               $qb->expr()->gte('P.payed_time', intval($filter['time_from'])),
               $qb->expr()->lte('P.payed_time', intval($filter['time_to'])),
               $qb->expr()->eq('P.status', $db->quote('success'))
           ))->execute()->fetchColumn();
	}

	public function searchOptions()
	{
		$columns = array();
		foreach($this->_columns() as $k=>$v){
			if(isset($v['searchtype']) && $v['searchtype']){
				$columns[$k] = $v['label'];
			}
		}
		$ext_columns = array(
			'payment_id'=>$this->app->_('支付单号'),
			'refund_id'=>$this->app->_('退款单号'),
		);

		return array_merge($columns, $ext_columns);
	}

	public function count($filter=null)
	{
		if(isset($filter['time_from']) && $filter['time_from']){
			$filter['time_from'] = strtotime($filter['time_from']);
			$filter['time_to'] = (strtotime($filter['time_to'])+86400);
		}

        $db = app::get('ectools')->database();
        $qb = $db->createQueryBuilder();
        $subQb = $db->createQueryBuilder();
        $qb->select('count(*) as _count')
           ->from('ectools_payments', 'P')
           ->where($qb->expr()->andX(
               $qb->expr()->eq('P.status', $db->quote('succ')),
               $qb->expr()->gte('P.payed_time', $db->quote($filter['time_from'], \PDO::PARAM_INT)),
               $qb->expr()->lte('P.payed_time', $db->quote($filter['time_to'], \PDO::PARAM_INT)),
               $this->_filter($filter, $qb)
           ));

        return $qb->execute()->fetchColumn();
	}

	public function getlist($cols='*', $filter=array(), $offset=0, $limit=-1, $orderBy=null)
	{
        $offset = (int)$offset<0 ? 0 : $offset;
        $limit = (int)$limit < 0 ? 100000 : $limit;

		if(isset($filter['time_from']) && $filter['time_from']){
			$filter['time_from'] = strtotime($filter['time_from']);
			$filter['time_to'] = (strtotime($filter['time_to'])+86400);
		}

        $db = app::get('ectools')->database();
        $qb = $db->createQueryBuilder();
        $qb->select('P.payed_time as order_time,P.money as order_amount')
            ->from('ectools_payments', 'P')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->where($qb->expr()->andX(
                $qb->expr()->gte('P.payed_time', $db->quote($filter['time_from'])),
                $qb->expr()->eq('status', $db->quote('succ')),
                $qb->expr()->lte('P.payed_time', $db->quote($filter['time_to'])),
                $this->_filter($filter, $qb)
            ));

        if ($orderBy)
        {
            $orderBy = is_array($orderBy) ? implode(' ', $orderBy) : $orderBy;
            array_map(function($o) use (&$qb){
                @list($sort, $order) = explode(' ', trim($o));
                $qb->addOrderBy($sort, $order);
            }, explode(',', $orderBy));
        }
        $rows = $qb->execute()->fetchAll();
		return $rows;
	}

	public function _filter($filter, $qb)
	{
		if(isset($filter['payment_id']) && $filter['payment_id']){
            $filter_sql = ' and bill_id LIKE '.$qb->getConnection()->quote($filter['payment_id'].'%');
		}elseif(isset($filter['refund_id']) && $filter['refund_id']){
			$filter_sql = ' and bill_id LIKE '.$qb->getConnection()->quote($filter['refund_id'].'%');
		}else{
			$filter_sql = '';
		}
		return $filter_sql;
	}

	public function get_schema(){
		$schema = array (
			'columns' => array (
				'rel_id' => array (
					'type' => 'bigint unsigned',
					'required' => true,
					'label' => app::get('sysstat')->_( '订单号'),
					'width' => 120,
					'default' => 0,
					'editable' => false,
					'realtype' => 'mediumint(8) unsigned',
				),
				'bill_type' => array (
					'type' =>
					array (
						'payments' =>  app::get('ectools')->_('付款单'),
						'refunds' =>  app::get('ectools')->_('退款单'),
					),
					'default' => 'payments',
					'required' => true,
					'label' => app::get('sysstat')->_( '单据类型'),
					'width' => 75,
					'editable' => false,
					'filtertype' => 'yes',
					'filterdefault' => true,
					'in_list' => true,
				),
				'bill_id' => array (
					//'type' => 'varchar(20)',
                    'type' => 'string',
                    'length' => 20,
					'required' => true,
					'label' =>  app::get('sysstat')->_('单号'),
					'width' => 110,
					'editable' => false,
					'filtertype' => 'yes',
					'filterdefault' => true,
					'in_list' => true,
					'default_in_list' => true,
				),
				'order_time' => array (
					'type' => 'time',
					'label' => app::get('sysstat')->_('时间'),
					'width' => 130,
					'editable' => false,
					'in_list' => true,
				),
				'order_amount' => array (
					'type' => 'money',
					'default' => '0',
					'required' => true,
					'label' =>app::get('sysstat')->_('金额'),
					'width' => 75,
					'editable' => false,
					'in_list' => true,
					'default_in_list' => true,
				),
			),
			'idColumn' => 'rel_id',
			'in_list' => array (
				0 => 'rel_id',
				1 => 'bill_type',
				2 => 'bill_id',
				3 => 'order_time',
				4 => 'order_amount',
			),
			'default_in_list' => array (
				0 => 'rel_id',
				1 => 'bill_type',
				2 => 'bill_id',
				3 => 'order_time',
				4 => 'order_amount',
			),
		);
		return $schema;
	}

}

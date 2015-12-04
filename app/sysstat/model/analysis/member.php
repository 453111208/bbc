<?php
class sysstat_mdl_analysis_member extends dbeav_model
{
	public function count($filter=null)
	{
        return app::get('sysuser')->model('account')->count($this->_filter($filter));
	}

	public function getlist($cols='*', $filter=array(), $offset=0, $limit=-1, $orderBy=null)
	{
        $offset = (int)$offset<0 ? 0 : $offset;
        $limit = (int)$limit < 0 ? 100000 : $limit;

        $db = app::get('systrade')->database();
        $qb = $db->createQueryBuilder();

        $qb->select('M.login_account as login_account, count(1) as saleTimes, sum(O.payment) as salePrice')
            ->from('systrade_trade', 'O')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->leftJoin('O', 'sysuser_account', 'M', 'O.user_id=M.user_id')
            ->where($this->_filter($filter))
            ->groupBy('M.user_id')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if ($orderBy)
        {
            $orderBy = is_array($orderBy) ? implode(' ', $orderBy) : $orderBy;
            array_map(function($o) use (&$qb){
                @list($sort, $order) = explode(' ', trim($o));
                $qb->addOrderBy($sort, $order);
            }, explode(',', $orderBy));
        }
        $stmt = $qb->execute();
        $rows = $stmt->fetchAll();

        foreach($rows as $key=>$val)
        {
			$rows[$key]['rownum'] = (string)($offset+$key+1);
		}
		return $rows;
	}

	public function _filter($filter,$tableAlias=null,$baseWhere=null)
	{
		$where = array(1);
		if(isset($filter['time_from']) && $filter['time_from']){
			$where[] = ' createtime >='.strtotime($filter['time_from']);
		}
		if(isset($filter['time_to']) && $filter['time_to']){
			$where[] = ' createtime <'.(strtotime($filter['time_to'])+86400);
		}
		if(isset($filter['login_account']) && $filter['login_account']){
			$where[] = ' login_account LIKE \'%'.$filter['login_account'].'%\'';
		}
		if(isset($filter['regtime_from']) && $filter['regtime_from'])
		{
			$where[] = 'createtime >='.strtotime($filter['regtime_from']);
		}
		if(isset($filter['regtime_to']) && $filter['regtime_to'])
		{
			$where[] = 'createtime <='.strtotime($filter['regtime_to']);
		}
		return implode($where,' AND ');
	}

	public function get_schema(){
		$schema = array (
			'columns' => array (
				'rownum' => array (
					'type' => 'number',
					'default' => 0,
					'label' => app::get('sysstat')->_('排名'),
					'width' => 110,
					'orderby' => false,
					'editable' => false,
					'hidden' => true,
					'in_list' => true,
					'default_in_list' => true,
					'realtype' => 'mediumint(8) unsigned',
				),
				'login_account' => array (
					'type' => 'varchar(200)',
					'sdfpath' => 'pam_account/user_id',
					'label' => app::get('sysstat')->_('会员名'),
					'width' => 210,
					'searchtype' => 'has',
					'editable' => false,
					'in_list' => true,
					'default_in_list' => true,
					'realtype' => 'mediumint(8) unsigned',
				),
				'saleTimes' => array (
					'type' => 'number',
					'label' => app::get('sysstat')->_('订单量'),
					'width' => 75,
					'sdfpath' => 'contact/name',
					'editable' => true,
					'filtertype' => 'normal',
					'filterdefault' => 'true',
					'in_list' => true,
					'is_title' => true,
					'default_in_list' => true,
					'realtype' => 'varchar(50)',
				),
				'salePrice' => array (
					'type' => 'money',
					'default' => 0,
					'required' => true,
					'sdfpath' => 'score/total',
					'label' => app::get('sysstat')->_('订单额'),
					'width' => 110,
					'editable' => false,
					'filtertype' => 'number',
					'in_list' => true,
					'default_in_list' => true,
					'realtype' => 'mediumint(8) unsigned',
				),
			),
			'idColumn' => 'login_account',
			'in_list' => array (
				0 => 'rownum',
				1 => 'login_account',
				2 => 'saleTimes',
				3 => 'salePrice',
			),
			'default_in_list' => array (
				0 => 'login_account',
				1 => 'saleTimes',
				2 => 'salePrice',
			),
		);
		return $schema;
	}
}

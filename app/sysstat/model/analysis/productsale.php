<?php
class sysstat_mdl_analysis_productsale extends dbeav_model
{
	public function count($filter=array())
	{
        $db = app::get('systrade')->database();
        $qb = $db->createQueryBuilder();
        $subQb = $db->createQueryBuilder();

        $subQb->select('I.item_id')
              ->from('systrade_trade', 'T')
              ->leftJoin('T', 'systrade_order', 'O', 'T.tid=O.tid')
              ->leftJoin('O', 'sysitem_item', 'I', 'O.item_id=I.item_id')
              ->where($qb->expr()->andX(
                  $qb->expr()->neq('T.status', $db->quote('WAIT_BUYER_PAY')),
                  $this->_filter($filter)
              ))
              ->groupBy('I.item_id');
        // todo: 子查询为临时方案.
        $qb->select('count(*) as _count')
           ->from('('.$subQb->getSql().')', 'tb');
        return $qb->execute()->fetchColumn();
	}

	public function getlist($cols='*', $filter=array(), $offset=0, $limit=-1, $orderBy=null)
    {
        $db = app::get('systrade')->database();
        $qb = $db->createQueryBuilder();
        //image_default_id,list_image
        $qb->select('I.item_id as rownum,I.title as pname,I.bn as bn,sum(O.num) as saleTimes,sum(O.payment) as salePrice,I.item_id,I.image_default_id,I.list_image as udfimg')
            ->from('systrade_trade', 'T')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->leftJoin('T', 'systrade_order', 'O', 'T.tid=O.tid')
            ->leftJoin('O', 'sysitem_item', 'I', 'O.item_id=I.item_id')
            ->where($qb->expr()->andX(
                $qb->expr()->neq('T.status', $db->quote('WAIT_BUYER_PAY')),
                $this->_filter($filter)
            ))
            ->groupBy('I.item_id');

        if ($orderBy)
        {
            $orderBy = is_array($orderBy) ? implode(' ', $orderBy) : $orderBy;
            array_map(function($o) use (&$qb){
                @list($sort, $order) = explode(' ', trim($o));
                $qb->addOrderBy($sort, $order);
            }, explode(',', $orderBy));
        }
        $rows = $qb->execute()->fetchAll();

		foreach($rows as $key=>$val){
			$rows[$key]['rownum'] = (string)($offset+$key+1);
			//$rows[$key]['thumbnail_pic'] = $image['thumbnail_pic'];

		}
		return $rows;
	}


	public function _filter($filter,$tableAlias=null,$baseWhere=null){
		$where = array(1);
		if(isset($filter['time_from']) && $filter['time_from']){
			$where[] = ' T.created_time >='.strtotime($filter['time_from']);
		}
		if(isset($filter['time_to']) && $filter['time_to']){
			$where[] = ' T.created_time <'.(strtotime($filter['time_to'])+86400);
		}
		if(isset($filter['pname']) && $filter['pname']){
			$where[] = ' I.name LIKE \'%'.$filter['pname'].'%\'';
		}
		if(isset($filter['bn']) && $filter['bn']){
			$where[] = ' I.bn LIKE \''.$filter['bn'].'%\'';
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
				'pname' => array (
					'type' => 'varchar(200)',
					'sdfpath' => 'pam_account/account_id',
					'label' => app::get('sysstat')->_('商品名称'),
					'width' => 210,
					'searchtype' => 'has',
					'editable' => false,
					'in_list' => true,
					'default_in_list' => true,
					'realtype' => 'mediumint(8) unsigned',
				),
				'bn' => array (
					'required' => true,
					'default' => 0,
					'label' => app::get('sysstat')->_('商品编号'),
					'sdfpath' => 'member_lv/member_group_id',
					'width' => 120,
					'searchtype' => 'has',
					'type' => 'varchar(200)',
					'editable' => true,
					'filtertype' => 'bool',
					'filterdefault' => 'true',
					'in_list' => true,
					'default_in_list' => true,
					'realtype' => 'mediumint(8) unsigned',
				),
				'saleTimes' => array (
					'type' => 'number',
					'label' => app::get('sysstat')->_('销售量'),
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
					'label' => app::get('sysstat')->_('销售额'),
					'width' => 110,
					'editable' => false,
					'filtertype' => 'number',
					'in_list' => true,
					'default_in_list' => true,
					'realtype' => 'mediumint(8) unsigned',
				),
				/*'refund_num' => array (
					'type' => 'varchar(200)',
					'sdfpath' => 'profile/gender',
					'default' => 1,
					'required' => true,
					'label' => app::get('sysstat')->_('退换货量'),
					'orderby' => false,
					'width' => 110,
					'editable' => true,
					'filtertype' => 'yes',
					'in_list' => true,
					'default_in_list' => true,
					'realtype' => 'enum(\'0\',\'1\')',
				),
				'refund_ratio' => array (
					'label' => app::get('sysstat')->_('退换货率'),
					'width' => 110,
					'type' => 'varchar(200)',
					'orderby' => false,
					'editable' => false,
					'filtertype' => 'time',
					'filterdefault' => true,
					'in_list' => true,
					'default_in_list' => true,
					'realtype' => 'int(10) unsigned',
				),*/
			),
			'idColumn' => 'pname',
			'in_list' => array (
				0 => 'rownum',
				1 => 'pname',
				2 => 'bn',
				3 => 'saleTimes',
				4 => 'salePrice',
				//5 => 'refund_num',
				//6 => 'refund_ratio',
			),
			'default_in_list' => array (
				0 => 'rownum',
				1 => 'pname',
				2 => 'bn',
				3 => 'saleTimes',
				4 => 'salePrice',
				//5 => 'refund_num',
				//6 => 'refund_ratio',
			),
		);
		return $schema;
	}

}

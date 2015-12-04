<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class sysstat_analysis_shopsale extends ectools_analysis_abstract implements ectools_analysis_interface
{
	public $logs_options = array(
		'1' => array(
			'name' => '订单成交量',
			'flag' => array(
				'0' => '全部',
				'1' => '已发货',
				'2' => '已付款',
				'3' => '已完成',
			),
			'memo' => '已发货订单数量',
			'icon' => 'application_add.gif',
		),
		'2' => array(
			'name' => '订单成交额',
			'flag' => array(
				'0' => '全部',
				'1' => '已发货',
				'2' => '已付款',
				'3' => '已完成',
			),
			'memo' => '已发货订单金额',
			'icon' => 'coins.gif',
		),

		/*'3' => array(
			'name' => '商品退换量',
			'flag' => array(),
			'memo' => '退换货商品件数',
			'icon' => 'money_delete.gif',
		),
		'4' => array(
			'name' => '商品退换率',
			'flag' => array(),
			'memo' => '退换货商品件数占商品销售量的比例',
			'icon' => 'application_key.gif',
		),*/
	);

	public function get_logs($time){
		$filter = array(
			'time_from' => $time,
			'time_to' => $time+86400,
		);
		$filterShip = array(
			'time_from' => $time,
			'time_to' => $time+86400,
			'status' => 'WAIT_BUYER_CONFIRM_GOODS',
		);
		$filterPay = array(
			'time_from' => $time,
			'time_to' => $time+86400,
			'status' => 'WAIT_SELLER_SEND_GOODS',
		);
		$filterComplete = array(
			'time_from' => $time,
			'time_to' => $time+86400,
			'status' => 'TRADE_FINISHED',
		);
		$shopsaleObj = app::get('sysstat')->model('analysis_shopsale');
		$order = $shopsaleObj->get_order($filter); //全部
		$saleTimes = $order['saleTimes']; //订单量
		$salePrice = $order['salePrice']; //订单额

		$orderShip = $shopsaleObj->get_order($filterShip); //已发货
		$shipTimes = $orderShip['saleTimes']; //订单量
		$shipPrice = $orderShip['salePrice']; //订单额

		$orderPay = $shopsaleObj->get_order($filterPay); //已支付
		$payTimes = $orderPay['saleTimes']; //订单量
		$payPrice = $orderPay['salePrice']; //订单额

		$orderComplete = $shopsaleObj->get_order($filterComplete); //已完成
		$completeTimes = $orderPay['saleTimes']; //订单量
		$completePrice = $orderPay['salePrice']; //订单额

		//$reship_num = $shopsaleObj->get_reship_num($filter); //商品退换量
		$sale_num = $shopsaleObj->get_sale_num($filter); //商品销售量
		//$refund_ratio = isset($sale_num)?number_format($reship_num/$sale_num,2):0; //商品退换率

		$result[] = array('type'=>0, 'target'=>1, 'flag'=>0, 'value'=>$saleTimes);
		$result[] = array('type'=>0, 'target'=>1, 'flag'=>1, 'value'=>$shipTimes);
		$result[] = array('type'=>0, 'target'=>1, 'flag'=>2, 'value'=>$payTimes);
		$result[] = array('type'=>0, 'target'=>1, 'flag'=>3, 'value'=>$completeTimes);
		$result[] = array('type'=>0, 'target'=>2, 'flag'=>0, 'value'=>$salePrice);
		$result[] = array('type'=>0, 'target'=>2, 'flag'=>1, 'value'=>$shipPrice);
		$result[] = array('type'=>0, 'target'=>2, 'flag'=>2, 'value'=>$payPrice);
		$result[] = array('type'=>0, 'target'=>2, 'flag'=>3, 'value'=>$completePrice);
		//$result[] = array('type'=>0, 'target'=>3, 'flag'=>0, 'value'=>$reship_num);
		//$result[] = array('type'=>0, 'target'=>4, 'flag'=>0, 'value'=>$refund_ratio);

		return $result;
	}

	public function ext_detail(&$detail){
		$filter = $this->_params;
		$filter['time_from'] = isset($filter['time_from'])?strtotime($filter['time_from']):'';
		$filter['time_to'] = isset($filter['time_to'])?(strtotime($filter['time_to'])+86400):'';

		$shopsaleObj = app::get('sysstat')->model('analysis_shopsale');
		//$reship_num = $shopsaleObj->get_reship_num($filter); //商品退换量
		$sale_num = $shopsaleObj->get_sale_num($filter); //商品销售量
		//$refund_ratio = isset($sale_num)?number_format($reship_num/$sale_num,2):0; //商品退换率

		$detail['订单成交额']['value'] = $detail['订单成交额']['value']?number_format($detail['订单成交额']['value'],2,"."," "):0;
		//$detail['商品退换率']['value'] = $refund_ratio;
	}

	public function finder()
	{
		return array(
			'model' => 'sysstat_mdl_analysis_shopsale',
			'params' => array(
			'actions'=>array(
					array(
						'label'=>app::get('sysstat')->_('生成报表'),
						'class'=>'export',
						'icon'=>'add.gif',
						'href' => '?app=importexport&ctl=admin_export&act=export_view&_params[app]=sysstat&_params[mdl]=sysstat_mdl_analysis_shopsale',
						'target'=>'{width:400,height:170,title:\''.app::get('sysstat')->_('生成报表').'\'}'),
				),
				'title'=>app::get('sysstat')->_('店铺销售概况'),
				'use_buildin_selectrow'=>false,
                'use_buildin_delete'=>false,
			),
		);
	}
}

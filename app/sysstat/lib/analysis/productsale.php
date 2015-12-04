<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysstat_analysis_productsale extends ectools_analysis_abstract implements ectools_analysis_interface
{
	public $detail_options = array(
		'hidden' => true,
	);
	public $graph_options = array(
		'hidden' => true,
	);
	public function finder()
	{
		return array(
			'model' => 'sysstat_mdl_analysis_productsale',
			'params' => array(
				'actions'=>array(
					array(
						'label'=>app::get('sysstat')->_('生成报表'),
						'class'=>'export',
						'icon'=>'add.gif',
						'href' => '?app=importexport&ctl=admin_export&act=export_view&_params[app]=sysstat&_params[mdl]=sysstat_mdl_analysis_productsale',
						'target'=>'{width:400,height:170,title:\''.app::get('sysstat')->_('生成报表').'\'}'),
				),
				'title'=>app::get('sysstat')->_('商品销售排行'),
				'use_buildin_selectrow'=>false,
                'use_buildin_delete'=>false,
			),
		);
	}

	public function rank(){
		$filter = $this->_params;
		$filter['time_from'] = isset($filter['time_from'])?$filter['time_from']:'';
		$filter['time_to'] = isset($filter['time_to'])?$filter['time_to']:'';

		$productObj =app::get('sysstat')->model('analysis_productsale');
		$numProducts = $productObj->getlist('*', $filter, 0, 5, 'saleTimes desc');
		$priceProducts = $productObj->getlist('*', $filter, 0, 5, 'salePrice desc');

		$pagedata['numProducts'] = $numProducts;
		$pagedata['priceProducts'] = $priceProducts;
		$imageDefault = app::get('image')->getConf('image.set');
		$pagedata['defaultImage'] = $imageDefault['S']['default_image'];
		$html = view::make('sysstat/admin/analysis/productsale.html', $pagedata)->render();

		$this->pagedata['rank_html'] = $html;
	}
}

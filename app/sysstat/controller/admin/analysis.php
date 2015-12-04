<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class  sysstat_ctl_admin_analysis extends desktop_controller
{
	public function index()
	{
		$html = kernel::single('sysstat_analysis_index',app::get('sysstat'))->set_service('sysstat_analysis_shopsale')->set_extra_view(array('ectools'=>'ectools/analysis/index_view.html'))->set_params($_POST)->fetch();
		$pagedata['_PAGE_CONTENT'] = $html;
		return $this->page('desktop/common/default.html', $pagedata);
	}

	public function sale()
	{
		return kernel::single('sysstat_analysis_sale',app::get('sysstat'))->set_params($_POST)->fetch();
	}

	/*public function advance()
	{
		kernel::single('sysstat_analysis_advance')->set_params($_POST)->display();
	}*/

	public function shopsale()
	{
		return kernel::single('sysstat_analysis_shopsale',app::get('sysstat'))->set_params($_POST)->fetch();
		//kernel::single('sysstat_analysis_shopsale')->set_extra_view(array('ectools'=>'ectools/analysis/shopsale.html'))->set_params($_POST)->display();
	}

	public function productsale()
	{
		return kernel::single('sysstat_analysis_productsale',app::get('sysstat'))->set_params($_POST)->fetch();
	}

	public function member()
	{
		return kernel::single('sysstat_analysis_member',app::get('sysstat'))->set_params($_POST)->fetch();

	}

}

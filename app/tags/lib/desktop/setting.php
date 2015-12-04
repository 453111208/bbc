<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class tags_desktop_setting{
    /**
	 * 获取桌面标签的链接
	 * @param string object_name
	 * @param string app id
	 * @param string target url
	 * @return null.
	 */
	public function gen_target_url($obj_name='',$app_id='',&$_tagediturl=''){
		if (!$obj_name || !$app_id) return '';
		
		if ($obj_name == 'goods')
			$_tagediturl = '?app=desktop&ctl=default&act=alertpages&nobuttion=1&goto='
					   . urlencode('?app=tags&ctl=admin_tags&act=index&nobuttion=1&type='.$obj_name.'&app_id='.$app_id);
	}
}

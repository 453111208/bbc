<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_supply_list(&$setting)
{
    	  $userMdlSupply = app::get("sysspfb")->model('supplyInfo');
	  $supplyList =  $userMdlSupply ->getList("*","");
	  $setting['supplyList'] = $supplyList;
	  $userMdlRequire = app::get("sysspfb")->model('requireInfo');
	  $requireList =  $userMdlRequire ->getList("*","");
   	  $setting['requireList'] = $requireList;
    	  return $setting;
}

?>

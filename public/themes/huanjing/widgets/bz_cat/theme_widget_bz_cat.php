<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_bz_cat(&$setting){
	$a=$setting["cat_id"];
	foreach ($a as $key => $value) {
		$setting["cat"][$key]["catinfo"]=app::get("syscategory")->model("cat")->getRow("*",array("cat_id"=>$value));
	}
	return $setting;
}
?>

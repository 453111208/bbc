<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_bz_item(&$setting)
{
    //$sprodreleaseMdl=app::get("sysshoppubt")->model("sprodrelease");
    $sprodreleaseSql = "select  a.* ,b.*,c.image_default_id from sysshoppubt_standard_item a
						left join  sysshoppubt_sprodrelease b on a.uniqid=b.uniqid
						left join sysitem_item c on a.item_id = c.item_id
						where b.is_through = 1 ORDER BY b.create_time desc LIMIT 3";
	$sprodreleaseList = app::get("base")->database()->executeQuery($sprodreleaseSql)->fetchAll();
	$setting["sprodreleaseList"] = $sprodreleaseList;
	//var_dump($sprodreleaseList);
	return $setting;
}

?>

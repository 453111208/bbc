<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_detailLeft_xgzx(&$setting)
{
    $articleids = "";
    foreach ($setting['articles'] as $key => $value) {
    	$articleids.=",".$value;
    }
    $articleids = substr($articleids,1);
    if($articleids){

    }else{
        $articleids=-1;
    }
    $sprodreleaseSql = "SELECT
			sysinfo_article_nodes.node_name,
			sysinfo_article.*
		FROM
			sysinfo_article 
		LEFT JOIN sysinfo_article_nodes ON sysinfo_article.node_id = sysinfo_article_nodes.node_id
		WHERE article_id IN (".$articleids.")";
    $sprodreleaseList = app::get("base")->database()->executeQuery($sprodreleaseSql)->fetchAll();    
    $setting['sprodreleaseList'] = $sprodreleaseList;
    return $setting;
}

?>

<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_articles_rmzx(&$setting)
{
    $sprodreleaseSql = "SELECT * FROM sysinfo_article where status=1 and towhere=1 ORDER BY click_rate DESC LIMIT 10";	
    $sprodreleaseList = app::get("base")->database()->executeQuery($sprodreleaseSql)->fetchAll();
    $setting['sprodreleaseList'] = $sprodreleaseList;
    return $setting;
}

?>

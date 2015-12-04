<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_ad_sub_items(&$setting){
    $rows = 'item_id,title,price,image_default_id';
    $objItem = kernel::single('sysitem_item_info');
    $setting['item'] = $objItem->getItemList($setting['item_select'], $rows);
    $setting['defaultImg'] = app::get('image')->getConf('image.set');
    return $setting;
}
?>

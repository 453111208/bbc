<?php
function theme_widget_ad_group(&$setting) {

    $objItem = kernel::single('sysitem_item_info');
    $rows = 'item_id,title,price,image_default_id';
    $setting['item'] = $objItem->getItemInfo(array('item_id'=>$setting['item_select']), $rows);
    $setting['defaultImg'] = app::get('image')->getConf('image.set');
    return $setting;
}

?>

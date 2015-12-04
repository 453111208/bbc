<?php
function theme_widget_item_recommendation(&$setting) {

    $itemId = $setting['item_select'];
    $objItem = kernel::single('sysitem_data_item');
    $rows = 'item_id,title,price,image_default_id';
    $setting['item'] = $objItem->getItemInfo($itemId, $rows);
    $setting['defaultImg'] = app::get('image')->getConf('image.set');
    return $setting;
}

?>

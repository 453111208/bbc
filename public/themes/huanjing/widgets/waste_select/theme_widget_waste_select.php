<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_waste_select(&$setting){
        $_return['first_sort']=$setting['first_sort'];
	$_return['sort_select']=$setting['sort'];
	
    return $_return;
}
?>

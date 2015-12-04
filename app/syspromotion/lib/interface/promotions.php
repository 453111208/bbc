<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
/**
 * 优惠方案接口
 */
interface syspromotion_interface_promotions
{
    // public function config($config=array());
    public function apply($cartData);
    // public function apply_order(&$object, &$config, &$cart_object=null);
    // public function getString();
    // public function setString($aData);
    // public function get_status();
}
?>

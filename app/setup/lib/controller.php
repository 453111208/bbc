<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class setup_controller extends base_routing_controller{

    function __construct(&$app) 
    {
        $compiler = view::getEngine()->getCompiler();
        $compiler->loadViewHelper(kernel::single('base_view_helper'));
        
    }//End Function
}

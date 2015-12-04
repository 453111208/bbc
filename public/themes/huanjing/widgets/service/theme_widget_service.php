<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_service(&$setting){
    $_return['subject1']=mb_substr($setting['subject1'], 0,5,'utf-8');
    $_return['subject2']=mb_substr($setting['subject2'], 0,5,'utf-8');
    $_return['ad_pic']=$setting['ad_pic'];
    $_return['ad_pic_link']=$setting['ad_pic_link'];
    
    return $_return;
}
?>



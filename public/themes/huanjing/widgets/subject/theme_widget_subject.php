<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_subject(&$setting){
    $_return['subject1']=mb_substr($setting['subject1'], 0,16,'utf-8');
    $_return['subject2']=mb_substr($setting['subject2'], 0,16,'utf-8');
    $_return['subject3']=mb_substr($setting['subject3'], 0,16,'utf-8');
    
    return $_return;
}
?>



<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_success_case(&$setting){
    foreach($setting['case_select'] as $key=>$value)
	{
        $literary_id =$value;
        $artList = app::get("sysplan")->model("literary")->getList("*",array('literary_id'=>$literary_id));
        $_return['case_select'][$key]["context"]=$artList[0]['context'];
        $_return['case_select'][$key]["title"]=$artList[0]['title'];
        $_return['case_select'][$key]["literary_logo"]=$artList[0]['literary_logo'];
        $_return['case_select'][$key]["abstract"]=$artList[0]['abstract'];
        $_return['case_select'][$key]["literary_id"]=$artList[0]['literary_id'];

        //var_dump($_return['case_select'][$key]);
    }
        
    return $_return;
}
?>

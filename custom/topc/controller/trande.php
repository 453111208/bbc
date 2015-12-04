<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_ctl_trande extends topc_controller {

	public function index(){
		$trandeid = intval(input::get('trande_id'));# code...
		
        	$trandeItem= app::get("sysshoppubt")->model("tender")->getRow("*",array("trande_id"=>$trandeid ));
        	$trandeItemList = app::get("sysshoppubt")->model("standard_item")->getList("*",array("uniqid"=>$trandeItem['uniqid'] ));
          
              $pagedata["requireItem"]=$trandeItem;
        	$pagedata["itemList"] = $trandeItemList;
        if( userAuth::check() )
        {
            $pagedata['nologin'] = 1;
        }
        	 $article_id = "122";
            $artList = app::get("syscontent")->model("article")->getList("*",array('article_id'=>$article_id));
            $pagedata["tender"]=$artList[0]['content'];
            
        	return $this->page('topc/trande/index.html', $pagedata);
	}
}
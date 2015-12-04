<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_ctl_gallery extends topc_controller {
	public function index()
	{
		$this->setLayoutFlag('list');
		$pagedata["list"]=array();

		$searchContent = $_GET['searchContent'];
		$pagedata["searchContent"] = $searchContent;
		$searchKey = "";
		if($searchContent){
			$searchKey = " AND sshop.shop_name LIKE '%".$searchContent."%' OR sshop.shop_descript LIKE '%".$searchContent."%' ";
		}

		$sellertype=$_GET['sellertype'];
		if($sellertype == 1){
			//回收处置企业
			$sellerParam = " AND sseller.seller_type=1";
		} elseif ($sellertype == 2) {
			//产废企业
			$sellerParam = " AND sseller.seller_type=2";
		} elseif ($sellertype == -1) {
			//第三方企业
			$sellerParam = " AND sshop.is_third=1";
		} else {
			//企业类型不限
			$sellerParam = "";
			$sellertype = 99;
		}
		$pagedata['sellertype'] = $sellertype;
		$sorttype=$_GET['sorttype'];
		if($sorttype == 1){
			//绿色排行
			$sortParam = " ORDER BY sep.green DESC";
		} elseif ($sorttype == 2) {
			//成交排行
			$sortParam = " ORDER BY t1.volume DESC";
		} else {
			$sortParam = "";
			$sorttype = -1;
		}
		$pagedata['sorttype'] = $sorttype;
		
		$cat_id=$_GET['catid'];
		if($cat_id>0){
			$catidParam = " AND cat_id LIKE '%".$cat_id."%' ";
		} else {
			$cat_id = -1;
		}
		$pagedata['catid'] = $cat_id;

		$pageNow=1;
		$pageSize=10;
		if(!empty($_GET['pageNow'])){
		    $pageNow=$_GET['pageNow'];
		}

		$shopAllListSql="SELECT sseller.seller_type, sep.green, sshop.shop_id, sshop.shop_name, sshop.shop_descript, sshop.seller_id, sshop.`status`, 
			sshop.shop_logo, sshop.shopuser_name, sshop.shop_area, sshop.is_third, GROUP_CONCAT(t2.cat_name) 'cat_name', GROUP_CONCAT(t2.cat_id) 'cat_id', t1.volume
			FROM sysshop_shop sshop
			LEFT JOIN sysshop_seller sseller ON sshop.seller_id = sseller.seller_id
			LEFT JOIN sysshop_enterprise sep ON sshop.shop_id = sep.shop_id
			LEFT JOIN 
			(SELECT shop_id, COUNT(shop_id) volume FROM sysshoppubt_tradeorder 
			WHERE state >= 1) t1 ON sshop.shop_id = t1.shop_id
			LEFT JOIN
			(SELECT ssrl.cat_id, ssrl.shop_id, sc.cat_name FROM sysshop_shop_rel_lv1cat ssrl
			LEFT JOIN syscategory_cat sc ON ssrl.cat_id = sc.cat_id) t2 ON sshop.shop_id = t2.shop_id
			WHERE 1=1".$sellerParam.$catidParam.$searchKey."
			GROUP BY sseller.seller_type, sep.green, sshop.shop_id, sshop.shop_name, sshop.shop_descript, sshop.seller_id,
			sshop.`status`, sshop.shop_logo, sshop.shopuser_name, sshop.shop_area, sshop.is_third, t1.volume".$sortParam;
		$shopAllList = app::get("base")->database()->executeQuery($shopAllListSql)->fetchAll();

		$shopListSql="SELECT sseller.seller_type, sep.green, sshop.shop_id, sshop.shop_name, sshop.shop_descript, sshop.seller_id, sshop.`status`, 
			sshop.shop_logo, sshop.shopuser_name, sshop.shop_area, sshop.is_third, GROUP_CONCAT(t2.cat_name) 'cat_name', GROUP_CONCAT(t2.cat_id) 'cat_id', t1.volume
			FROM sysshop_shop sshop
			LEFT JOIN sysshop_seller sseller ON sshop.seller_id = sseller.seller_id
			LEFT JOIN sysshop_enterprise sep ON sshop.shop_id = sep.shop_id
			LEFT JOIN 
			(SELECT shop_id, COUNT(shop_id) volume FROM sysshoppubt_tradeorder 
			WHERE state >= 1) t1 ON sshop.shop_id = t1.shop_id
			LEFT JOIN
			(SELECT ssrl.cat_id, ssrl.shop_id, sc.cat_name FROM sysshop_shop_rel_lv1cat ssrl
			LEFT JOIN syscategory_cat sc ON ssrl.cat_id = sc.cat_id) t2 ON sshop.shop_id = t2.shop_id
			WHERE 1=1".$sellerParam.$catidParam.$searchKey."
			GROUP BY sseller.seller_type, sep.green, sshop.shop_id, sshop.shop_name, sshop.shop_descript, sshop.seller_id,
			sshop.`status`, sshop.shop_logo, sshop.shopuser_name, sshop.shop_area, sshop.is_third, t1.volume".$sortParam." LIMIT ".($pageNow-1)*$pageSize.",".$pageSize."";
		$shopList = app::get("base")->database()->executeQuery($shopListSql)->fetchAll();

		$catList = app::get("syscategory")->model("cat")->getList("*",array("level"=>1));
		$pagedata['catList'] = $catList;

		$rowConut=  count($shopAllList);
		$pageCount=  ceil($rowConut/$pageSize);
		$pagedata['pageCount'] = $pageCount;
		$pagedata['pageNow'] = $pageNow;     
		$pagedata['shopAllList'] = $shopAllList;
		$pagedata['shopList'] = $shopList;

		$shopCatSql = "SELECT ssrl.shop_id, GROUP_CONCAT(ssrl.cat_id SEPARATOR ' ') 'cat_id', GROUP_CONCAT(sc.cat_name SEPARATOR ' ') 'cat_name' 
			FROM sysshop_shop_rel_lv1cat ssrl
			LEFT JOIN syscategory_cat sc ON ssrl.cat_id = sc.cat_id
			GROUP BY ssrl.shop_id";
		$shopCat = app::get("base")->database()->executeQuery($shopCatSql)->fetchAll();
		$pagedata['shopCat'] = $shopCat;
		
		return $this->page('topc/gallery/index.html', $pagedata);

	}
}
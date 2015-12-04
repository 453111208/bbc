<?php
class topc_ctl_member_configcat extends topc_ctl_member
{
    public function index() {
        $userInfo = userAuth::getUserInfo();
        $params["user_id"] = $userInfo["userId"];
        $shopInfo = app::get('topc')->rpcCall('shop.get.shopInfo', $params, 'buyer');
        $shopCatList = app::get("sysshop")->model("shop_rel_lv1cat")->getList("*", array("shop_id" => $shopInfo["shop_id"]));
        foreach ($shopCatList as $key => $value) {
            $catList = app::get("syscategory")->model('cat')->getRow("*", array("cat_id" => $value["cat_id"]));
            $catsecList = app::get("syscategory")->model('cat')->getRow("*", array("parent_id" => $value["cat_id"], "cat_name" => "其他"));
            $shopCatList[$key]["cat_name"] = $catList["cat_name"];
            if ($catsecList) {
                $catconfigList = app::get("syscategory")->model('cat')->getList("*", array("parent_id" => $catsecList["cat_id"], "belong" => $shopInfo["shop_id"]));
                
                $shopCatList[$key]["config"] = $catconfigList;
            }
        }
        $pagedata['catList'] = $shopCatList;
        $pagedata['action'] = 'topc_ctl_member_configcat@index';
        $this->action_view = "configcat/index.html";
        return $this->output($pagedata);
    }
    public function add() {
        $userInfo = userAuth::getUserInfo();
        $params["user_id"] = $userInfo["userId"];
        $shopInfo = app::get('topc')->rpcCall('shop.get.shopInfo', $params, 'buyer');
        $shopCatList = app::get("sysshop")->model("shop_rel_lv1cat")->getList("*", array("shop_id" => $shopInfo["shop_id"]));
        foreach ($shopCatList as $key => $value) {
            $catList = app::get("syscategory")->model('cat')->getRow("*", array("cat_id" => $value["cat_id"]));
            $shopCatList[$key]["cat_name"] = $catList["cat_name"];
        }
        $pagedata['catList'] = $shopCatList;
        $pagedata["shopInfo"] = $shopInfo;
        $pagedata['action'] = 'topc_ctl_member_configcat@index';
        $this->action_view = "configcat/configcat.html";
        return $this->output($pagedata);
    }
    
    public function save() {
        $userInfo = userAuth::getUserInfo();
        $params["user_id"] = $userInfo["userId"];
        $shopInfo = app::get('topc')->rpcCall('shop.get.shopInfo', $params, 'buyer');
        $catid = $_POST["parentcat"];
        $newcat = $_POST["lv3cat"];
        $secsortcat = app::get("syscategory")->model('cat')->getRow("*", array("parent_id" => $catid, "cat_name" => "其他"));
        if ($secsortcat) {
            try {
                if($this->check_cat($catid, $newcat, $shopInfo,$secsortcat)){
                   $this->saveconfig($catid, $newcat, $shopInfo,$secsortcat);
                }else{
                    $url=url::action("topc_ctl_member_configcat@index");
                    return $this->splash('error', $url, app::get('topc')->_('您已经提交，请勿重复操作'));
                }
            }
            catch(Exception $e){
                $msg = $e->getMessage();
                return $this->splash('error', null, $msg);
            }
        } 
        else {
        	$secodcat=array(
        		"parent_id"=>$catid,
        		"cat_name"=>"其他",
        		"cat_path"=>",".$catid.",",
        		 "level" => "2",
        		 "is_leaf" => true,
        		 "disabled" => false,
        		  "order_sort" => 999, 
        		  "modified_time" => time(), 
        		  "belong" => $shopInfo["shop_id"]
        		);
        app::get("syscategory")->model('cat')->save($secodcat);
        $secsortcat = app::get("syscategory")->model('cat')->getRow("*", array("parent_id" => $catid, "cat_name" => "其他"));
         try {
                if($this->check_cat($catid, $newcat, $shopInfo,$secsortcat)){
                   $this->saveconfig($catid, $newcat, $shopInfo,$secsortcat);
                }else{
                    $url=url::action("topc_ctl_member_configcat@index");
                    return $this->splash('error', $url, app::get('topc')->_('您已经提交，请勿重复操作'));
                }
            }
            catch(Exception $e){
                $msg = $e->getMessage();
                return $this->splash('error', null, $msg);
            }
        }
          $url=url::action("topc_ctl_member_configcat@index");
            return $this->splash('success', $url, app::get('topc')->_('保存成功'));
       
    }
    public function saveconfig($catid, $newcat, $shopInfo,$secsortcat){       
       
            $thirdinsert = array("parent_id" => $secsortcat["cat_id"], "cat_name" => $newcat, "cat_path" => "," . $catid . "," . $secsortcat["cat_id"] . ",", "level" => "3", "is_leaf" => true, "disabled" => false, "order_sort" => 999, "modified_time" => time(), "belong" => $shopInfo["shop_id"]);
            app::get("syscategory")->model('cat')->save($thirdinsert); 
    }

    public function check_cat($catid, $newcat, $shopInfo,$secsortcat)
    {
        $thirdcat= app::get("syscategory")->model('cat')->getRow("*", array("parent_id" => $secsortcat['cat_id'], "cat_name" =>$newcat,"belong"=>$shopInfo["shop_id"]));
        if($thirdcat){
            return false;
        }else{
            return true;
        }
    }
}
?>
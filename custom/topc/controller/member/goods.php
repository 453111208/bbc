<?php
class topc_ctl_member_goods extends topc_ctl_member
{
    
    //发布商品
    public function addGoods() {
        
        $rows = "*";
        $parent = "0";
        try {
            $userId = userAuth::id();
            $params["user_id"] = $userId;
            $shopInfo = app::get('topc')->rpcCall('shop.get.shopInfo', $params, 'buyer');
            $shopCatList = app::get("sysshop")->model("shop_rel_lv1cat")->getList("*", array("shop_id" => $shopInfo["shop_id"]));
            foreach ($shopCatList as $key => $value) {
                $catList = app::get("syscategory")->model('cat')->getRow("*", array("cat_id" => $value["cat_id"]));
                $shopCatList[$key]["cat_name"] = $catList["cat_name"];

            }

        }
        catch(Exception $e) {
            $msg = $e->getMessage();
            return $this->splash('error', null, $msg);
        }
        catch(\LogicException $e) {
            $msg = $e->getMessage();
            return $this->splash('error', null, $msg);
        }
        $pagedata['action'] = 'topc_ctl_member_goods@addGoods';
        $pagedata['catList'] = $shopCatList;
        $this->action_view = "goods/addGoods.html";
        return $this->output($pagedata);
    }
    
    public function getOption() {
        $postCartId = input::get('parent');
        $level = input::get('level');
        try {
            
            $userMdlcat = app::get("syscategory")->model('cat');
            $catList = $userMdlcat->getList("*", array('parent_id' => $postCartId));
            $count = "1";
        }
        catch(Exception $e) {
            $msg = $e->getMessage();
            return $this->splash('error', null, $msg);
        }
        catch(\LogicException $e) {
            $msg = $e->getMessage();
            return $this->splash('error', null, $msg);
        }
        $ajaxdata = array('datas' => $catList);
        return response::json($ajaxdata);
    }
    
    public function getProp() {
        $postCartId = input::get('catId');
        try {
            
            $userMdlProp = app::get("syscategory")->model('cat_rel_prop');
            $propList = $userMdlProp->getList("*", array('cat_id' => $postCartId));
            $pagedata = array();
            foreach ($propList as $prop) {
                // code...
                $propId = $prop["prop_id"];
                $propModel = app::get("syscategory")->model('prop_values');
                $propValueList = $propModel->getList("*", array('prop_id' => $propId));
                foreach ($propValueList as $propvalue) {
                    $pagedata[] = array("prop_value_id" => $propvalue["prop_value_id"], "prop_value" => $propvalue["prop_value"]);
                }
                
                $a = 1;
            }
        }
        catch(Exception $e) {
            $msg = $e->getMessage();
            return $this->splash('error', null, $msg);
        }
        catch(\LogicException $e) {
            $msg = $e->getMessage();
            return $this->splash('error', null, $msg);
        }
        $ajaxdata = array('datas' => $pagedata);
        return response::json($ajaxdata);
    }
    
    // public function save(){
    //     $userId = userAuth::id();
    //      $postData =utils::_filter_input(input::get());
    //     $postData['user_id'] = $userId;
    //      $postData['modified_time']=time();
    //     $postData['state'] = 0;
    //     $postData['otherstate'] = 0;
    //       $postData['item_numberro'] = $_POST["item_numberro"];
    
    //      try
    // {
    //     $userMdlAddr = app::get('sysspfb')->model('item');
    //     $userMdlAddr->save($postData);
    // }
    // catch(Exception $e)
    // {
    //     $msg = $e->getMessage();
    
    //     return $this->splash('error',null,$msg);
    // }
    // catch(\LogicException $e)
    // {
    //     $msg = $e->getMessage();
    
    //     return $this->splash('error',null,$msg);
    // }
    
    //     $url = url::action('topc_ctl_member_goods@save');
    //     $msg = app::get('topc')->_('添加成功');
    //     return $this->splash('success',$url,$msg);
    // }
    public function sGoodsData() {
        $postData = input::get();
        $sysitem = app::get("sysitem")->model('item');
        $cat_id = $postData['cat_id'];
        $filter['cat_id'] = $cat_id;
        $userId = userAuth::id();
        $params["user_id"] = $userId;
        $shopInfo = app::get('topc')->rpcCall('shop.get.shopInfo', $params, 'buyer');
        $rows = $sysitem->getList('*,item_id,shop_id', array("cat_id" => $cat_id,"shop_id"=>$shopInfo["shop_id"]));
        $pagedata['rows'] = $rows;
        $a = $rows[0];
        $msg = view::make('topc/member/shoppubt/gooditem.html', $pagedata)->render();
        return $this->splash('success', null, $msg, true);
    }
}

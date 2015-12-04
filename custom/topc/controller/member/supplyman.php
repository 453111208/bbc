<?php
class topc_ctl_member_supplyman extends topc_ctl_member {
//供求管理




//我的供应信息
   public function needgoods()
	{
        $userInfo = userAuth::getUserInfo();
        $userId = userAuth::id();
        $rows = "*";
        try
            {
                $userMdlAddr = app::get('sysspfb')->model('supplyInfo');
                $goosList =$userMdlAddr->getList($rows,array('user_id'=>$userId));
                $count = $userMdlAddr->count(array('user_id'=>$userId));
                $params["user_id"]=$userId;
                $sellertype=app::get('topc')->rpcCall('seller.get.sellertype',$params,'buyer');
                $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
                //$gongying=$shopInfo["gongying_count"];

                
            }
         catch(Exception $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
            catch(\LogicException $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
        $pagedata['goosList'] = $goosList;
        $pagedata['goodCounts'] = $count;
        $pagedata['shopInfo']=$shopInfo;
        $pagedata['sellertype'] = $sellertype;
      
        $pagedata['action'] = 'topc_ctl_member_supplyman@needgoods';
        $this->action_view = "supplyman/needgoods.html";
        return $this->output($pagedata);
	}

	    /**
    * 供应编辑
    **/
 
        public function editGoods()
    {
             $rows = "*";
             $parent="0";
             //$userId = userAuth::id();
       // $params['user_id'] = userAuth::id();
         try
            {
                $userId =  userAuth::id();
                $params["user_id"]=$userId;
                $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
                $shop_id=$shopInfo["shop_id"];
                $shopCatList = app::get("sysshop")->model("shop_rel_lv1cat")->getList("*",array("shop_id"=>$shop_id));
                $catList = array();
                foreach ($shopCatList as $key => $value) {
                $catRow =  app::get("syscategory")->model('cat')->getRow("*",array("cat_id"=>$value["cat_id"]));
                $catList[$key]= $catRow ;
            }
            }
         catch(Exception $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
            catch(\LogicException $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
        $pagedata['catList'] = $catList;
        $pagedata['action'] = 'topc_ctl_member_supplyman@editGoods';
        $pagedata['license'] = app::get('sysuser')->getConf('sysuser.register.setting_reqsupp_license');
        
        //编辑信息初始化
        $supply_id = input::get('supply_id');
        if($supply_id != null && $supply_id != ""){
            $supplyinfo = app::get("sysspfb")->model("supplyInfo")->getRow("*",array("supply_id"=>$supply_id));
            $pagedata['supplyinfo'] = $supplyinfo;
            $listimage = explode(',',$supplyinfo['list_image']);
            array_pop($listimage);
            $pagedata['listimage'] = $listimage;
        }

        $this->action_view = "supplyman/editGoods.html";
        return $this->output($pagedata);
    }
      /**
    * 供应保存
    **/
        public function SaveGoods(){
            $userId = userAuth::id();
            $postData =utils::_filter_input(input::get());
            $supply_id = input::get("supply_id");
            if($postData['price_method'] == null || $postData['price_method'] === ""){
                return $this->splash('error',"","请填写价格方式");
            }
            if($postData['cat_id'] == null || $postData['cat_id'] === "" || $postData['cat_id'] === "null"){
                return $this->splash('error',"","请把所有类别填写完整");
            }
            if($postData['countnum'] == null || $postData['countnum'] === ""){
                unset($postData['countnum']);
            }
            $postData['user_id'] = $userId;
            
            $postData['create_time']=time();
            $postData['approve_stats'] = false;
            $postData['show_stats'] = false;
            $postData['product_intro'] = $_POST["product_intro"];
            $listimage = $postData['list_image'];
            $images = "";
            if(count($listimage)>0){
                $postData['image_default_id'] = $listimage[0];
            }
            foreach ($listimage as $key => $value) {
                $images.=($value.",");
            }
            $postData['list_image'] = $images;
            
            try
            {

                $userMdlAddr = app::get('sysspfb')->model('supplyInfo');
                //$goosList =$userMdlAddr->getList($rows,array('user_id'=>$userId));
                $count = $userMdlAddr->count(array('user_id'=>$userId));
                $params["user_id"]=$userId;
                $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
                if($count>=$shopInfo["gongying_count"]){
                    $url = url::action('topc_ctl_member_supplyman@needgoods');
                    $msg = app::get('topc')->_('您发布的供应数量已近超过平台方的规定额度，请联系平台方增加最大发布限额！');
                    return $this->splash('error',$url,$msg);
                }
                $userMdlAddr = app::get('sysspfb')->model('supplyInfo');
                $userMdlAddr->save($postData);
            }
            catch(Exception $e)
            {
                $msg = $e->getMessage();

                return $this->splash('error',null,$msg);
            }
            catch(\LogicException $e)
            {
                $msg = $e->getMessage();

                return $this->splash('error',null,$msg);
            }

            $url = url::action('topc_ctl_member_supplyman@needgoods');
            $msg = app::get('topc')->_('添加成功');
            return $this->splash('success',$url,$msg);
        }
//我的求购信息
	 public function wantgoods(){
                $userId = userAuth::id();
                $rows = "*";
                  try
            {
                $userMdlAddr = app::get('sysspfb')->model('requireInfo');
                $goosList =$userMdlAddr->getList($rows,array('user_id'=>$userId));
                $count = $userMdlAddr->count(array('user_id'=>$userId));
                $params["user_id"]=$userId;
                 $sellertype=app::get('topc')->rpcCall('seller.get.sellertype',$params,'buyer');
                $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
                
            }
         catch(Exception $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
            catch(\LogicException $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
                $pagedata['goosList'] = $goosList;
                $pagedata['goodCounts'] = $count;
                $pagedata['shopInfo']=$shopInfo;
                $pagedata['sellertype'] = $sellertype;
                $pagedata['action'] = 'topc_ctl_member_supplyman@wantgoods';
                $this->action_view = "supplyman/wantgoods.html";
                return $this->output($pagedata);
        }









     public function eidtRequireGoods()
    {
       // $params['user_id'] = userAuth::id();
            $rows = "*";
             $parent="0";
       // $params['user_id'] = userAuth::id();
         try
            {
               
                $userId =  userAuth::id();
                $params["user_id"]=$userId;
                $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
                $shop_id=$shopInfo["shop_id"];
                $shopCatList = app::get("sysshop")->model("shop_rel_lv1cat")->getList("*",array("shop_id"=>$shop_id));
                $catList = array();
                foreach($shopCatList as $key => $value){
                $catRow =  app::get("syscategory")->model('cat')->getRow("*",array("cat_id"=>$value["cat_id"]));
                $catList[$key]= $catRow ;
            }
       }
         catch(Exception $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
            catch(\LogicException $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
        $pagedata['catList'] = $catList;
        $pagedata['action'] = 'topc_ctl_member_supplyman@eidtRequireGoods';
        $pagedata['license'] = app::get('sysuser')->getConf('sysuser.register.setting_reqsupp_license');
        
        //编辑信息初始化
        $require_id = input::get('require_id');
        if($require_id != null && $require_id != ""){
            $requireinfo = app::get("sysspfb")->model("requireinfo")->getRow("*",array("require_id"=>$require_id));
            $pagedata['requireinfo'] = $requireinfo;
            $listimage = explode(',',$requireinfo['list_image']);
            array_pop($listimage);
            $pagedata['listimage'] = $listimage;
        }

        $this->action_view = "supplyman/eidtRequireGoods.html";
        return $this->output($pagedata);
    }

      public function SaveRequireGoods(){
            $userId = userAuth::id();
            $postData =utils::_filter_input(input::get());
            if($postData['price_method'] == null || $postData['price_method'] === ""){
                return $this->splash('error',"","请填写价格方式");
            }
            if($postData['cat_id'] == null || $postData['cat_id'] === "" || $postData['cat_id'] === "null"){
                return $this->splash('error',"","请把所有类别填写完整");
            }
            if($postData['countnum'] == null || $postData['countnum'] === ""){
                unset($postData['countnum']);
            }
            $postData['user_id'] = $userId;
            $postData['create_time']=time();
            $postData['approve_stats'] = false;
            $postData['show_stats'] = false;
            $postData['product_intro'] = $_POST["product_intro"];
            $listimage = $postData['list_image'];
            $images = "";
            if(count($listimage)>0){
                $postData['image_default_id'] = $listimage[0];
            }
            foreach ($listimage as $key => $value) {
                $images.=($value.",");
            }
            $postData['list_image'] = $images;
            try
            {

                $userMdlAddr = app::get('sysspfb')->model('requireInfo');
                $count = $userMdlAddr->count(array('user_id'=>$userId));
                $params["user_id"]=$userId;
                $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
                if($count>=$shopInfo["qiugou_count"]){
                    $url = url::action('topc_ctl_member_supplyman@wantgoods');
                    $msg = app::get('topc')->_('您发布的求购数量已近超过平台方的规定额度，请联系平台方增加最大发布限额！');
                    return $this->splash('error',$url,$msg);
                }
                $userMdlAddr->save($postData);
                $params["user_id"]=$userId;
                $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
                
            }
            catch(Exception $e)
            {
                $msg = $e->getMessage();

                return $this->splash('error',null,$msg);
            }
            catch(\LogicException $e)
            {
                $msg = $e->getMessage();

                return $this->splash('error',null,$msg);
            }

            $url = url::action('topc_ctl_member_supplyman@wantgoods');
            $msg = app::get('topc')->_('添加成功');
            return $this->splash('success',$url,$msg);
        }




}
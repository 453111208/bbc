<?php
class topc_ctl_member_shoppubt extends topc_ctl_member {

//我发布的交易 
    public function shoppubtList(){
        $pagedata['action'] = 'topc_ctl_member_shoppubt@shoppubtList';
        $this->action_view = "shoppubt/list.html";
        $userId = userAuth::id();
        return $this->output($pagedata);
    }

// 添加标准商品的发布交易
    public function addStandards(){
        $pagedata['uniqid']=uniqid();
        $userId = userAuth::id();
        $userMdlAddr = app::get('sysuser')->model('user_addrs');
        $userAddrList =$userMdlAddr->getList('*',array('user_id'=>$userId,'def_addr'=>1));
        if(!empty($userAddrList)){
            $userAddrList[0]['create_time']=  time();
            $userAddrList[0]['uniqid']= $pagedata['uniqid'];
            $addritem=app::get('sysshoppubt')->model('deliveryaddr');
            $addritem->save($userAddrList[0]);
            $pagedata['userAddrList'] = $userAddrList;
        }
        $pagedata['action'] = 'topc_ctl_member_shoppubt@shoppubtList';
        $this->action_view = "shoppubt/addStandards.html";
        return $this->output($pagedata);
    }

//添加竞价
    public function addBidding(){
        $pagedata['uniqid']=uniqid();
        $pagedata['action'] = 'topc_ctl_member_shoppubt@shoppubtList';
        $this->action_view = "shoppubt/bidding_or_tender.html";
        return $this->output($pagedata);
    }
//添加招标
    public function addTender(){
        $pagedata['uniqid']=uniqid();
        $pagedata['action'] = 'topc_ctl_member_shoppubt@shoppubtList';
        $this->action_view = "tender/tender_index.html";
        return $this->output($pagedata);
    }
 
//添加地址 dialog
	public function addr_dialog()
    {
        $pagedata['asd']='test';
        return view::make('topc/member/shoppubt/add_address.html',$pagedata);
    }

//选择商品分类 dialog
    public function sGodds()
    {
        $rows = "*";
        $parent="0";
        $userId = userAuth::id();

         try
            {
                $params["user_id"]=$userId;
                $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
                $shopCatList = app::get("sysshop")->model("shop_rel_lv1cat")->getList("*",array("shop_id"=>$shopInfo["shop_id"]));
                foreach ($shopCatList as $key => $value){
                $catList =  app::get("syscategory")->model('cat')->getRow("*",array("cat_id"=>$value["cat_id"]));
                $shopCatList[$key]["cat_name"] = $catList["cat_name"];
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
        $pagedata['action'] = 'topc_ctl_member_goods@addGoods';
        $pagedata['catList'] =$shopCatList;
        $a=$shopCatList[0];
        return view::make('topc/member/shoppubt/sGoods.html',$pagedata);
    }


//saveAddress
    public function  saveAddress(){
        $userId = userAuth::id();
        $postData = input::get();
        $addritem=app::get('sysshoppubt')->model('deliveryaddr');
        $postData['area'] = rtrim(input::get()['area'][0],',');
        $postData['user_id'] = $userId;
        $postData['create_time']=time();
        $area = app::get('topc')->rpcCall('logistics.area',array('area'=>$postData['area']));
        if($area)
        {
            $areaId =  str_replace(",","/", $postData['area']);
            $postData['area'] = $area . ':' . $areaId;
        }
        else
        {
            $msg = app::get('topc')->_('地区不存在!');
            return $this->splash('error',null,$msg);
        }
        try
        {  
       $filter = array('uniqid' => $postData['uniqid']);
        if($postData['def_addr'])
        {
            $arrUpdate = array('def_addr'=>0);
            $addritem->update($arrUpdate, $filter);
        }
        if( $postData['uniqid'])
        {
        $addritem->save($postData);
        }
        $filter1['uniqid']=$postData['uniqid'];
        $userAddrList=$addritem->getList('*',$filter1);
        foreach ($userAddrList as &$addr) {
            list($regions,$region_id) = explode(':', $addr['area']);
            $addr['region_id'] = str_replace('/', ',', $region_id);
        }
        $pagedata['userAddrList'] = $userAddrList;
        return view::make('topc/member/shoppubt/add_edit.html',$pagedata);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();

            return $this->splash('error',null,$msg);
        }
        
	}

    public function saveS(){
        $postData = input::get();
        $user_id = userAuth::id();
        switch ($postData['stop_time']) {
            case 'one':
                $postData['through_time']=strtotime("+1 month");
                break;
            case 'three':
                $postData['through_time']=strtotime("+3 month");
                break;  
            case 'six':
             $postData['through_time']=strtotime("+6 month");
                break;
            case 'december':
            $postData['through_time']=strtotime("+12 month");
                break;      
            case 'effective':
            $postData['through_time']=strtotime("+120 month");
                break;      
        }
        $arr['stop_time']=time();
        $arr['uniqid']=$postData['uniqid'];
        $arr['trading_title']=$postData['trading_title'];
        $arr['price_type']=$postData['price_type'];
        $arr['fund_trend']=$postData['fund_trend']; 
        $arr['create_time']=time();
        $params["user_id"]=$user_id;
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
                
        $arr['shop_id']=$shopInfo["shop_id"];
        $arr['shop_name']=$shopInfo["shop_name"];
        try
        {
        $saveItem = app::get('sysshoppubt')->model('sprodrelease');
        $saveItem->save($arr);

        $item_ids=$postData['item_id'];
        $units=$postData['unit'];
        $num=$postData['num'];
        $net_price=$postData['net_price'];
        $standardg_item_ids=$postData['standardg_item_id'];
        $i=0;
        $itemmodel = app::get('sysshoppubt')->model('standard_item');
        $db=app::get('sysshoppubt')->database();
        foreach ($item_ids as $key => $item_id) {
            $item=array();
            $sql="update sysshoppubt_standard_item set unit = '".$units[$i]."',num=".$num[$i].",net_price=".$net_price[$i]." where standardg_item_id = ".$standardg_item_ids[$i];
            $db->exec($sql);
        $i++;
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

        $url = url::action('topc_ctl_member_shoppubt@addStandards');
        $msg = app::get('topc')->_('添加成功');
        return $this->splash('success',$url,$msg);

    }
//xiugai
    public function tenderList(){
        $data = input::get('page');
        $data -= 1;
        $data *= 10;
        $tendermdl = app::get('sysshoppubt')->model('tender');
        $tenderenter = app::get('sysshoppubt')->model('tenderenter');
        $user_id=userAuth::id();
        $params["user_id"]=$user_id;
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        $shopinf = $tendermdl->getList('*',array('shop_id'=>$shopInfo["shop_id"]),$data,10);
        $countnum = $tendermdl->count(array('shop_id'=>$shopInfo['shop_id']));
        $countarr=array();
        if($countnum%10!=0){
        $countnum = $countnum/10;
        $countnum = intval($countnum)+1;
        }else{
        $countnum = $countnum/10;  
        }
        for ($i=0; $i < intval($countnum); $i++) { 
            $countarr[$i] = $i+1;
        }
        $pagedata['countnum'] = $countarr;
        $shopinfo = array();
        foreach ($shopinf as $key => $value) {
            $shopinfo[$key] = $value;
            $showme = $tenderenter->getList('*',array('tender_id'=>$value['tender_id'],'winornot'=>0));
            if($showme)$shopinfo[$key]['winornot']=0;
            else $shopinfo[$key]['winornot']=1;
            $count = $tenderenter->count(array('tender_id'=>$value['tender_id'],'openornot'=>0));
            $shopinfo[$key]['count'] = $count;
        }
        $pagedata['shopinfo'] = $shopinfo;
        $num = $tendermdl->count(array('shop_id'=> $shopInfo["shop_id"]));
        $pagedata['num'] = $num;
        $pagedata['action'] = 'topc_ctl_member_shoppubt@tenderList';
        $this->action_view = "tender/tenderlist.html";
        return $this->output($pagedata);
    }
    public function select(){
        $data = input::get();
        $datapage = input::get('page');
        $datapage -= 1;
        $datapage *= 10;
        $params["user_id"]=userAuth::id();
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        $tenderinfo = app::get('sysshoppubt')->model('tenderinfo');
        $tenderenter = app::get('sysshoppubt')->model('tenderenter');
        $tenderget = $tenderenter->getList('*',array('tender_id'=>$data['tender_id'],'openornot'=>0,'shop_id'=>$shopInfo['shop_id']),$datapage,10);
        $countnum = $tenderenter->count(array('tender_id'=>$data['tender_id'],'openornot'=>0,'shop_id'=>$shopInfo['shop_id']));
        $countarr=array();
        if($countnum%10!=0){
        $countnum = $countnum/10;
        $countnum = intval($countnum)+1;
        }else{
        $countnum = $countnum/10;  
        }
        for ($i=0; $i < intval($countnum); $i++) { 
            $countarr[$i] = $i+1;
        }
        $pagedata['countnum'] = $countarr;
        $num = $tenderenter->count(array('tender_id'=>$data['tender_id'],'openornot'=>0));
        $pagedata['num'] = $num;
        $pagedata['tenderget'] = $tenderget;
        $pagedata["tender_id"]=$data['tender_id'];
        $pagedata['action'] = 'topc_ctl_member_shoppubt@select';
        $this->action_view = "tender/select.html";
        return $this->output($pagedata);
    }
    public function win(){
        $item = app::get('sysitem')->model('item');
        $tender = app::get('sysshoppubt')->model('tender');
        $standartitem = app::get('sysshoppubt')->model('standard_item');
        $tenderenter = app::get('sysshoppubt')->model('tenderenter');
        $shopnotice = app::get('sysshop')->model('shop_notice');
        $data = input::get();
        $params["user_id"]=userAuth::id();
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        $real = $tenderenter->getList('winornot',array('winornot'=>0,'tender_id'=>$data['tender_id']));
        if (!$real) {
        $db=app::get('sysshoppubt')->database();
        $sql="update sysshoppubt_tenderenter set winornot = 1 where tender_id=".$data['tender_id'];
        $db->exec($sql);
        $tenderwin = $tenderenter->getRow('*',array('tender_id'=>$data['tender_id'],'tender_man_id'=>$data['tender_man_id']));
        $oldwin = $tenderwin;
        $tenderwin['winornot'] = 0;
        $tenderall = $tender->getRow('*',array('tender_id'=>$data['tender_id']));
        try {
        $result = $tenderenter->update($tenderwin,$oldwin);
        $sn['notice_title'] = "中标提示";
        $sn['notice_content'] = "恭喜您在对".$tenderall['shop_name']."的".$tenderall['trading_title']."招标中中标";
        $sn['notice_type'] = "招标";
        $sn['shop_id'] = $data['tender_man_id'];
        $sn['admin_id'] = $shopInfo['shop_id'];
        $sn['createtime'] = time();
        $sn['is_read'] = 0;
        $shopnotice->save($sn);
        } catch (Exception $e) {
        $msg = $e->getMessage();
        return $this->splash('error',null,$msg);
        }
        $tenderallold = $tenderall;
        $tenderall['isok'] = 1;
        try {
        $tenderisok = $tender->update($tenderall,$tenderallold);
        } catch (Exception $e) {
            $msg = $e->getMessage();
        return $this->splash('error',null,$msg);
        }
        if($result&&$tenderisok){
        $tenderinfo = $tender->getRow('*',array('tender_id'=>$data['tender_id']));
        $itemid = $standartitem->getRow('item_id',array('uniqid'=>$tenderinfo['uniqid']));
        $itemimg = $item->getRow('*',array('item_id'=>$itemid['item_id']));
        $img = split(',', $itemimg['list_image']);
        $notice = app::get('sysnotice')->model('notice_item');
        $arr['notice_name'] = $tenderwin['tender_title'];
        $arr['notice_content'] = "用户".$tenderwin['tender_man']."赢得".$tenderwin['shop_name']."的".$tenderwin['tender_title']."招标";
        $arr['notice_time'] = time();
        $arr['type_id'] = "招标";
        $arr['image_default_id'] = $img[0];
        try{
        $notice->save($arr);
        }catch(Exception $e){
        $msg = $e->getMessage();
        return $this->splash('error',null,$msg);
        }
        }
        return $result;
        }else return false;
        
    }
    public function detail(){
        $data = input::get();
        $tenderenter = app::get('sysshoppubt')->model('tenderenter');
        $chrule = app::get('sysshoppubt')->model('chrule');
        $tender = app::get('sysshoppubt')->model('tender');
        $tenderinfo = app::get('sysshoppubt')->model('tenderinfo');
        $getinfo = $tenderinfo->getList('*',array('tender_man_id'=>$data['tender_man_id'],'tender_id'=>$data['tender_id']));
        $money = $tenderenter->getList('*',array('tender_man_id'=>$data['tender_man_id'],'tender_id'=>$data['tender_id']));
        $pagedata['realmoney'] = $money[0]['price'];
        $tenderinfo = $tender->getRow('*',array('tender_id'=>$data['tender_id']));
        $rules = $chrule->getList('*',array('uniqid'=>$tenderinfo['uniqid']));
        foreach ($rules as $key => $value) {
            foreach ($getinfo as $key1 => $value1) {
                if($value1['chrule_id'] == $value['chrule_id']){
                $rules[$key]['data'] = $value1['data'];break;
            }else{
               $rules[$key]['data'] = null; 
            } 
            }
        }
        $pagedata['allrule'] = $rules;
        $pagedata['num'] = count($rules);
        $pagedata["tender_id"]=$data['tender_id'];
        $pagedata['tender_man_id'] = $data['tender_man_id'];
        return view::make('topc/member/tender/detail.html',$pagedata);
    }
    public function save(){
        $data = input::get();
        $tenderenter = app::get('sysshoppubt')->model('tenderenter');
        $get = $tenderenter->getRow('*',array('tender_man_id'=>$data['tender_man_id'],'tender_id'=>$data['tender_id']));
        $old = $get;
        $sums = 0;
        foreach ($data as $key => $value) {
            $s = split('_', $key);
            if($s[0] == 's'){
                $sums += $value;
            }
        }
        $get['score'] = $sums;
        $tenderenter->update($get,$old);
        $msg = app::get('topc')->_('操作成功');
        return $this->splash('success',null,$msg);
    }
    public function standardList(){
        $data = input::get('page');
        $data -= 1;
        $data *= 10;
        $sprodrelease = app::get('sysshoppubt')->model('sprodrelease');
        $tradeorder = app::get('sysshoppubt')->model('tradeorder');
        $params["user_id"]=userAuth::id();
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        $standard = $sprodrelease->getList('*',array('shop_id'=>$shopInfo['shop_id']),$data,10);
        $countnum = $sprodrelease->count(array('shop_id'=>$shopInfo['shop_id']));
        $countarr=array();
        if($countnum%10!=0){
        $countnum = $countnum/10;
        $countnum = intval($countnum)+1;
        }else{
        $countnum = $countnum/10;  
        }
        for ($i=0; $i < intval($countnum); $i++) { 
            $countarr[$i] = $i+1;
        }
        $pagedata['countnum'] = $countarr;
        $pagedata['allorder'] = $standard;
        
        $pagedata['action'] = 'topc_ctl_member_shoppubt@standardList';
        $this->action_view = "shoppubt/standardList.html";
        return $this->output($pagedata);
    }
}


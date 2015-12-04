<?php
class topc_ctl_member_biddings extends topc_ctl_member{

//添加竞价
    public function addBidding(){
        $pagedata['uniqid']=uniqid();
        $userId = userAuth::id();
        $userMdlAddr = app::get('sysuser')->model('user_addrs');
        $userAddrList =$userMdlAddr->getList('*',array('user_id'=>$userId,'def_addr'=>1));
        $userAddrList[0]['create_time']= time();
        $userAddrList[0]['uniqid']= $pagedata['uniqid'];
        $addritem=app::get('sysshoppubt')->model('deliveryaddr');
        $addritem->save($userAddrList[0]);
        $pagedata['userAddrList'] = $userAddrList;
        $params["user_id"]=$userId;
        $sellertype=app::get('topc')->rpcCall('seller.get.sellertype',$params,'buyer');
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        $pagedata['shopInfo']=$shopInfo;
        $pagedata['sellertype'] = $sellertype;
        $pagedata['action'] = 'topc_ctl_member_shoppubt@shoppubtList';
        $this->action_view = "biddings/bidding.html";
        return $this->output($pagedata);
    }


	 public function saveS(){
        $postData = input::get();
        $user_id = userAuth::id();
        $arr['uniqid']=$postData['uniqid'];
        $arr['trading_title']=$postData['trading_title'];
        
        
        $arr['start_time']=strtotime($postData['start_time']);
        $arr['stop_time']=strtotime($postData['stop_time']);
        $arr['price_type']=$postData['price_type'];
        $arr['advice']=$postData['advice']; 
        $arr['trade_type']=$postData['trade_type']; 
        $arr['add_price']=$postData['add_price']; 
        $arr['ensurence']=$postData['ensurence']; 
        $arr['fund_trend']=$postData['fund_trend'];
        $arr['public_item']=$postData['public_item'];
        $arr['limitation']=$postData['limitation']; 
        $arr['create_time']=time();
        $arr['desc']=$postData['desc'];
        $params["user_id"]=$user_id;
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        $arr['shop_id']=$shopInfo['shop_id'];
        $arr['shop_name']=$shopInfo["shop_name"];
        $arr["fixed_price"] = $postData["fixed_price"]?$postData["fixed_price"]:0;
        try
        {
        $saveItem = app::get('sysshoppubt')->model('biddings');
        $saveItem->save($arr);

        $item_ids=$postData['item_id'];
        $units=$postData['unit'];
        $num=$postData["num"];
        $standardg_item_ids=$postData['standardg_item_id'];
        $net_prices=$postData['net_price'];
        $fixed_prices=$postData['fixed_price'];
        $i=0;
        $itemmodel = app::get('sysshoppubt')->model('standard_item');
        $db=app::get('sysshoppubt')->database();
        foreach ($item_ids as $key => $item_id) {
            $item=array();
            $sql="update sysshoppubt_standard_item set unit = '".$units[$i]."' , net_price =".$net_prices[$i]." ,  fixed_price =".$fixed_prices[$i].",num=  ".$num[$i]." where standardg_item_id = ".$standardg_item_ids[$i];
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

        $url = url::action('topc_ctl_member_biddings@biddingList');
        $msg = app::get('topc')->_('添加成功');
        return $this->splash('success',$url,$msg);

    }

    public function biddingList(){
        $data = input::get('page');
        $data -= 1;
        $data *= 10;
        $biditem = app::get('sysshoppubt')->model('biddings');
        $standarditem = app::get('sysshoppubt')->model('standard_item');
        $params["user_id"]=userAuth::id();
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        $bidding = $biditem->getList('*',array('shop_id'=>$shopInfo['shop_id']),$data,10);
        $countnum = $biditem->count(array('shop_id'=>$shopInfo['shop_id']));
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
        foreach ($bidding as $key => $value) {
            $advicesum = $standarditem->getList('*',array('uniqid'=>$value['uniqid']));
            foreach ($advicesum as $key1 => $value1) {
                $sums += intval($value1['advice']);
            }
            $bidding[$key]['advicesum'] = $sums;
            $bidding[$key]['bidgoods'] = $advicesum;
            $sums = 0;
        }
        $pagedata['bidding'] = $bidding;
        $pagedata['action'] = 'topc_ctl_member_biddings@biddingList';
        $this->action_view = "biddings/biddinglist.html";
        return $this->output($pagedata);
    }
}
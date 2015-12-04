
<?php
class topc_ctl_member_tender extends topc_ctl_member{

public function addTender(){
        $pagedata['uniqid']=uniqid();
        $userId = userAuth::id();
        $userMdlAddr = app::get('sysuser')->model('user_addrs');
        $userAddrList =$userMdlAddr->getList('*',array('user_id'=>$userId,'def_addr'=>1));
        $userAddrList[0]['create_time']=  time();
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
        $this->action_view = "tender/tender_index.html";
        return $this->output($pagedata);
    }



     public function saveS(){
        if(!userAuth::id()){
            $msg = app::get('topc')->_('请先登录');
            return $this->splash('error',null,$msg);
        }
        $chrule = app::get('sysshoppubt')->model('chrule');
        $postData = input::get();
        $start = strtotime($postData['start_time'])+86399;
        $stop = strtotime($postData['stop_time'])+86399;
        $result = $chrule->getRow('*',array('uniqid'=>$postData['uniqid']));
        if($result){
        if($start<time()){
            $msg = app::get('topc')->_('开始时间不可小于当前时间');
            return $this->splash('error',null,$msg);
        }elseif($stop<$start){
            $msg = app::get('topc')->_('结束时间不可小于开始时间');
            return $this->splash('error',null,$msg);
        }
        $user_id = userAuth::id();
        $arr['uniqid']=$postData['uniqid'];
        $arr['trading_title']=$postData['trading_title'];
        // $arr['start_time']=$postData['start_time'];
        // $arr['stop_time']=$postData['stop_time'];
//时间
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
        $arr['text']=$postData['desc'];
        $arr['create_time']=time();
        $params["user_id"]=$user_id;
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        $arr['shop_name']=$shopInfo["shop_name"];
        $arr['shop_id']=$shopInfo["shop_id"];
        try
        {
        $saveItem = app::get('sysshoppubt')->model('tender');
        try {
            
            $saveItem->save($arr);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        $item_ids=$postData['item_id'];
        $units=$postData['unit'];
        $num=$postData["num"];
        $standardg_item_ids=$postData['standardg_item_id'];
        $net_prices=$postData['net_price'];
        //$fixed_prices=$postData['fixed_price'];
        $i=0;
        $itemmodel = app::get('sysshoppubt')->model('standard_item');
        $db=app::get('sysshoppubt')->database();
        foreach ($item_ids as $key => $item_id) {
            $item=array();
            $sql="update sysshoppubt_standard_item set unit = '".$units[$i]."' , net_price =".$net_prices[$i]." ,  num =".$num[$i]." where standardg_item_id = ".$standardg_item_ids[$i];
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

        $url = url::action('topc_ctl_member_shoppubt@tenderList');
        $msg = app::get('topc')->_('添加成功');
        return $this->splash('success',$url,$msg);
    }else{
        $msg = app::get('topc')->_('请填写评标规则表');
        return $this->splash('error',null,$msg);
    }
    }
}
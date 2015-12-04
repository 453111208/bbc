<?php
class topc_ctl_member_mytrade extends topc_ctl_member {

//我发布的交易
	public function mytrade()
	{
        $data = input::get('page');
        $data -= 1;
        $data *= 10;
		$tradeorder = app::get('sysshoppubt')->model('tradeorder');
		$tenderenter = app::get('sysshoppubt')->model('tenderenter');
		$user_id=userAuth::id();
		$params["user_id"]=$user_id;
    	$shopInfoGet=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
		$countnum1 = $tradeorder->count(array('user_id'=>$shopInfoGet['shop_id']));
        $countnum2 = $tenderenter->count(array('tender_man_id'=>$shopInfoGet['shop_id']));
        $data1 = $data-10;
        if($countnum1>=$data){
        	$otherorder = $tradeorder->getList('*',array('user_id'=>$shopInfoGet["shop_id"]),$data,10);
        }elseif($countnum1<=$data&&$countnum1>$data1){
    		$cha = intval($data) - intval($countnum1);
    		$getcha = intval($countnum1)+10-intval($data);
			$otherorder = $tradeorder->getList('*',array('user_id'=>$shopInfoGet["shop_id"]),$data,$getcha);
			$tenderorder = $tenderenter->getList('*',array('tender_man_id'=>$shopInfoGet["shop_id"]),0,$cha);
    	}elseif($countnum1<$data){
    		$cha = $data-$countnum1;
    		$cha = $cha/10;
    		$cha = intval($cha)*10;
    		$tenderorder = $tenderenter->getList('*',array('tender_man_id'=>$shopInfoGet["shop_id"]),$cha,10);
    	}
		
        $countarr=array();
        $countnum = intval($countnum1)+intval($countnum2);
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
		$pagedata['tenderorder'] = $tenderorder;
        $pagedata['allorder'] = $otherorder;

		$pagedata['action'] = 'topc_ctl_member_mytrade@mytrade';
        $this->action_view = "mytrade/mytrade.html";
        return $this->output($pagedata);
	}
}
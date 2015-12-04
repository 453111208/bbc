<?php
class topc_ctl_member_frame extends topc_ctl_member {
	public function add_index()
	{
		# code...
		$user_id = userAuth::id();
		$pagedata["user_id"]=$user_id;
		$params["user_id"]=$user_id;
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        if(!$shopInfo){
        	$pagedata["msg"]="请先完成企业入住";
        	$pagedata["success"]=false;
        }else{
        	$pagedata["msg"]="";
        	$pagedata["success"]=true;
        }
        $pagedata["type"]=1;
		$pagedata['action'] = 'topc_ctl_member_frame@add_index';
		$this->action_view = "frame/add_item_frame.html";
		return $this->output($pagedata);
		//return view::make("topc/member/frame/frame.html",$pagedata);
	}

	public function list_index()
	{
		$user_id = userAuth::id();
		$pagedata["user_id"]=$user_id;
		$params["user_id"]=$user_id;
		$shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
	        if(!$shopInfo){
	        	$pagedata["msg"]="请先完成企业入住";
	        	$pagedata["success"]=false;
	        }else{
	        	$pagedata["msg"]="";
	        	$pagedata["success"]=true;
	        }

		// $pagedata['src'] = url::action('topshop_ctl_item@list_index');
		$this->action_view = "frame/item_list_frame.html";
		return $this->output($pagedata);
		# code...
	}
}